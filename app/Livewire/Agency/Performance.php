<?php

namespace App\Livewire\Agency;

use App\Models\AgencyPartner;
use App\Models\CommissionTable;
use Livewire\Component;

class Performance extends Component
{
    public AgencyPartner $agency;

    public function mount(): void
    {
        $this->agency = AgencyPartner::findOrFail(session('agency_id'));
    }

    public function render()
    {
        $realtors    = $this->agency->realtors()->get();
        $commissions = $this->agency->commissions()->with(['property', 'listingRealtor'])->get();

        $stats = [
            'total_realtors'  => $realtors->count(),
            'active_realtors' => $realtors->where('Status', 'active')->count(),
            'total_deals'     => $commissions->count(),
            'total_override'  => $commissions->sum('agency_override_amount'),
            'paid_override'   => $commissions->where('status', 'paid')->sum('agency_override_amount'),
            'pending_override'=> $commissions->whereIn('status', ['pending', 'processing'])->sum('agency_override_amount'),
        ];

        // Top performing realtors by deal count
        $topRealtors = $realtors->map(function ($realtor) use ($commissions) {
            $realtorDeals = $commissions->where('listing_realtor_id', $realtor->id);
            return (object) [
                'name'         => trim("{$realtor->First_name} {$realtor->Last_name}"),
                'email'        => $realtor->Email,
                'status'       => $realtor->Status,
                'deal_count'   => $realtorDeals->count(),
                'total_value'  => $realtorDeals->sum('total_amount'),
                'your_override'=> $realtorDeals->sum('agency_override_amount'),
            ];
        })->sortByDesc('deal_count')->values()->take(10);

        return view('livewire.agency.performance', compact('stats', 'topRealtors'));
    }
}
