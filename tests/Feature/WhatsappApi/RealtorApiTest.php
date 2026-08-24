<?php

namespace Tests\Feature\WhatsappApi;

use App\Mail\Users\EmailVerificationMail;
use App\Mail\WelcomeRealtor;
use App\Models\AgencyPartner;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\PropertyInquiry;
use App\Models\UsersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RealtorApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->headers = ['X-API-Key' => config('services.whatsapp_api.key')];
    }

    private function makeRealtor(array $overrides = []): UsersTable
    {
        // api_token / email_verified_at are mass-assignment guarded on the
        // model; trusted test setup opts out rather than silently losing them.
        return UsersTable::unguarded(fn () => UsersTable::create(array_merge([
            'First_name' => 'Wa', 'Last_name' => 'Realtor',
            'Email' => 'wa.realtor@example.com', 'Phone' => '08011112222',
            'Password' => Hash::make('x'), 'Status' => 'active',
            '2fa' => 'disabled', 'steps_signup' => 'complete',
            'api_token' => hash('sha256', uniqid('', true)), 'email_verified_at' => now(),
            'State' => 'Lagos',
        ], $overrides)));
    }

    // ── Auth gate ─────────────────────────────────────────────────────────────

    public function test_request_without_api_key_is_rejected(): void
    {
        $this->postJson('/api/wa/v1/realtor/identify', ['phone' => '08011112222'])
            ->assertStatus(401);
    }

    public function test_request_with_wrong_api_key_is_rejected(): void
    {
        $this->withHeaders(['X-API-Key' => 'wrong-key'])
            ->postJson('/api/wa/v1/realtor/identify', ['phone' => '08011112222'])
            ->assertStatus(401);
    }

    // ── Register ──────────────────────────────────────────────────────────────

    public function test_register_creates_pending_realtor_and_returns_kyc_link(): void
    {
        Mail::fake();

        $response = $this->withHeaders($this->headers)->postJson('/api/wa/v1/realtor/register', [
            'first_name' => 'New', 'last_name' => 'Realtor',
            'email' => 'new.wa.realtor@example.com', 'phone' => '2348033334444',
            'specialization' => 'Residential',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['realtor_id', 'status', 'kyc_link']]);

        $realtor = UsersTable::where('Email', 'new.wa.realtor@example.com')->first();
        $this->assertNotNull($realtor);
        $this->assertSame('08033334444', $realtor->Phone); // international format normalized to local
        $this->assertSame('pending', $realtor->Status);

        Mail::assertSent(WelcomeRealtor::class);
        Mail::assertSent(EmailVerificationMail::class);

        $this->assertStringContainsString('/wa/kyc/' . $realtor->id, $response->json('data.kyc_link'));
    }

    public function test_register_rejects_duplicate_phone(): void
    {
        $this->makeRealtor();

        $this->withHeaders($this->headers)->postJson('/api/wa/v1/realtor/register', [
            'first_name' => 'Dup', 'last_name' => 'Realtor',
            'email' => 'dup@example.com', 'phone' => '08011112222',
        ])->assertStatus(422);
    }

    public function test_register_links_agency_via_referral_code(): void
    {
        $agency = AgencyPartner::create([
            'company_name' => 'Ref Agency', 'first_name' => 'A', 'last_name' => 'B',
            'email' => 'refagency@example.com', 'password' => Hash::make('x'),
            'referral_code' => 'REF123', 'status' => 'active',
        ]);

        $response = $this->withHeaders($this->headers)->postJson('/api/wa/v1/realtor/register', [
            'first_name' => 'Ref', 'last_name' => 'Realtor',
            'email' => 'ref.realtor@example.com', 'phone' => '08055556666',
            'referral_code' => 'REF123',
        ]);

        $response->assertStatus(201);
        $realtor = UsersTable::where('Email', 'ref.realtor@example.com')->first();
        $this->assertSame($agency->id, $realtor->agency_id);
    }

    // ── Identify ──────────────────────────────────────────────────────────────

    public function test_identify_returns_realtor_profile(): void
    {
        $this->makeRealtor();

        $this->withHeaders($this->headers)->postJson('/api/wa/v1/realtor/identify', ['phone' => '08011112222'])
            ->assertOk()
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.can_transact', true);
    }

    public function test_identify_normalizes_international_phone_format(): void
    {
        $this->makeRealtor();

        $this->withHeaders($this->headers)->postJson('/api/wa/v1/realtor/identify', ['phone' => '2348011112222'])
            ->assertOk()
            ->assertJsonPath('status', 'success');
    }

    public function test_identify_returns_404_for_unknown_phone(): void
    {
        $this->withHeaders($this->headers)->postJson('/api/wa/v1/realtor/identify', ['phone' => '08099999999'])
            ->assertStatus(404);
    }

    // ── Properties ────────────────────────────────────────────────────────────

    public function test_create_property_returns_photo_link(): void
    {
        $this->makeRealtor();

        $response = $this->withHeaders($this->headers)->postJson('/api/wa/v1/realtor/properties', [
            'phone' => '08011112222', 'title' => 'WA Test Property', 'type' => 'Flat',
            'description' => 'desc', 'listing_type' => 'sale', 'street' => '1 St',
            'neigborhood' => 'Area', 'city' => 'Lagos', 'state' => 'Lagos',
            'localarea' => 'Ikeja', 'price' => 1000000,
        ]);

        $response->assertStatus(201)->assertJsonStructure(['data' => ['property_id', 'photo_upload_link']]);

        $property = PropertiesTable::find($response->json('data.property_id'));
        $this->assertSame('pending', $property->approval_status);
        $this->assertStringContainsString('/wa/property-photos/' . $property->id, $response->json('data.photo_upload_link'));
    }

    public function test_create_property_blocked_for_pending_realtor(): void
    {
        $this->makeRealtor(['Status' => 'pending', 'Phone' => '08077778888']);

        $this->withHeaders($this->headers)->postJson('/api/wa/v1/realtor/properties', [
            'phone' => '08077778888', 'title' => 'X', 'type' => 'Flat', 'description' => 'd',
            'listing_type' => 'sale', 'street' => 's', 'neigborhood' => 'n', 'city' => 'c',
            'state' => 'Lagos', 'localarea' => 'l', 'price' => 1000,
        ])->assertStatus(403);
    }

    public function test_update_property_status_only_for_own_property(): void
    {
        $realtor  = $this->makeRealtor();
        $other    = $this->makeRealtor(['Phone' => '08022223333', 'Email' => 'other@example.com']);
        $property = $this->makeProperty($other);

        // Not the owner — should 404, not silently succeed
        $this->withHeaders($this->headers)->patchJson("/api/wa/v1/realtor/properties/{$property->id}/status", [
            'phone' => '08011112222', 'status' => 'sold',
        ])->assertStatus(404);

        $this->assertSame('active', $property->fresh()->listing_status);

        // Real owner — should succeed
        $this->withHeaders($this->headers)->patchJson("/api/wa/v1/realtor/properties/{$property->id}/status", [
            'phone' => '08022223333', 'status' => 'sold',
        ])->assertOk();

        $this->assertSame('sold', $property->fresh()->listing_status);
    }

    // ── Appointments ──────────────────────────────────────────────────────────

    public function test_appointments_lists_and_approve_reject_work(): void
    {
        $realtor  = $this->makeRealtor();
        $property = $this->makeProperty($realtor);
        $apt      = PropertyInquiry::create([
            'property_id' => $property->id, 'name' => 'Client', 'phone' => '08000000000',
            'preferred_date' => now()->addDay()->toDateString(), 'preferred_time' => '10:00',
            'status' => 'pending',
        ]);

        $this->withHeaders($this->headers)->getJson('/api/wa/v1/realtor/appointments?phone=08011112222')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->withHeaders($this->headers)->postJson("/api/wa/v1/realtor/appointments/{$apt->id}/approve", ['phone' => '08011112222'])
            ->assertOk();
        $this->assertSame('confirmed', $apt->fresh()->status);

        $apt2 = PropertyInquiry::create([
            'property_id' => $property->id, 'name' => 'Client2', 'phone' => '08000000001',
            'preferred_date' => now()->addDay()->toDateString(), 'preferred_time' => '11:00',
            'status' => 'pending',
        ]);
        $this->withHeaders($this->headers)->postJson("/api/wa/v1/realtor/appointments/{$apt2->id}/reject", [
            'phone' => '08011112222', 'reason' => 'Not available',
        ])->assertOk();
        $this->assertSame('cancelled', $apt2->fresh()->status);
    }

    // ── Search ────────────────────────────────────────────────────────────────

    public function test_search_only_returns_approved_active_properties(): void
    {
        $realtor = $this->makeRealtor();
        $this->makeProperty($realtor, ['title' => 'Approved Home', 'approval_status' => 'approved', 'listing_status' => 'active']);
        $this->makeProperty($realtor, ['title' => 'Pending Home', 'approval_status' => 'pending']);

        $response = $this->withHeaders($this->headers)->getJson('/api/wa/v1/realtor/search?q=Home');

        $response->assertOk();
        $titles = collect($response->json('data'))->pluck('title');
        $this->assertContains('Approved Home', $titles);
        $this->assertNotContains('Pending Home', $titles);
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

    public function test_stats_reflects_earned_commission(): void
    {
        $realtor  = $this->makeRealtor();
        $property = $this->makeProperty($realtor, ['approval_status' => 'approved']);

        CommissionTable::create([
            'property_id' => $property->id, 'listing_realtor_id' => $realtor->id,
            'total_amount' => 1000000, 'listing_realtor_amount' => 400000, 'status' => 'paid',
        ]);

        $response = $this->withHeaders($this->headers)->getJson('/api/wa/v1/realtor/stats?phone=08011112222');

        $response->assertOk()
            ->assertJsonPath('data.total_assets', 1);
        $this->assertEquals(400000, $response->json('data.total_earnings'));
    }

    private function makeProperty(UsersTable $realtor, array $overrides = []): PropertiesTable
    {
        return PropertiesTable::create(array_merge([
            'user_id' => $realtor->id, 'title' => 'Test Property', 'type' => 'Flat',
            'description' => 'desc', 'listing_type' => 'sale', 'street' => '1 St',
            'neigborhood' => 'Area', 'city' => 'Lagos', 'state' => 'Lagos',
            'localarea' => 'Ikeja', 'country' => 'Nigeria', 'amenities' => json_encode([]),
            'price' => '1000000', 'photos' => json_encode([]), 'videos' => json_encode([]),
            'listing_status' => 'active', 'approval_status' => 'pending', 'views' => '0',
        ], $overrides));
    }
}
