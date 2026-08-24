<?php

namespace App\Livewire\Agent\Auth;

use App\Livewire\Concerns\ThrottlesAttempts;
use App\Models\AgentsTable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Login extends Component
{
    use ThrottlesAttempts;

    public string $email    = '';
    public string $password = '';

    protected array $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];

    protected array $messages = [
        'email.required'    => 'Email address is required.',
        'email.email'       => 'Please enter a valid email address.',
        'password.required' => 'Password is required.',
    ];

    public function login(): void
    {
        $this->validate();

        if (! $this->passesRateLimit('agent-login', $this->email, 5, 60)) {
            return;
        }

        try {
            $agent = AgentsTable::where('Email', $this->email)->first();

            if (! $agent || ! Hash::check($this->password, $agent->Password)) {
                $this->addError('email', 'Invalid email or password. Please try again.');
                return;
            }

            if ($agent->Status !== 'active') {
                $this->addError('email', 'Your account is currently inactive. Please contact support.');
                return;
            }

            $this->clearRateLimit('agent-login', $this->email);

            // Rotate the session id on privilege change. Without this, an id
            // fixed before login (via a planted cookie) stays valid afterwards
            // and hands the attacker the authenticated session.
            session()->regenerate();

            session([
                'agent_id'   => $agent->id,
                'agent_name' => $agent->First_name,
            ]);

            $this->redirect(route('agent.dashboard'));
        } catch (\Throwable $e) {
            Log::error('Agent login error: ' . $e->getMessage());
            $this->addError('email', 'Something went wrong. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.agent.auth.login');
    }
}
