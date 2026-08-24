<?php

namespace App\Livewire\Agent;

use App\Models\AgentsTable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Settings extends Component
{
    public AgentsTable $agent;

    // Profile (editable)
    public string $first_name = '';
    public string $last_name  = '';
    public string $phone      = '';
    public bool   $profileSaved = false;

    // Password
    public string $current_password = '';
    public string $new_password     = '';
    public string $confirm_password = '';
    public bool   $passwordSaved    = false;

    // Bank
    public string $bank_name      = '';
    public string $account_number = '';
    public string $account_name   = '';
    public bool   $bankSaved      = false;

    public function mount(): void
    {
        $this->agent = AgentsTable::findOrFail(session('agent_id'));

        $this->first_name     = $this->agent->First_name ?? '';
        $this->last_name      = $this->agent->Last_name  ?? '';
        $this->phone          = $this->agent->Phone      ?? '';
        $this->bank_name      = $this->agent->bank_name      ?? '';
        $this->account_number = $this->agent->account_number ?? '';
        $this->account_name   = $this->agent->account_name   ?? '';
    }

    // ── Profile ───────────────────────────────────────────────────────────────

    public function updateProfile(): void
    {
        $this->profileSaved = false;

        $this->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'required|string|min:10|max:15',
        ]);

        try {
            $this->agent->update([
                'First_name' => $this->first_name,
                'Last_name'  => $this->last_name,
                'Phone'      => $this->phone,
            ]);
            $this->profileSaved = true;
        } catch (\Throwable $e) {
            Log::error("Agent profile update failed #{$this->agent->id}: " . $e->getMessage());
            $this->addError('first_name', 'Failed to update profile. Please try again.');
        }
    }

    // ── Password ──────────────────────────────────────────────────────────────

    public function updatePassword(): void
    {
        $this->passwordSaved = false;

        $this->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ], ['confirm_password.same' => 'Passwords do not match.']);

        try {
            if (! Hash::check($this->current_password, $this->agent->Password)) {
                $this->addError('current_password', 'Current password is incorrect.');
                return;
            }

            $this->agent->update(['Password' => Hash::make($this->new_password)]);
            $this->reset(['current_password', 'new_password', 'confirm_password']);
            $this->passwordSaved = true;
        } catch (\Throwable $e) {
            Log::error("Agent password update failed #{$this->agent->id}: " . $e->getMessage());
            $this->addError('current_password', 'Failed to update password. Please try again.');
        }
    }

    // ── Bank ──────────────────────────────────────────────────────────────────

    public function saveBank(): void
    {
        $this->bankSaved = false;

        $this->validate([
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|digits:10',
            'account_name'   => 'required|string|max:100',
        ]);

        try {
            $this->agent->update([
                'bank_name'      => $this->bank_name,
                'account_number' => $this->account_number,
                'account_name'   => $this->account_name,
            ]);
            $this->bankSaved = true;
        } catch (\Throwable $e) {
            Log::error("Agent bank update failed #{$this->agent->id}: " . $e->getMessage());
            $this->addError('bank_name', 'Failed to save bank information. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.agent.settings');
    }
}
