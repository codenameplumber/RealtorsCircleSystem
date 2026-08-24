<?php

namespace App\Livewire\Admin;

use App\Data\NigeriaLocations;
use App\Helpers\IdHasher;
use App\Mail\Agent\PropertyApprovedMail;
use App\Mail\Agent\PropertyRejectedMail;
use App\Models\AdminAuditLog;
use App\Models\PropertiesTable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class Properties extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $statusFilter = '';
    public string $typeFilter   = '';
    public string $stateFilter  = '';
    public string $lgaFilter    = '';

    public bool   $rejectOpen   = false;
    public ?int   $rejectId     = null;
    public string $rejectReason = '';

    public function updatingSearch()       { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingTypeFilter()   { $this->resetPage(); }
    public function updatingStateFilter()  { $this->lgaFilter = ''; $this->resetPage(); }
    public function updatingLgaFilter()    { $this->resetPage(); }

    public function approveProperty(int $id): void
    {
        try {
            $property = PropertiesTable::with('user')->findOrFail($id);
            $property->update(['approval_status' => 'approved']);

            if ($property->user) {
                try {
                    Mail::to($property->user->Email)->send(new PropertyApprovedMail($property));
                } catch (\Throwable $e) {
                    Log::error("PropertyApprovedMail failed for property #{$id}: " . $e->getMessage());
                }
            }

            AdminAuditLog::record('property.approve', 'PropertiesTable', $id, "Approved property \"{$property->title}\"");

            session()->flash('prop_success', 'Property approved successfully. Realtor notified.');
        } catch (\Throwable $e) {
            Log::error("Admin property approve failed #{$id}: " . $e->getMessage());
            session()->flash('prop_error', 'Failed to approve property. Please try again.');
        }
    }

    public function openReject(int $id): void
    {
        $this->rejectId     = $id;
        $this->rejectReason = '';
        $this->resetErrorBag();
        $this->rejectOpen   = true;
    }

    public function closeReject(): void
    {
        $this->rejectOpen   = false;
        $this->rejectId     = null;
        $this->rejectReason = '';
    }

    public function submitReject(): void
    {
        $this->validate(
            ['rejectReason' => 'required|string|min:10|max:500'],
            [
                'rejectReason.required' => 'Please provide a reason for rejection.',
                'rejectReason.min'      => 'Reason must be at least 10 characters.',
            ],
        );

        try {
            $property = PropertiesTable::with('user')->findOrFail($this->rejectId);
            $property->update(['approval_status' => 'rejected']);

            if ($property->user) {
                try {
                    Mail::to($property->user->Email)->send(new PropertyRejectedMail($property, $this->rejectReason));
                } catch (\Throwable $e) {
                    Log::error("PropertyRejectedMail failed for property #{$this->rejectId}: " . $e->getMessage());
                }
            }

            AdminAuditLog::record('property.reject', 'PropertiesTable', $this->rejectId, $this->rejectReason);

            $this->closeReject();
            session()->flash('prop_success', 'Property rejected. Realtor notified.');
        } catch (\Throwable $e) {
            Log::error("Admin property reject failed #{$this->rejectId}: " . $e->getMessage());
            session()->flash('prop_error', 'Failed to reject property. Please try again.');
        }
    }

    public function render()
    {
        $properties = PropertiesTable::with('user')
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('title', 'like', "%{$this->search}%")
                   ->orWhere('city',  'like', "%{$this->search}%")
                   ->orWhere('state', 'like', "%{$this->search}%")
            ))
            ->when($this->statusFilter, fn($q) => $q->where('approval_status', $this->statusFilter))
            ->when($this->typeFilter,   fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->stateFilter,  fn($q) => $q->where('state', $this->stateFilter))
            ->when($this->lgaFilter,    fn($q) => $q->where('localarea', $this->lgaFilter))
            ->latest()
            ->paginate(15);

        $types = PropertiesTable::distinct()->orderBy('type')->pluck('type')->filter()->values();

        // Stat board — respects current search/location/type filters
        $statsBase = PropertiesTable::query()
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('title', 'like', "%{$this->search}%")
                   ->orWhere('city',  'like', "%{$this->search}%")
                   ->orWhere('state', 'like', "%{$this->search}%")
            ))
            ->when($this->typeFilter,  fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->stateFilter, fn($q) => $q->where('state', $this->stateFilter))
            ->when($this->lgaFilter,   fn($q) => $q->where('localarea', $this->lgaFilter));

        $stats = [
            'total'    => (clone $statsBase)->count(),
            'approved' => (clone $statsBase)->where('approval_status', 'approved')->count(),
            'pending'  => (clone $statsBase)->where('approval_status', 'pending')->count(),
            'rejected' => (clone $statsBase)->where('approval_status', 'rejected')->count(),
        ];

        return view('livewire.admin.properties', [
            'properties' => $properties,
            'types'      => $types,
            'states'     => NigeriaLocations::states(),
            'lgas'       => $this->stateFilter ? NigeriaLocations::lgasFor($this->stateFilter) : [],
            'stats'      => $stats,
        ]);
    }
}
