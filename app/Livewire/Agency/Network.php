<?php

namespace App\Livewire\Agency;

use App\Models\AgencyPartner;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use Livewire\Component;
use Livewire\WithPagination;

class Network extends Component
{
    use WithPagination;

    public AgencyPartner $agency;
    public string $search = '';

    public function mount(): void
    {
        $this->agency = AgencyPartner::findOrFail(session('agency_id'));
    }

    public function updatedSearch(): void { $this->resetPage(); }

    public function render()
    {
        $realtorIds = $this->agency->realtors()->pluck('id');

        $realtors = $this->agency->realtors()
            ->withCount('properties')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('First_name', 'like', '%' . $this->search . '%')
                  ->orWhere('Last_name',  'like', '%' . $this->search . '%')
                  ->orWhere('Email',      'like', '%' . $this->search . '%');
            }))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_realtors'   => $realtorIds->count(),
            'closed_deals'     => CommissionTable::where('agency_id', $this->agency->id)
                                    ->where('status', 'paid')
                                    ->sum('agency_override_amount'),
            'pending_deals'    => CommissionTable::where('agency_id', $this->agency->id)
                                    ->whereIn('status', ['pending', 'processing'])
                                    ->sum('agency_override_amount'),
            'total_properties' => PropertiesTable::whereIn('user_id', $realtorIds)->count(),
        ];

        return view('livewire.agency.network', compact('realtors', 'stats'));
    }
}
