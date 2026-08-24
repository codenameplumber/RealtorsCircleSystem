<?php

namespace App\Livewire\Agent\Auth;

use App\Models\AgentsTable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token                = '';
    public string $email                = '';
    public string $password             = '';
    public string $password_confirmation = '';

    public function mount(string $token, string $email): void
    {
        $this->token = $token;
        $this->email = $email;
    }

    protected array $rules = [
        'password'             => 'required|min:8',
        'password_confirmation' => 'required|same:password',
    ];

    protected array $messages = [
        'password.required'              => 'New password is required.',
        'password.min'                   => 'Password must be at least 8 characters.',
        'password_confirmation.required' => 'Please confirm your new password.',
        'password_confirmation.same'     => 'Passwords do not match.',
    ];

    public function resetPassword(): void
    {
        $this->validate();

        try {
            $agent = AgentsTable::where('Email', $this->email)->first();

            if (! $agent || ! $agent->isValidResetToken($this->token)) {
                $this->addError('password', 'This reset link is invalid or has expired. Please request a new one.');
                return;
            }

            $agent->update(['Password' => Hash::make($this->password)]);
            $agent->clearResetToken();

            session()->flash('success', 'Password reset successfully. You can now sign in.');
            $this->redirect(route('agent.login'));
        } catch (\Throwable $e) {
            Log::error('Agent password reset error: ' . $e->getMessage());
            $this->addError('password', 'Failed to reset password. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.agent.auth.reset-password');
    }
}
