<?php

namespace App\Livewire\Agent;

use App\Mail\Agent\PropertyApprovedMail;
use App\Mail\Agent\PropertyRejectedMail;
use App\Models\AgentsTable;
use App\Models\PropertiesTable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyApprovals extends Component
{
    use WithPagination;

    public AgentsTable $agent;

    public string $tab    = 'pending';
    public string $search = '';

    public bool $detailOpen = false;
    public ?int $viewingId  = null;

    public bool   $rejectOpen   = false;
    public ?int   $rejectId     = null;
    public string $rejectReason = '';

    public function mount(): void
    {
        $this->agent = AgentsTable::findOrFail(session('agent_id'));
    }

    public function updatedTab(): void    { $this->resetPage(); }
    public function updatedSearch(): void { $this->resetPage(); }

    // ── Detail modal ──────────────────────────────────────────────────────────

    public function viewProperty(int $id): void
    {
        $this->viewingId  = $id;
        $this->detailOpen = true;
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
            $property = PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $this->agent->Assigned_area))
                ->with('user')
                ->findOrFail($id);

            $property->update(['approval_status' => 'approved']);

            try {
                Mail::to($property->user->Email)->send(new PropertyApprovedMail($property));
            } catch (\Throwable $e) {
                Log::error("PropertyApprovedMail failed for property #{$id}: " . $e->getMessage());
            }

            $this->closeDetail();
            $this->dispatch('agent-notify', message: 'Property approved. Realtor notified.', type: 'success');
        } catch (\Throwable $e) {
            Log::error("Property approve failed #{$id}: " . $e->getMessage());
            $this->dispatch('agent-notify', message: 'Failed to approve property. Please try again.', type: 'error');
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
            ['rejectReason.required' => 'Please provide a reason for rejection.'],
        );

        try {
            $property = PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $this->agent->Assigned_area))
                ->with('user')
                ->findOrFail($this->rejectId);

            $property->update(['approval_status' => 'rejected']);

            try {
                Mail::to($property->user->Email)->send(new PropertyRejectedMail($property, $this->rejectReason));
            } catch (\Throwable $e) {
                Log::error("PropertyRejectedMail failed for property #{$this->rejectId}: " . $e->getMessage());
            }

            $this->rejectOpen   = false;
            $this->rejectId     = null;
            $this->rejectReason = '';
            $this->closeDetail();
            $this->dispatch('agent-notify', message: 'Property rejected. Realtor notified.', type: 'success');
        } catch (\Throwable $e) {
            Log::error("Property reject failed #{$this->rejectId}: " . $e->getMessage());
            $this->dispatch('agent-notify', message: 'Failed to reject property. Please try again.', type: 'error');
        }
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $query = PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $this->agent->Assigned_area))
            ->with('user');

        if ($this->search) {
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$this->search}%")
                ->orWhere('city',  'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($uq) => $uq->where('First_name', 'like', "%{$this->search}%")
                    ->orWhere('Last_name', 'like', "%{$this->search}%"))
            );
        }

        $properties = match ($this->tab) {
            'approved' => $query->where('approval_status', 'approved'),
            'rejected' => $query->where('approval_status', 'rejected'),
            default    => $query->where('approval_status', 'pending'),
        };

        $properties = $properties->latest()->paginate(10);

        $viewingProperty = $this->viewingId
            ? PropertiesTable::with('user.bankInformation')->find($this->viewingId)
            : null;

        return view('livewire.agent.property-approvals', compact('properties', 'viewingProperty'));
    }
}
