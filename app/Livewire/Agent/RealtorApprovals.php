<?php

namespace App\Livewire\Agent;

use App\Mail\Agent\RealtorApprovedMail;
use App\Mail\Agent\RealtorRejectedMail;
use App\Models\AgentsTable;
use App\Models\UsersTable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class RealtorApprovals extends Component
{
    use WithPagination;

    public AgentsTable $agent;

    public string $tab    = 'pending';
    public string $search = '';

    public bool $detailOpen  = false;
    public ?int $viewingId   = null;

    public bool   $rejectOpen   = false;
    public ?int   $rejectId     = null;
    public string $rejectReason = '';

    public function mount(): void
    {
        $this->agent = AgentsTable::findOrFail(session('agent_id'));
    }

    public function updatedTab(): void   { $this->resetPage(); }
    public function updatedSearch(): void { $this->resetPage(); }

    // ── Detail modal ──────────────────────────────────────────────────────────

    public function viewRealtor(int $id): void
    {
        $this->viewingId   = $id;
        $this->detailOpen  = true;
    }

    public function closeDetail(): void
    {
        $this->detailOpen = false;
        $this->viewingId  = null;
    }

    // ── Approve ───────────────────────────────────────────────────────────────

    public function approve(int $id): void
    {
        try {
            $realtor = UsersTable::where('Lga', $this->agent->Assigned_area)->findOrFail($id);
            $realtor->update(['Status' => 'active', 'kyc_status' => 'approved']);

            try {
                Mail::to($realtor->Email)->send(new RealtorApprovedMail($realtor));
            } catch (\Throwable $e) {
                Log::error("RealtorApprovedMail failed for realtor #{$id}: " . $e->getMessage());
            }

            $this->closeDetail();
            $this->dispatch('agent-notify', message: 'Realtor approved. Notification sent.', type: 'success');
        } catch (\Throwable $e) {
            Log::error("Realtor approve failed #{$id}: " . $e->getMessage());
            $this->dispatch('agent-notify', message: 'Failed to approve realtor. Please try again.', type: 'error');
        }
    }

    // ── Reject ────────────────────────────────────────────────────────────────

    public function openReject(int $id): void
    {
        $this->rejectId     = $id;
        $this->rejectReason = '';
        $this->resetErrorBag();
        $this->rejectOpen   = true;
    }

    public function submitReject(): void
    {
        $this->validate(
            ['rejectReason' => 'required|string|min:10|max:500'],
            ['rejectReason.required' => 'Please provide a reason for rejection.',
             'rejectReason.min'      => 'Reason must be at least 10 characters.'],
        );

        try {
            $realtor = UsersTable::where('Lga', $this->agent->Assigned_area)->findOrFail($this->rejectId);
            $realtor->update(['Status' => 'inactive', 'kyc_status' => 'rejected']);

            try {
                Mail::to($realtor->Email)->send(new RealtorRejectedMail($realtor, $this->rejectReason));
            } catch (\Throwable $e) {
                Log::error("RealtorRejectedMail failed for realtor #{$this->rejectId}: " . $e->getMessage());
            }

            $this->rejectOpen   = false;
            $this->rejectId     = null;
            $this->rejectReason = '';
            $this->closeDetail();
            $this->dispatch('agent-notify', message: 'Realtor rejected. Notification sent.', type: 'success');
        } catch (\Throwable $e) {
            Log::error("Realtor reject failed #{$this->rejectId}: " . $e->getMessage());
            $this->dispatch('agent-notify', message: 'Failed to reject realtor. Please try again.', type: 'error');
        }
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $query = UsersTable::where('Lga', $this->agent->Assigned_area)->with('bankInformation');

        if ($this->search) {
            $query->where(fn ($q) => $q
                ->where('First_name', 'like', "%{$this->search}%")
                ->orWhere('Last_name',  'like', "%{$this->search}%")
                ->orWhere('Email',      'like', "%{$this->search}%")
                ->orWhere('Phone',      'like', "%{$this->search}%")
            );
        }

        $realtors = match ($this->tab) {
            'approved' => $query->where('Status', 'active')->where('kyc_status', 'approved'),
            'rejected' => $query->where('kyc_status', 'rejected'),
            default    => $query->where('kyc_status', 'pending'),
        };

        $realtors = $realtors->latest()->paginate(15);

        $viewingRealtor = $this->viewingId
            ? UsersTable::with('bankInformation')->find($this->viewingId)
            : null;

        return view('livewire.agent.realtor-approvals', compact('realtors', 'viewingRealtor'));
    }
}
