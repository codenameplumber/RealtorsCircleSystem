<?php

namespace Tests\Feature\Security;

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\LoginForm;
use App\Livewire\ContactForm;
use App\Models\AdminsTable;
use App\Models\UsersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Before this audit the application had no rate limiting of any kind: no
 * `throttle` middleware on any route and no named limiters. These tests cover
 * both delivery mechanisms — route middleware for real form/JSON posts, and
 * the in-component limiter for Livewire actions (which all POST to the same
 * /livewire/update endpoint and so are invisible to route middleware).
 */
class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The array cache store persists for the whole test process, so
        // limiter state would otherwise leak between test methods.
        Cache::flush();
        Mail::fake();
    }

    private function makeRealtor(): UsersTable
    {
        return UsersTable::unguarded(fn () => UsersTable::create([
            'First_name' => 'Rate', 'Last_name' => 'Limited',
            'Email' => 'rate@example.com', 'Phone' => '08090000000',
            'Password' => Hash::make('password123'), 'Status' => 'active',
            '2fa' => 'disabled', 'steps_signup' => 'complete',
            'email_verified_at' => now(),
        ]));
    }

    // -- Livewire-driven forms -------------------------------------------------

    public function test_realtor_login_locks_out_after_repeated_failures(): void
    {
        $this->makeRealtor();

        $component = Livewire::test(LoginForm::class)
            ->set('email', 'rate@example.com')
            ->set('password', 'wrong-password');

        for ($i = 0; $i < 5; $i++) {
            $component->call('login')->assertHasErrors('email');
        }

        $component->call('login');

        $this->assertStringContainsString(
            'Too many attempts',
            (string) $component->errors()->first('email'),
            'The 6th consecutive failure should be throttled, not just rejected.'
        );
    }

    public function test_a_successful_login_releases_the_lockout_budget(): void
    {
        $this->makeRealtor();

        $component = Livewire::test(LoginForm::class)->set('email', 'rate@example.com');

        // Three misses, then the right password.
        $component->set('password', 'wrong-password');
        for ($i = 0; $i < 3; $i++) {
            $component->call('login');
        }

        $component->set('password', 'password123')->call('login')->assertHasNoErrors();

        // A later wrong attempt should be a plain rejection, not a lockout,
        // because the budget was cleared on success.
        $fresh = Livewire::test(LoginForm::class)
            ->set('email', 'rate@example.com')
            ->set('password', 'wrong-password')
            ->call('login');

        $this->assertStringNotContainsString('Too many attempts', (string) $fresh->errors()->first('email'));
    }

    public function test_resend_verification_is_throttled(): void
    {
        UsersTable::unguarded(fn () => UsersTable::create([
            'First_name' => 'Un', 'Last_name' => 'Verified',
            'Email' => 'unverified@example.com', 'Phone' => '08090001111',
            'Password' => Hash::make('password123'), 'Status' => 'pending',
            '2fa' => 'disabled', 'steps_signup' => 'complete',
            'email_verified_at' => null,
        ]));

        $component = Livewire::test(LoginForm::class)->set('email', 'unverified@example.com');

        for ($i = 0; $i < 3; $i++) {
            $component->call('resendVerification');
        }

        $component->call('resendVerification');

        $this->assertStringContainsString('Too many attempts', (string) $component->errors()->first('email'));

        // Three requests got through the IP limiter; the per-recipient cap in
        // EmailThrottle means at most three mails were actually sent.
        Mail::assertSentCount(3);
    }

    public function test_forgot_password_is_throttled(): void
    {
        $this->makeRealtor();

        $component = Livewire::test(ForgotPassword::class);

        // The component blanks the field after each send, so a scripted caller
        // re-sets it every time; mirror that rather than relying on state.
        for ($i = 0; $i < 3; $i++) {
            $component->set('email', 'rate@example.com')->call('sendLink');
        }

        $component->set('email', 'rate@example.com')->call('sendLink');

        $this->assertStringContainsString('Too many attempts', (string) $component->errors()->first('email'));
    }

    /**
     * The per-address budget alone is sidestepped by rotating addresses, which
     * is exactly what a spam relay does. The per-IP ceiling has to catch it.
     */
    public function test_forgot_password_is_throttled_across_rotating_addresses(): void
    {
        $component = Livewire::test(ForgotPassword::class);

        for ($i = 0; $i < 10; $i++) {
            $component->set('email', "victim{$i}@example.com")->call('sendLink');
        }

        $component->set('email', 'victim-final@example.com')->call('sendLink');

        $this->assertStringContainsString(
            'Too many attempts',
            (string) $component->errors()->first('email'),
            'An address-rotating caller must still hit a per-IP ceiling.'
        );
    }

    public function test_contact_form_is_throttled(): void
    {
        $component = Livewire::test(ContactForm::class)
            ->set('name', 'Spam Bot')
            ->set('email', 'spam@example.com')
            ->set('subject', 'Hello there')
            ->set('body', str_repeat('spam text ', 5));

        for ($i = 0; $i < 5; $i++) {
            $component->set('name', 'Spam Bot')
                ->set('email', 'spam@example.com')
                ->set('subject', 'Hello there')
                ->set('body', str_repeat('spam text ', 5))
                ->call('send');
        }

        $component->set('name', 'Spam Bot')
            ->set('email', 'spam@example.com')
            ->set('subject', 'Hello there')
            ->set('body', str_repeat('spam text ', 5))
            ->call('send');

        $this->assertStringContainsString('Too many attempts', (string) $component->errors()->first('email'));
        $this->assertDatabaseCount('contact_messages', 5);
    }

    // -- Route middleware ------------------------------------------------------

    public function test_admin_login_route_is_throttled(): void
    {
        AdminsTable::create([
            'First_name' => 'Ada', 'Last_name' => 'Min',
            'Email' => 'admin@example.com', 'Phone' => '08090002222',
            'Password' => Hash::make('password123'), 'Role' => 'super_admin',
            'Status' => 'active',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', [
                'email' => 'admin@example.com', 'password' => 'wrong-password',
            ])->assertRedirect();
        }

        $this->post('/admin/login', [
            'email' => 'admin@example.com', 'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    public function test_api_login_route_is_throttled(): void
    {
        $this->makeRealtor();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/rc/v1/auth/login', [
                'email' => 'rate@example.com', 'password' => 'wrong-password',
            ])->assertStatus(401);
        }

        $this->postJson('/api/rc/v1/auth/login', [
            'email' => 'rate@example.com', 'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    public function test_api_forgot_password_route_is_throttled(): void
    {
        $this->makeRealtor();

        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/rc/v1/auth/forgot-password', ['email' => 'rate@example.com'])
                ->assertOk();
        }

        $this->postJson('/api/rc/v1/auth/forgot-password', ['email' => 'rate@example.com'])
            ->assertStatus(429);
    }

    public function test_whatsapp_api_is_throttled(): void
    {
        config(['services.whatsapp_api.key' => 'test-key']);

        $headers = ['X-API-Key' => 'test-key'];

        // The n8n surface has a deliberately high ceiling; prove it exists by
        // exhausting it rather than by assuming a low number.
        for ($i = 0; $i < 120; $i++) {
            $this->withHeaders($headers)->postJson('/api/wa/v1/realtor/identify', ['phone' => '08000000000']);
        }

        $this->withHeaders($headers)->postJson('/api/wa/v1/realtor/identify', ['phone' => '08000000000'])
            ->assertStatus(429);
    }
}
