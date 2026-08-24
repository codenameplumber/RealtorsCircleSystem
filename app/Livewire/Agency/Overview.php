<?php

namespace App\Livewire\Agency;

use App\Models\AgencyPartner;
use App\Models\CommissionTable;
use Livewire\Component;

class Overview extends Component
{
    public AgencyPartner $agency;

    public function mount(): void
    {
        $this->agency = AgencyPartner::findOrFail(session('agency_id'));
    }

    public function render()
    {
        $realtors      = $this->agency->realtors()->get();
        $commissions   = $this->agency->commissions();

        $stats = [
            'network_size'    => $realtors->count(),
            'active_realtors' => $realtors->where('Status', 'active')->count(),
            'total_earned'    => $commissions->sum('agency_override_amount'),
            'pending_earned'  => $commissions->where('status', 'pending')->sum('agency_override_amount'),
            'deals_closed'    => $commissions->count(),
        ];

        $recentDeals = $this->agency->commissions()
            ->with(['property', 'listingRealtor'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.agency.overview', compact('stats', 'recentDeals'));
    }
}
