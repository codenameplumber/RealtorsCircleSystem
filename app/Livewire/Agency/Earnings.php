<?php

namespace App\Livewire\Agency;

use App\Models\AgencyPartner;
use Livewire\Component;
use Livewire\WithPagination;

class Earnings extends Component
{
    use WithPagination;

    public AgencyPartner $agency;
    public string $statusFilter = 'all';

    public function mount(): void
    {
        $this->agency = AgencyPartner::findOrFail(session('agency_id'));
    }

    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function render()
    {
        $query = $this->agency->commissions()
            ->with(['property', 'listingRealtor', 'agent']);

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $commissions = $query->orderByDesc('deal_date')->paginate(15);

        $totals = [
            'total'    => $this->agency->commissions()->sum('agency_override_amount'),
            'paid'     => $this->agency->commissions()->where('status', 'paid')->sum('agency_override_amount'),
            'pending'  => $this->agency->commissions()->whereIn('status', ['pending', 'processing'])->sum('agency_override_amount'),
        ];

        return view('livewire.agency.earnings', compact('commissions', 'totals'));
    }
}
