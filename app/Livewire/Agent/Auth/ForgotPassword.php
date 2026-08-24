<?php

namespace App\Livewire\Agent\Auth;

use App\Livewire\Concerns\ThrottlesAttempts;
use App\Mail\Agent\AgentPasswordResetMail;
use App\Models\AgentsTable;
use App\Support\EmailThrottle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ForgotPassword extends Component
{
    use ThrottlesAttempts;

    public string $email = '';
    public bool   $sent  = false;

    protected array $rules = [
        'email' => 'required|email',
    ];

    protected array $messages = [
        'email.required' => 'Email address is required.',
        'email.email'    => 'Please enter a valid email address.',
    ];

    public function send(): void
    {
        $this->validate();

        if (! $this->passesRateLimit('agent-forgot-password', $this->email, 3, 3600, 'email', 10)) {
            return;
        }

        try {
            $agent = AgentsTable::where('Email', $this->email)->first();

            // Always show success to prevent email enumeration. The recipient
            // budget is checked alongside the account lookup so that a
            // throttled request is indistinguishable from an unknown address.
            if ($agent && EmailThrottle::allows($agent->Email, 'password-reset')) {
                $token    = $agent->generateResetToken();
                $resetUrl = route('agent.reset-password.show', [
                    'token' => $token,
                    'email' => encrypt($agent->Email),
                ]);

                Mail::to($agent->Email)->send(new AgentPasswordResetMail($agent, $resetUrl));
            }

            $this->sent = true;
        } catch (\Throwable $e) {
            Log::error('Agent reset link error: ' . $e->getMessage());
            $this->addError('email', 'Failed to send reset link. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.agent.auth.forgot-password');
    }
}
