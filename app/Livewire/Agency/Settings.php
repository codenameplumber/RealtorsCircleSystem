<?php

namespace App\Livewire\Agency;

use App\Models\AgencyPartner;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Settings extends Component
{
    public AgencyPartner $agency;

    public string $company_name = '';
    public string $first_name   = '';
    public string $last_name    = '';
    public string $email        = '';
    public string $phone        = '';

    public string $current_password  = '';
    public string $new_password      = '';
    public string $confirm_password  = '';

    public string $bank_name      = '';
    public string $account_name   = '';
    public string $account_number = '';

    public function mount(): void
    {
        $this->agency         = AgencyPartner::findOrFail(session('agency_id'));
        $this->company_name   = $this->agency->company_name;
        $this->first_name     = $this->agency->first_name;
        $this->last_name      = $this->agency->last_name;
        $this->email          = $this->agency->email;
        $this->phone          = $this->agency->phone ?? '';
        $this->bank_name      = $this->agency->bank_name ?? '';
        $this->account_name   = $this->agency->account_name ?? '';
        $this->account_number = $this->agency->account_number ?? '';
    }

    public function saveProfile(): void
    {
        $this->validate([
            'company_name' => 'required|string|max:150',
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:agency_partners,email,' . $this->agency->id,
            'phone'        => 'nullable|string|max:20',
        ]);

        $this->agency->update([
            'company_name' => $this->company_name,
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'email'        => $this->email,
            'phone'        => $this->phone ?: null,
        ]);

        session(['agency_name' => $this->company_name]);
        $this->dispatch('agency-notify', message: 'Profile updated successfully.', type: 'success');
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|same:confirm_password',
            'confirm_password' => 'required',
        ]);

        if (! Hash::check($this->current_password, $this->agency->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $this->agency->update(['password' => Hash::make($this->new_password)]);

        $this->current_password = '';
        $this->new_password     = '';
        $this->confirm_password = '';

        $this->dispatch('agency-notify', message: 'Password changed successfully.', type: 'success');
    }

    public function saveBank(): void
    {
        $this->validate([
            'bank_name'      => 'required|string|max:100',
            'account_name'   => 'required|string|max:150',
            'account_number' => 'required|string|max:20',
        ]);

        $this->agency->update([
            'bank_name'      => $this->bank_name,
            'account_name'   => $this->account_name,
            'account_number' => $this->account_number,
        ]);

        $this->dispatch('agency-notify', message: 'Bank details saved successfully.', type: 'success');
    }

    public function render()
    {
        return view('livewire.agency.settings');
    }
}
