<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\ThrottlesAttempts;
use App\Mail\Users\EmailVerificationMail;
use App\Models\UsersTable;
use App\Support\EmailThrottle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Component;

class LoginForm extends Component
{
    use ThrottlesAttempts;

    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;

    public bool $unverifiedEmail = false;

    /**
     * No length rule on login: the minimum belongs on registration (which
     * requires 8), and enforcing it here would lock out any account created
     * before that rule existed. A wrong-length password simply fails the hash
     * check like any other wrong password.
     */
    protected array $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];

    protected array $messages = [
        'email.required'    => 'Please enter your email address.',
        'email.email'       => 'Please enter a valid email address.',
        'password.required' => 'Please enter your password.',
    ];

    public function login()
    {
        $this->validate();

        // Livewire actions all POST to /livewire/update, so route-level
        // throttle middleware cannot see this form — limit in-component.
        if (! $this->passesRateLimit('realtor-login', $this->email, 5, 60)) {
            return;
        }

        $this->unverifiedEmail = false;

        $user = UsersTable::where('Email', $this->email)->first();

        if (! $user) {
            $this->addError('email', 'These credentials do not match our records.');
            return;
        }

        // getAttributes() reads the raw DB array — works regardless of column name case
        $attrs      = $user->getAttributes();
        $storedHash = $attrs['Password'] ?? $attrs['password'] ?? null;

        if (! $storedHash || ! Hash::check($this->password, $storedHash)) {
            $this->addError('email', 'These credentials do not match our records.');
            return;
        }

        if (! $user->email_verified_at) {
            $this->unverifiedEmail = true;
            $this->addError('email', 'Please verify your email address before signing in.');
            return;
        }

        if ($user->Status === 'pending') {
            $this->addError('email', 'Your account is pending admin approval. You will be notified once approved.');
            return;
        }

        if (in_array($user->Status, ['inactive', 'suspended'])) {
            $this->addError('email', 'Your account has been suspended. Contact support@realtorscircle.com.');
            return;
        }

        // Credentials were good — release the budget so a user who mistyped a
        // few times first is not left part-way locked out.
        $this->clearRateLimit('realtor-login', $this->email);

        Auth::login($user, $this->remember);

        session()->regenerate();

        $this->redirect(route('realtor.dashboard'));
    }

    public function resendVerification(): void
    {
        // This action mails whatever address is typed into the form, with no
        // authentication behind it — the most direct email-bombing surface in
        // the app. Two limits apply: how often this IP may ask, and (inside
        // EmailThrottle) how much mail the named address may receive at all.
        if (! $this->passesRateLimit('resend-verification', $this->email, 3, 3600, 'email', 10)) {
            return;
        }

        $user = UsersTable::where('Email', $this->email)->first();

        if ($user && ! $user->email_verified_at && EmailThrottle::allows($user->Email, 'verification')) {
            try {
                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addHours(48),
                    ['id' => $user->id, 'hash' => sha1($user->Email)]
                );

                Mail::to($user->Email)->send(new EmailVerificationMail($user, $verificationUrl));
            } catch (\Throwable $e) {
                Log::error('Resend verification email failed: ' . $e->getMessage());
            }
        }

        // Always show the same success message to avoid leaking whether the email exists
        $this->unverifiedEmail = false;
        session()->flash('resend_success', 'If that account needs verifying, a new link has been sent to ' . $this->email . '.');
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
