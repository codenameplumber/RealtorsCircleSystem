<?php

namespace Tests\Feature\Security;

use App\Models\UsersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * The API verification endpoint used to ignore its {token} segment entirely and
 * trust the ?email= query string, so anyone could verify any address they could
 * name. These tests pin the token down as the actual credential.
 */
class EmailVerificationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function makeUnverifiedUser(): UsersTable
    {
        return UsersTable::unguarded(fn () => UsersTable::create([
            'First_name'   => 'Vera',
            'Last_name'    => 'Fyer',
            'Email'        => 'victim@example.com',
            'Phone'        => '08010203040',
            'Password'     => Hash::make('password123'),
            'Status'       => 'pending',
            '2fa'          => 'disabled',
            'steps_signup' => 'step1_initiated',
            'email_verified_at' => null,
        ]));
    }

    public function test_verification_rejects_an_arbitrary_token(): void
    {
        $user = $this->makeUnverifiedUser();

        // The original bug, expressed directly: guess nothing, just name the
        // address and hand over any token you like.
        $this->getJson('/api/rc/v1/auth/verify-email/totally-made-up?email=' . urlencode($user->Email))
            ->assertStatus(400);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_verification_rejects_a_token_issued_for_a_different_user(): void
    {
        $victim = $this->makeUnverifiedUser();

        $other = UsersTable::unguarded(fn () => UsersTable::create([
            'First_name' => 'Other', 'Last_name' => 'User',
            'Email' => 'other@example.com', 'Phone' => '08011223344',
            'Password' => Hash::make('password123'), 'Status' => 'pending',
            '2fa' => 'disabled', 'steps_signup' => 'step1_initiated',
            'email_verified_at' => null,
        ]));

        $othersToken = $other->generateEmailVerificationToken();

        $this->getJson('/api/rc/v1/auth/verify-email/' . $othersToken . '?email=' . urlencode($victim->Email))
            ->assertStatus(400);

        $this->assertNull($victim->fresh()->email_verified_at);
    }

    public function test_verification_succeeds_with_the_issued_token(): void
    {
        $user  = $this->makeUnverifiedUser();
        $token = $user->generateEmailVerificationToken();

        $this->getJson('/api/rc/v1/auth/verify-email/' . $token . '?email=' . urlencode($user->Email))
            ->assertOk();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verification_token_is_single_use(): void
    {
        $user  = $this->makeUnverifiedUser();
        $token = $user->generateEmailVerificationToken();

        $this->getJson('/api/rc/v1/auth/verify-email/' . $token . '?email=' . urlencode($user->Email))
            ->assertOk();

        // Replaying the same link must not work a second time.
        $this->getJson('/api/rc/v1/auth/verify-email/' . $token . '?email=' . urlencode($user->Email))
            ->assertStatus(400);

        $this->assertNull($user->fresh()->email_verification_token);
    }

    public function test_verification_token_expires(): void
    {
        $user  = $this->makeUnverifiedUser();
        $token = $user->generateEmailVerificationToken(48);

        $this->travel(49)->hours();

        $this->getJson('/api/rc/v1/auth/verify-email/' . $token . '?email=' . urlencode($user->Email))
            ->assertStatus(400);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_only_the_token_hash_is_persisted(): void
    {
        $user  = $this->makeUnverifiedUser();
        $token = $user->generateEmailVerificationToken();

        $stored = $user->fresh()->email_verification_token;

        $this->assertNotSame($token, $stored, 'The plaintext token must never be stored.');
        $this->assertTrue(Hash::check($token, $stored));
    }
}
