<?php

namespace App\Livewire\Realtor;

use App\Models\CommissionTable;
use Livewire\Component;
use Livewire\WithPagination;

class Commission extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $userId = auth()->id();

        $commissions = CommissionTable::with(['property', 'listingRealtor', 'referringRealtor', 'agent'])
            ->where(fn ($q) => $q->where('listing_realtor_id', $userId)->orWhere('referring_realtor_id', $userId))
            ->when($this->search, function ($q) {
                $term = $this->search;
                $q->where(function ($q2) use ($term) {
                    $q2->where('deal_reference', 'like', "%{$term}%")
                        ->orWhereHas('property', fn ($q3) => $q3->where('title', 'like', "%{$term}%"));
                });
            })
            ->latest('deal_date')
            ->paginate(15);

        $totalEarned = CommissionTable::where('status', 'paid')
            ->where(fn ($q) => $q->where('listing_realtor_id', $userId)->orWhere('referring_realtor_id', $userId))
            ->get()
            ->sum(fn ($c) => $c->listing_realtor_id == $userId ? $c->listing_realtor_amount : $c->referring_realtor_amount);

        $pendingAmount = CommissionTable::whereIn('status', ['pending', 'processing'])
            ->where(fn ($q) => $q->where('listing_realtor_id', $userId)->orWhere('referring_realtor_id', $userId))
            ->get()
            ->sum(fn ($c) => $c->listing_realtor_id == $userId ? $c->listing_realtor_amount : $c->referring_realtor_amount);

        return view('livewire.realtor.commission', compact('commissions', 'totalEarned', 'pendingAmount', 'userId'));
    }
}
