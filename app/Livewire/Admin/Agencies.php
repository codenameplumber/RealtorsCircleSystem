<?php

namespace App\Livewire\Admin;

use App\Data\NigeriaLocations;
use App\Mail\Agency\AgencyStatusMail;
use App\Mail\Agency\AgencyWelcomeMail;
use App\Models\AdminAuditLog;
use App\Models\AgencyPartner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Agencies extends Component
{
    use WithPagination;

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $search       = '';
    public string $statusFilter = '';
    public string $stateFilter  = '';

    // ── Create form ───────────────────────────────────────────────────────────
    public bool   $createOpen    = false;
    public string $companyName   = '';
    public string $firstName     = '';
    public string $lastName      = '';
    public string $email         = '';
    public string $phone         = '';
    public string $password      = '';
    public string $state         = '';
    public string $lga           = '';

    // ── View modal ────────────────────────────────────────────────────────────
    public bool $showProfile  = false;
    public ?int $profileId    = null;

    public function updatingSearch()       { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingStateFilter()  { $this->resetPage(); }
    public function updatingState()        { $this->lga = ''; }

    // ── Create ────────────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->reset(['companyName', 'firstName', 'lastName', 'email', 'phone', 'password', 'state', 'lga']);
        $this->resetErrorBag();
        $this->createOpen = true;
    }

    public function closeCreate(): void
    {
        $this->createOpen = false;
        $this->resetErrorBag();
    }

    public function create(): void
    {
        $this->validate([
            'companyName' => ['required', 'string', 'max:120'],
            'firstName'   => ['required', 'string', 'max:60'],
            'lastName'    => ['required', 'string', 'max:60'],
            'email'       => ['required', 'email', 'unique:agency_partners,email'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'password'    => ['required', 'string', 'min:8'],
            'state'       => ['required', 'string'],
            'lga'         => ['required', 'string'],
        ]);

        try {
            $plain  = $this->password;

            $agency = AgencyPartner::create([
                'company_name'  => $this->companyName,
                'first_name'    => $this->firstName,
                'last_name'     => $this->lastName,
                'email'         => $this->email,
                'phone'         => $this->phone ?: null,
                'password'      => Hash::make($plain),
                'referral_code' => AgencyPartner::generateReferralCode(),
                'status'        => 'active',
                'state'         => $this->state,
                'lga'           => $this->lga,
            ]);

            try {
                Mail::to($agency->email)->send(new AgencyWelcomeMail($agency, $plain));
            } catch (\Throwable $e) {
                Log::error("AgencyWelcomeMail failed for agency #{$agency->id}: " . $e->getMessage());
            }

            AdminAuditLog::record('agency.create', 'AgencyPartner', $agency->id, "Created agency \"{$agency->company_name}\"");

            $this->closeCreate();
            $this->dispatch('admin-notify', message: "Agency \"{$agency->company_name}\" created. Welcome email sent to {$agency->email}.", type: 'success');
        } catch (\Throwable $e) {
            Log::error('Admin create agency failed: ' . $e->getMessage());
            $this->dispatch('admin-notify', message: 'Failed to create agency. Please try again.', type: 'error');
        }
    }

    // ── Profile modal ─────────────────────────────────────────────────────────

    public function viewProfile(int $id): void
    {
        $this->profileId   = $id;
        $this->showProfile = true;
    }

    public function closeProfile(): void
    {
        $this->showProfile = false;
        $this->profileId   = null;
    }

    // ── Toggle status (with email notification) ───────────────────────────────

    public function toggleStatus(int $id): void
    {
        $agency    = AgencyPartner::findOrFail($id);
        $newStatus = $agency->status === 'active' ? 'suspended' : 'active';

        $agency->update(['status' => $newStatus]);

        try {
            Mail::to($agency->email)->send(new AgencyStatusMail($agency, $newStatus));
        } catch (\Throwable $e) {
            Log::error("AgencyStatusMail [{$newStatus}] failed for agency #{$id}: " . $e->getMessage());
        }

        AdminAuditLog::record(
            $newStatus === 'active' ? 'agency.activate' : 'agency.suspend',
            'AgencyPartner',
            $id,
            "Agency \"{$agency->company_name}\" status changed to {$newStatus}"
        );

        if ($this->showProfile && $this->profileId === $id) {
            $this->closeProfile();
        }
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function delete(int $id): void
    {
        $agency = AgencyPartner::findOrFail($id);
        $name   = $agency->company_name;
        $agency->delete();

        AdminAuditLog::record('agency.delete', 'AgencyPartner', $id, "Deleted agency \"{$name}\"");

        $this->dispatch('admin-notify', message: 'Agency removed.', type: 'success');
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $query = AgencyPartner::query()
            ->when($this->search, fn ($q) =>
                $q->where('company_name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
            )
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->stateFilter,  fn ($q) => $q->where('state', $this->stateFilter));

        $agencies = (clone $query)->withCount('realtors')->latest()->paginate(15);

        $stats = [
            'total'        => AgencyPartner::count(),
            'active'       => AgencyPartner::where('status', 'active')->count(),
            'suspended'    => AgencyPartner::whereIn('status', ['suspended', 'pending'])->count(),
            'total_realtors' => \App\Models\UsersTable::whereNotNull('agency_id')->count(),
        ];

        $profileAgency = $this->profileId
            ? AgencyPartner::withCount('realtors')->find($this->profileId)
            : null;

        return view('livewire.admin.agencies', [
            'agencies'       => $agencies,
            'stats'          => $stats,
            'states'         => NigeriaLocations::states(),
            'lgas'           => $this->state ? NigeriaLocations::lgasFor($this->state) : [],
            'profileAgency'  => $profileAgency,
        ]);
    }
}
