<?php

namespace App\Livewire\Agency\Auth;

use App\Livewire\Concerns\ThrottlesAttempts;
use App\Models\AgencyPartner;
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

    public function login(): void
    {
        $this->validate();

        if (! $this->passesRateLimit('agency-login', $this->email, 5, 60)) {
            return;
        }

        try {
            $agency = AgencyPartner::where('email', $this->email)->first();

            if (! $agency || ! Hash::check($this->password, $agency->password)) {
                $this->addError('email', 'Invalid email or password.');
                return;
            }

            if ($agency->status !== 'active') {
                $this->addError('email', 'Your account is pending approval or has been suspended. Contact support.');
                return;
            }

            $this->clearRateLimit('agency-login', $this->email);

            // Rotate the session id on privilege change — see the agent login
            // for why a fixed pre-login id must not survive authentication.
            session()->regenerate();

            session([
                'agency_id'   => $agency->id,
                'agency_name' => $agency->company_name,
            ]);

            $this->redirect(route('agency.dashboard'));
        } catch (\Throwable $e) {
            Log::error('Agency login error: ' . $e->getMessage());
            $this->addError('email', 'Something went wrong. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.agency.auth.login');
    }
}
