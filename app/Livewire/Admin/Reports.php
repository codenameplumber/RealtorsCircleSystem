<?php

namespace App\Livewire\Admin;

use App\Data\NigeriaLocations;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\UsersTable;
use App\Models\AgentsTable;
use Livewire\Component;

class Reports extends Component
{
    public string $stateFilter = '';
    public string $lgaFilter   = '';
    public string $dateRange   = 'all'; // all | this_year | this_month

    public function updatingStateFilter() { $this->lgaFilter = ''; }

    public function render()
    {
        $propQ = PropertiesTable::query()
            ->when($this->stateFilter, fn($q) => $q->where('state', $this->stateFilter))
            ->when($this->lgaFilter,   fn($q) => $q->where('localarea', $this->lgaFilter));
        $comQ  = CommissionTable::query();

        if ($this->dateRange === 'this_year') {
            $propQ->whereYear('created_at', now()->year);
            $comQ->whereYear('created_at', now()->year);
        } elseif ($this->dateRange === 'this_month') {
            $propQ->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month);
            $comQ->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month);
        }

        // Core stats
        $stats = [
            'total_properties'    => (clone $propQ)->count(),
            'approved_properties' => (clone $propQ)->where('approval_status', 'approved')->count(),
            'pending_properties'  => (clone $propQ)->where('approval_status', 'pending')->count(),
            'rejected_properties' => (clone $propQ)->where('approval_status', 'rejected')->count(),
            'closed_deals'        => (clone $comQ)->where('status', 'paid')->count(),
            'closed_value'        => (clone $comQ)->where('status', 'paid')->sum('total_amount'),
            'pending_deals'       => (clone $comQ)->where('status', 'pending')->count(),
            'pending_value'       => (clone $comQ)->where('status', 'pending')->sum('total_amount'),
            'total_realtors'      => UsersTable::when($this->stateFilter, fn($q) => $q->where('State', $this->stateFilter))
                                                ->when($this->lgaFilter, fn($q) => $q->where('Lga', $this->lgaFilter))
                                                ->count(),
            'total_agents'        => AgentsTable::when($this->stateFilter, fn($q) => $q->where('State', $this->stateFilter))
                                                 ->when($this->lgaFilter, fn($q) => $q->where('Assigned_area', $this->lgaFilter))
                                                 ->count(),
            'platform_revenue'    => (clone $comQ)->where('status', 'paid')->sum('platform_fee'),
        ];

        // Property type breakdown (most listed / sold)
        $byType = PropertiesTable::query()
            ->when($this->stateFilter, fn($q) => $q->where('state', $this->stateFilter))
            ->when($this->lgaFilter,   fn($q) => $q->where('localarea', $this->lgaFilter))
            ->selectRaw('type, COUNT(*) as total, SUM(CASE WHEN approval_status = "approved" THEN 1 ELSE 0 END) as approved')
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

        // Listing type breakdown (sale, rent, lease, etc.)
        $byListingType = PropertiesTable::query()
            ->when($this->stateFilter, fn($q) => $q->where('state', $this->stateFilter))
            ->when($this->lgaFilter,   fn($q) => $q->where('localarea', $this->lgaFilter))
            ->selectRaw('listing_type, COUNT(*) as total')
            ->groupBy('listing_type')
            ->orderByDesc('total')
            ->get();

        // City stats (properties count per city)
        $byCity = PropertiesTable::query()
            ->when($this->stateFilter, fn($q) => $q->where('state', $this->stateFilter))
            ->when($this->lgaFilter,   fn($q) => $q->where('localarea', $this->lgaFilter))
            ->selectRaw('city, state, COUNT(*) as total, MIN(CAST(price AS DECIMAL)) as min_price, MAX(CAST(price AS DECIMAL)) as max_price')
            ->groupBy('city', 'state')
            ->orderByDesc('total')
            ->take(15)
            ->get();

        // Monthly deals closed
        $monthlyDeals = array_fill(0, 12, 0);
        CommissionTable::where('status', 'paid')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->get()
            ->each(fn($r) => $monthlyDeals[$r->month - 1] = (int) $r->total);

        // Monthly registrations
        $monthlyRealtors = array_fill(0, 12, 0);
        UsersTable::whereYear('created_at', now()->year)
            ->when($this->stateFilter, fn($q) => $q->where('State', $this->stateFilter))
            ->when($this->lgaFilter,   fn($q) => $q->where('Lga', $this->lgaFilter))
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->get()
            ->each(fn($r) => $monthlyRealtors[$r->month - 1] = (int) $r->total);

        return view('livewire.admin.reports', [
            'stats'          => $stats,
            'byType'         => $byType,
            'byListingType'  => $byListingType,
            'byCity'         => $byCity,
            'monthlyDeals'   => $monthlyDeals,
            'monthlyRealtors'=> $monthlyRealtors,
            'states'         => NigeriaLocations::states(),
            'lgas'           => $this->stateFilter ? NigeriaLocations::lgasFor($this->stateFilter) : [],
        ]);
    }
}
