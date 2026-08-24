<?php

namespace App\Livewire\Realtor;

use App\Data\NigeriaLocations;
use App\Models\BankInformation;
use App\Models\UsersTable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileSettings extends Component
{
    use WithFileUploads;

    public $user;

    // --- Profile ---
    public string $first_name     = '';
    public string $last_name      = '';
    public string $phone          = '';
    public string $bio            = '';
    public string $specialization = '';
    public string $address        = '';
    public string $state          = '';
    public string $lga            = '';
    public bool   $profileSaved   = false;

    // --- Password ---
    public string $current_password = '';
    public string $new_password     = '';
    public string $confirm_password = '';
    public bool   $passwordSaved    = false;
    public string $passwordError    = '';

    // --- KYC ---
    public string $kyc_id_type  = '';
    public $kyc_front           = null;
    public $kyc_back            = null;
    public bool $kycSaved       = false;
    public string $kycError     = '';

    // --- Bank ---
    public string $account_name    = '';
    public string $bank_name       = '';
    public string $account_number  = '';
    public bool   $bankSaved       = false;
    public ?int   $bankId          = null;

    public function mount($user)
    {
        $this->user           = $user;
        $this->first_name     = $user->First_name ?? '';
        $this->last_name      = $user->Last_name ?? '';
        $this->phone          = $user->Phone ?? '';
        $this->bio            = $user->Bio ?? '';
        $this->specialization = $user->Specialization ?? '';
        $this->address        = $user->Address ?? '';
        $this->state          = $user->State ?? '';
        $this->lga            = $user->Lga ?? '';
        $this->kyc_id_type    = $user->kyc_id_type ?? '';

        $bank = BankInformation::where('user_id', $user->id)->first();
        if ($bank) {
            $this->bankId         = $bank->id;
            $this->account_name   = $bank->Account_Name ?? '';
            $this->bank_name      = $bank->Bank_Name ?? '';
            $this->account_number = $bank->Bank_Account ?? '';
        }
    }

    #[Computed]
    public function states(): array
    {
        return NigeriaLocations::states();
    }

    #[Computed]
    public function lgas(): array
    {
        return $this->state ? NigeriaLocations::lgasFor($this->state) : [];
    }

    public function updatedState(): void
    {
        $this->lga = '';
    }

    // ------------------------------------------------------------------ Profile

    public function updateProfile(): void
    {
        $this->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'phone'          => 'required|string|min:10|max:15',
            'bio'            => 'nullable|string|max:500',
            'specialization' => 'required|string',
            'state'          => 'nullable|string',
            'lga'            => 'nullable|string',
            'address'        => 'nullable|string|max:255',
        ]);

        $this->user->update([
            'First_name'     => $this->first_name,
            'Last_name'      => $this->last_name,
            'Phone'          => $this->phone,
            'Bio'            => $this->bio,
            'Specialization' => $this->specialization,
            'Address'        => $this->address,
            'State'          => $this->state,
            'Lga'            => $this->lga,
        ]);

        $this->profileSaved = true;
    }

    // ------------------------------------------------------------------ Password

    public function updatePassword(): void
    {
        $this->passwordError = '';

        $this->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ], [
            'confirm_password.same' => 'Passwords do not match.',
        ]);

        if (! Hash::check($this->current_password, $this->user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $this->user->update(['Password' => Hash::make($this->new_password)]);
        $this->reset(['current_password', 'new_password', 'confirm_password']);
        $this->passwordSaved = true;
    }

    // ------------------------------------------------------------------ KYC

    public function uploadKyc(): void
    {
        $this->kycError = '';

        $this->validate([
            'kyc_id_type' => 'required|string',
            'kyc_front'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'kyc_back'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'kyc_front.required' => 'Front of ID is required.',
            'kyc_front.max'      => 'Front image must not exceed 5 MB.',
            'kyc_back.max'       => 'Back image must not exceed 5 MB.',
        ]);

        try {
            // Store the object key, not a public URL - the kycaws disk is
            // private and these are read back through presigned links.
            $frontKey = $this->kyc_front->store('front', 'kycaws');

            $backKey = $this->user->kyc_back_url;
            if ($this->kyc_back) {
                $backKey = $this->kyc_back->store('back', 'kycaws');
            }

            $this->user->update([
                'kyc_id_type'  => $this->kyc_id_type,
                'kyc_front_url' => $frontKey,
                'kyc_back_url'  => $backKey,
                'kyc_status'    => 'pending',
            ]);

            $this->reset(['kyc_front', 'kyc_back']);
            $this->kycSaved = true;
        } catch (\Throwable $e) {
            $this->kycError = 'Upload failed. Please try again.';
        }
    }

    // ------------------------------------------------------------------ Bank

    public function saveBank(): void
    {
        $this->validate([
            'account_name'   => 'required|string|max:100',
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|digits:10',
        ]);

        $data = [
            'user_id'      => $this->user->id,
            'Account_Name' => $this->account_name,
            'Bank_Name'    => $this->bank_name,
            'Bank_Account' => $this->account_number,
        ];

        if ($this->bankId) {
            BankInformation::where('id', $this->bankId)->update($data);
        } else {
            $bank = BankInformation::create($data);
            $this->bankId = $bank->id;
        }

        $this->bankSaved = true;
    }

    public function render()
    {
        return view('livewire.realtor.profile-settings');
    }
}
