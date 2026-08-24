<?php

namespace Tests\Feature;

use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;
use App\Mail\Users\EmailVerificationMail;
use App\Mail\WelcomeRealtor;
use App\Models\UsersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class RealtorAuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeRealtor(array $overrides = []): UsersTable
    {
        // api_token / email_verified_at are mass-assignment guarded on the
        // model; trusted test setup opts out rather than silently losing them.
        return UsersTable::unguarded(fn () => UsersTable::create(array_merge([
            'First_name'     => 'Test',
            'Last_name'      => 'Realtor',
            'Email'          => 'realtor@example.com',
            'Phone'          => '08010000000',
            'Password'       => Hash::make('password123'),
            'Status'         => 'active',
            '2fa'            => 'disabled',
            'steps_signup'   => 'complete',
            'api_token'      => hash('sha256', 'x'),
            'email_verified_at' => now(),
        ], $overrides)));
    }

    public function test_registration_creates_pending_unverified_user_and_sends_mails(): void
    {
        Mail::fake();

        Livewire::test(RegisterForm::class)
            ->set('first_name', 'Jane')
            ->set('last_name', 'Doe')
            ->set('email', 'jane@example.com')
            ->set('phone', '08011112222')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('specialization', 'Residential')
            ->set('years_experience', '2')
            ->set('terms_agreed', true)
            ->call('register');

        $user = UsersTable::where('Email', 'jane@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('pending', $user->Status);
        $this->assertNull($user->email_verified_at);

        Mail::assertSent(WelcomeRealtor::class);
        Mail::assertSent(EmailVerificationMail::class);
    }

    public function test_login_blocked_when_email_not_verified(): void
    {
        $this->makeRealtor(['email_verified_at' => null]);

        Livewire::test(LoginForm::class)
            ->set('email', 'realtor@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email')
            ->assertSet('unverifiedEmail', true);

        $this->assertGuest();
    }

    public function test_resend_verification_sends_mail_without_leaking_account_existence(): void
    {
        Mail::fake();
        $this->makeRealtor(['email_verified_at' => null]);

        Livewire::test(LoginForm::class)
            ->set('email', 'realtor@example.com')
            ->call('resendVerification');

        Mail::assertSent(EmailVerificationMail::class);
    }

    public function test_login_blocked_when_status_pending_even_if_verified(): void
    {
        $this->makeRealtor(['Status' => 'pending']);

        Livewire::test(LoginForm::class)
            ->set('email', 'realtor@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email')
            ->assertSet('unverifiedEmail', false);

        $this->assertGuest();
    }

    public function test_login_blocked_when_suspended(): void
    {
        $this->makeRealtor(['Status' => 'suspended']);

        Livewire::test(LoginForm::class)
            ->set('email', 'realtor@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_succeeds_when_active_and_verified(): void
    {
        $this->makeRealtor();

        Livewire::test(LoginForm::class)
            ->set('email', 'realtor@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors();

        $this->assertAuthenticated();
    }

    public function test_verification_link_marks_user_verified(): void
    {
        $user = $this->makeRealtor(['email_verified_at' => null]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(48),
            ['id' => $user->id, 'hash' => sha1($user->Email)]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('login'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verification_link_rejects_tampered_hash(): void
    {
        $user = $this->makeRealtor(['email_verified_at' => null]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(48),
            ['id' => $user->id, 'hash' => 'wrong-hash']
        );

        $response = $this->get($url);

        $response->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_dashboard_requires_auth(): void
    {
        $this->get('/realtor/dashboard')->assertRedirect('/login');
    }
}
