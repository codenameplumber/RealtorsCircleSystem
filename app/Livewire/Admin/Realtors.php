<?php

namespace App\Livewire\Admin;

use App\Data\NigeriaLocations;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\UsersTable;
use Livewire\Component;
use Livewire\WithPagination;

class Realtors extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $statusFilter = '';
    public string $stateFilter  = '';
    public string $lgaFilter    = '';
    public string $topState     = '';

    public bool  $showProfile = false;
    public ?int  $profileId   = null;

    public function updatingSearch()       { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingStateFilter()  { $this->lgaFilter = ''; $this->resetPage(); }
    public function updatingLgaFilter()    { $this->resetPage(); }

    public function viewProfile(int $id)
    {
        $this->profileId   = $id;
        $this->showProfile = true;
    }

    public function closeProfile()
    {
        $this->showProfile = false;
        $this->profileId   = null;
    }

    public function toggleStatus(int $id)
    {
        $r = UsersTable::findOrFail($id);
        $r->update(['Status' => $r->Status === 'active' ? 'suspended' : 'active']);
    }

    public function render()
    {
        $realtors = UsersTable::query()
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('First_name', 'like', "%{$this->search}%")
                   ->orWhere('Last_name',  'like', "%{$this->search}%")
                   ->orWhere('Email',      'like', "%{$this->search}%")
            ))
            ->when($this->statusFilter, fn($q) => $q->where('Status', $this->statusFilter))
            ->when($this->stateFilter,  fn($q) => $q->where('State', $this->stateFilter))
            ->when($this->lgaFilter,    fn($q) => $q->where('Lga', $this->lgaFilter))
            ->latest()
            ->paginate(15);

        // Stat board — respects current filters except status (so the breakdown stays meaningful)
        $statsBase = UsersTable::query()
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('First_name', 'like', "%{$this->search}%")
                   ->orWhere('Last_name',  'like', "%{$this->search}%")
                   ->orWhere('Email',      'like', "%{$this->search}%")
            ))
            ->when($this->stateFilter, fn($q) => $q->where('State', $this->stateFilter))
            ->when($this->lgaFilter,   fn($q) => $q->where('Lga', $this->lgaFilter));

        $stats = [
            'total'     => (clone $statsBase)->count(),
            'active'    => (clone $statsBase)->where('Status', 'active')->count(),
            'pending'   => (clone $statsBase)->where('Status', 'pending')->count(),
            'suspended' => (clone $statsBase)->whereIn('Status', ['suspended', 'inactive'])->count(),
        ];

        // Top 10 by total deals (listing + referring) and earnings
        $topRealtors = UsersTable::query()
            ->when($this->topState, fn($q) => $q->where('State', $this->topState))
            ->withCount([
                'listingCommissions as listing_deals'     => fn($q) => $q->where('status', 'paid'),
                'referringCommissions as referring_deals' => fn($q) => $q->where('status', 'paid'),
            ])
            ->withSum([
                'listingCommissions as listing_earned'     => fn($q) => $q->where('status', 'paid'),
                'referringCommissions as referring_earned' => fn($q) => $q->where('status', 'paid'),
            ], 'total_amount')
            ->get()
            ->map(fn($r) => tap($r, function ($r) {
                $r->total_deals  = ($r->listing_deals  ?? 0) + ($r->referring_deals  ?? 0);
                $r->total_earned = ($r->listing_earned ?? 0) + ($r->referring_earned ?? 0);
            }))
            ->sortByDesc('total_deals')
            ->take(10)
            ->values();

        $profileRealtor = null;
        $profileStats   = null;
        if ($this->profileId) {
            $profileRealtor = UsersTable::with(['bankInformation', 'properties'])->find($this->profileId);
            if ($profileRealtor) {
                $listingDeals    = CommissionTable::where('listing_realtor_id', $this->profileId)->where('status', 'paid');
                $referringDeals  = CommissionTable::where('referring_realtor_id', $this->profileId)->where('status', 'paid');
                $profileStats = [
                    'total_properties'   => $profileRealtor->properties()->count(),
                    'approved_properties'=> $profileRealtor->properties()->where('approval_status', 'approved')->count(),
                    'listing_deals'      => $listingDeals->count(),
                    'listing_earned'     => $listingDeals->sum('listing_realtor_amount'),
                    'referring_deals'    => $referringDeals->count(),
                    'referring_earned'   => $referringDeals->sum('referring_realtor_amount'),
                    'total_deals'        => $listingDeals->count() + $referringDeals->count(),
                    'total_earned'       => $listingDeals->sum('listing_realtor_amount') + $referringDeals->sum('referring_realtor_amount'),
                ];
            }
        }

        return view('livewire.admin.realtors', [
            'realtors'      => $realtors,
            'topRealtors'   => $topRealtors,
            'profileRealtor'=> $profileRealtor,
            'profileStats'  => $profileStats,
            'states'        => NigeriaLocations::states(),
            'lgas'          => $this->stateFilter ? NigeriaLocations::lgasFor($this->stateFilter) : [],
            'stats'         => $stats,
        ]);
    }
}
