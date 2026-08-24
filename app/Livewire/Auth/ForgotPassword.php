<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\ThrottlesAttempts;
use App\Support\EmailThrottle;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    use ThrottlesAttempts;

    public string $email = '';
    public bool   $sent  = false;

    /**
     * Deliberately no `exists:` rule. Validating the address against the users
     * table made this form an account-enumeration oracle: "we could not find an
     * account with that email" confirmed which addresses are registered. Every
     * outcome below now looks identical to the visitor.
     */
    protected array $rules = [
        'email' => 'required|email',
    ];

    public function sendLink()
    {
        $this->validate();

        if (! $this->passesRateLimit('realtor-forgot-password', $this->email, 3, 3600, 'email', 10)) {
            return;
        }

        // The per-recipient budget is consumed here rather than inside the
        // notification so that a throttled request still reports success —
        // otherwise the throttle itself leaks whether the account exists.
        if (EmailThrottle::allows($this->email, 'password-reset')) {
            Password::sendResetLink(['email' => $this->email]);
        }

        $this->sent  = true;
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
