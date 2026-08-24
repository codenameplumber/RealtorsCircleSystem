<?php

namespace Tests\Feature\Security;

use App\Models\RealtorRating;
use App\Models\UsersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DataExposureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Mail::fake();
    }

    private function makeRealtor(array $overrides = []): UsersTable
    {
        return UsersTable::unguarded(fn () => UsersTable::create(array_merge([
            'First_name' => 'Data', 'Last_name' => 'Subject',
            'Email' => 'data@example.com', 'Phone' => '08055000000',
            'Password' => Hash::make('password123'), 'Status' => 'active',
            '2fa' => 'disabled', 'steps_signup' => 'complete',
            'email_verified_at' => now(),
        ], $overrides)));
    }

    // -- Mass assignment -------------------------------------------------------

    /**
     * These columns decide identity and privilege. They were fully
     * mass-assignable ($guarded = []), so a single careless
     * ->update($request->all()) anywhere would have been privilege escalation.
     */
    public function test_credential_columns_cannot_be_mass_assigned(): void
    {
        $user = $this->makeRealtor(['email_verified_at' => null]);

        $user->update([
            'api_token'         => 'attacker-chosen-token',
            'email_verified_at' => now(),
            'First_name'        => 'Legitimately Changed',
        ]);

        $fresh = $user->fresh();

        $this->assertNotSame('attacker-chosen-token', $fresh->api_token);
        $this->assertNull($fresh->email_verified_at);

        // Ordinary columns must still be writable, or the guard is too broad.
        $this->assertSame('Legitimately Changed', $fresh->First_name);
    }

    public function test_verification_token_columns_cannot_be_mass_assigned(): void
    {
        $user = $this->makeRealtor();

        $user->update([
            'email_verification_token'      => Hash::make('attacker-token'),
            'email_verification_expires_at' => now()->addYear(),
        ]);

        $this->assertNull($user->fresh()->email_verification_token);
    }

    // -- Rating abuse ----------------------------------------------------------

    public function test_a_visitor_cannot_rate_the_same_realtor_twice(): void
    {
        $realtor = $this->makeRealtor();

        $this->postJson('/api/rate-realtor', ['realtor_id' => $realtor->id, 'rating' => 5])
            ->assertOk()
            ->assertJsonPath('message', 'Rating saved. Thank you!');

        $this->postJson('/api/rate-realtor', ['realtor_id' => $realtor->id, 'rating' => 5])
            ->assertOk()
            ->assertJsonPath('message', 'You have already rated this realtor.');

        $this->assertSame(1, RealtorRating::where('realtor_id', $realtor->id)->count());
    }

    /**
     * Deduplication used to key on the session id alone, so clearing cookies
     * bought an unlimited supply of five-star ratings.
     */
    public function test_clearing_the_session_does_not_buy_another_rating(): void
    {
        $realtor = $this->makeRealtor();

        $this->postJson('/api/rate-realtor', ['realtor_id' => $realtor->id, 'rating' => 5])->assertOk();

        // A brand new session from the same client.
        $this->flushSession();
        $this->app['session']->driver()->regenerate(true);

        $this->postJson('/api/rate-realtor', ['realtor_id' => $realtor->id, 'rating' => 5])
            ->assertOk()
            ->assertJsonPath('message', 'You have already rated this realtor.');

        $this->assertSame(1, RealtorRating::where('realtor_id', $realtor->id)->count());
    }

    /**
     * Pins the mechanism rather than the symptom: a row whose session_token
     * could never match is still caught, so the IP fingerprint is doing the
     * work and the previous test is not passing by accident.
     */
    public function test_the_ip_fingerprint_is_what_catches_a_new_session(): void
    {
        $realtor = $this->makeRealtor();

        RealtorRating::create([
            'realtor_id'    => $realtor->id,
            'rating'        => 5,
            'session_token' => 'a-session-token-that-will-never-recur',
            'ip_hash'       => hash_hmac('sha256', '127.0.0.1_' . $realtor->id, config('app.key')),
        ]);

        $this->postJson('/api/rate-realtor', ['realtor_id' => $realtor->id, 'rating' => 1])
            ->assertOk()
            ->assertJsonPath('message', 'You have already rated this realtor.');

        $this->assertSame(1, RealtorRating::where('realtor_id', $realtor->id)->count());
    }

    public function test_ratings_are_rate_limited(): void
    {
        $realtors = collect(range(1, 20))->map(fn ($i) => $this->makeRealtor([
            'Email' => "r{$i}@example.com",
            'Phone' => '0805500' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
        ]));

        foreach ($realtors->take(15) as $realtor) {
            $this->postJson('/api/rate-realtor', ['realtor_id' => $realtor->id, 'rating' => 5])->assertOk();
        }

        $this->postJson('/api/rate-realtor', ['realtor_id' => $realtors->last()->id, 'rating' => 5])
            ->assertStatus(429);
    }

    public function test_pending_realtors_cannot_be_rated(): void
    {
        $realtor = $this->makeRealtor(['Status' => 'pending']);

        $this->postJson('/api/rate-realtor', ['realtor_id' => $realtor->id, 'rating' => 5])
            ->assertStatus(404);

        $this->assertSame(0, RealtorRating::where('realtor_id', $realtor->id)->count());
    }

    public function test_rating_fingerprints_are_not_serialised(): void
    {
        $realtor = $this->makeRealtor();

        $this->postJson('/api/rate-realtor', ['realtor_id' => $realtor->id, 'rating' => 4])->assertOk();

        $rating = RealtorRating::where('realtor_id', $realtor->id)->firstOrFail();

        $this->assertArrayNotHasKey('ip_hash', $rating->toArray());
        $this->assertArrayNotHasKey('session_token', $rating->toArray());
    }

    // -- KYC documents ---------------------------------------------------------

    /**
     * KYC scans were stored as permanent public S3 URLs and rendered straight
     * into <img src>. They must now be stored as opaque object keys and only
     * ever surfaced through an expiring presigned link.
     */
    public function test_kyc_upload_stores_an_object_key_not_a_public_url(): void
    {
        Storage::fake('kycaws');

        $user = $this->makeRealtor(['Status' => 'pending']);

        $url = URL::temporarySignedRoute('whatsapp.kyc.show', now()->addHour(), ['id' => $user->id]);

        $this->post($url, [
            'kyc_id_type' => 'National ID',
            'kyc_front'   => UploadedFile::fake()->image('front.jpg'),
        ])->assertOk();

        $stored = $user->fresh()->kyc_front_url;

        $this->assertNotNull($stored);
        $this->assertStringStartsNotWith('http', $stored, 'A public URL must not be persisted for a KYC document.');
        Storage::disk('kycaws')->assertExists($stored);
    }

    public function test_kyc_accessor_resolves_legacy_public_urls_to_object_keys(): void
    {
        // Rows written before the fix still hold a full public URL; the
        // accessor has to cope with both shapes without a data migration.
        $user = $this->makeRealtor([
            'kyc_front_url' => 'https://charsac-realtorscircle.s3.eu-north-1.amazonaws.com/kyc/front/legacy.jpg',
        ]);

        $method = new \ReflectionMethod($user, 'kycObjectKey');
        $method->setAccessible(true);

        $this->assertSame('front/legacy.jpg', $method->invoke($user, $user->kyc_front_url));
        $this->assertSame('front/new.jpg', $method->invoke($user, 'front/new.jpg'));
        $this->assertNull($method->invoke($user, null));
    }

    public function test_kyc_disk_is_configured_private(): void
    {
        $this->assertSame('private', config('filesystems.disks.kycaws.visibility'));
        $this->assertSame('https', config('filesystems.disks.kycaws.scheme'));
    }
}
