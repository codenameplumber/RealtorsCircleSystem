<?php

namespace Tests\Feature\Security;

use App\Models\BankInformation;
use App\Models\UsersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiAuthHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function makeUser(array $overrides = []): UsersTable
    {
        return UsersTable::unguarded(fn () => UsersTable::create(array_merge([
            'First_name'   => 'Api',
            'Last_name'    => 'User',
            'Email'        => 'api.user@example.com',
            'Phone'        => '08070000000',
            'Password'     => Hash::make('password123'),
            'Status'       => 'active',
            '2fa'          => 'disabled',
            'steps_signup' => 'complete',
            'api_token'    => hash('sha256', 'seed-token'),
            'email_verified_at' => now(),
        ], $overrides)));
    }

    /**
     * $hidden listed a lowercase 'password' while the column is 'Password', so
     * the entry matched nothing and the bcrypt hash was serialised straight
     * into the login response.
     */
    public function test_login_response_never_contains_the_password_hash(): void
    {
        $this->makeUser();

        $response = $this->postJson('/api/rc/v1/auth/login', [
            'email'    => 'api.user@example.com',
            'password' => 'password123',
        ])->assertOk();

        $body = $response->getContent();

        $this->assertStringNotContainsString('$2y$', $body, 'A bcrypt hash was serialised into the response.');
        $response->assertJsonMissingPath('user.Password');
        $response->assertJsonMissingPath('user.password');
        $response->assertJsonMissingPath('user.api_token');
    }

    public function test_authenticated_user_endpoint_never_contains_the_password_hash(): void
    {
        $user  = $this->makeUser();
        $plain = Str::random(60);
        $user->forceFill(['api_token' => hash('sha256', $plain)])->save();

        $response = $this->getJson('/api/rc/v1/auth/user?api_token=' . $plain)->assertOk();

        $this->assertStringNotContainsString('$2y$', $response->getContent());
        $response->assertJsonMissingPath('Password');
        $response->assertJsonMissingPath('api_token');
    }

    public function test_unverified_account_cannot_obtain_an_api_token(): void
    {
        $this->makeUser(['email_verified_at' => null]);

        $this->postJson('/api/rc/v1/auth/login', [
            'email'    => 'api.user@example.com',
            'password' => 'password123',
        ])->assertStatus(403);
    }

    public function test_pending_account_cannot_obtain_an_api_token(): void
    {
        $this->makeUser(['Status' => 'pending']);

        $this->postJson('/api/rc/v1/auth/login', [
            'email'    => 'api.user@example.com',
            'password' => 'password123',
        ])->assertStatus(403);
    }

    /**
     * Carbon 3 returns a signed diff, so the old
     * `now()->diffInMinutes($created) > $expire` check was never true and reset
     * tokens lived forever.
     */
    public function test_password_reset_token_expires(): void
    {
        $user  = $this->makeUser();
        $token = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email'      => $user->Email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $expire = (int) config('auth.passwords.users.expire', 60);
        $this->travel($expire + 5)->minutes();

        $this->postJson('/api/rc/v1/auth/reset-password', [
            'email'                 => $user->Email,
            'token'                 => $token,
            'password'              => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])->assertStatus(400);

        // The old password must still be the live one.
        $this->assertTrue(Hash::check('password123', $user->fresh()->getAuthPassword()));
    }

    public function test_password_reset_token_works_inside_the_window(): void
    {
        $user  = $this->makeUser();
        $token = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email'      => $user->Email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $this->postJson('/api/rc/v1/auth/reset-password', [
            'email'                 => $user->Email,
            'token'                 => $token,
            'password'              => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])->assertOk();

        $this->assertTrue(Hash::check('brand-new-password', $user->fresh()->getAuthPassword()));
    }

    public function test_forgot_password_does_not_leak_whether_an_account_exists(): void
    {
        $this->makeUser();

        $known = $this->postJson('/api/rc/v1/auth/forgot-password', ['email' => 'api.user@example.com'])
            ->assertOk();

        $unknown = $this->postJson('/api/rc/v1/auth/forgot-password', ['email' => 'nobody@example.com'])
            ->assertOk();

        $this->assertSame($known->getContent(), $unknown->getContent());
    }

    /**
     * Completing the bank-details step used to set Status => 'active',
     * letting an API caller approve themselves and skip admin review.
     */
    public function test_completing_bank_details_does_not_self_activate_the_account(): void
    {
        $user  = $this->makeUser(['Status' => 'pending', 'steps_signup' => 'step2_completed']);
        $plain = Str::random(60);
        $user->forceFill(['api_token' => hash('sha256', $plain)])->save();

        $this->postJson('/api/rc/v1/auth/register/step3?api_token=' . $plain, [
            'Account_Name' => 'Api User',
            'Bvn'          => '12345678901',
            'Bank_Name'    => 'Test Bank',
            'Bank_Account' => '0123456789',
        ])->assertOk();

        $this->assertSame('pending', $user->fresh()->Status);
        $this->assertSame('registration_complete', $user->fresh()->steps_signup);
        $this->assertDatabaseCount(BankInformation::class, 1);
    }
}
