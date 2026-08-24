<?php

namespace App\Livewire\Admin;

use App\Data\NigeriaLocations;
use App\Models\CommissionTable;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Commission extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $stateFilter  = '';
    public string $lgaFilter    = '';
    public string $topState     = '';

    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingStateFilter()  { $this->lgaFilter = ''; $this->resetPage(); }
    public function updatingLgaFilter()    { $this->resetPage(); }

    public function generatePaymentLink(int $commissionId): void
    {
        $commission = CommissionTable::findOrFail($commissionId);

        if (! $commission->payment_token) {
            // payment_token is guarded - mint it explicitly.
            $commission->forceFill([
                'payment_token'             => Str::random(40),
                'payment_link_generated_at' => now(),
            ])->save();
            $commission->refresh();
        }

        $this->dispatch('copy-payment-link', url: route('payment.show', $commission->payment_token));
    }

    public function render()
    {
        $commissions = CommissionTable::with(['property', 'listingRealtor', 'referringRealtor', 'agent'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->stateFilter, fn($q) => $q->whereHas('property', fn($q2) =>
                $q2->where('state', $this->stateFilter)
            ))
            ->when($this->lgaFilter, fn($q) => $q->whereHas('property', fn($q2) =>
                $q2->where('localarea', $this->lgaFilter)
            ))
            ->latest()
            ->paginate(15);

        $summary = [
            'total_deals'   => CommissionTable::count(),
            'total_paid'    => CommissionTable::where('status', 'paid')->sum('total_amount'),
            'total_pending' => CommissionTable::where('status', 'pending')->sum('total_amount'),
            'platform_fees' => CommissionTable::where('status', 'paid')->sum('platform_fee'),
            'agent_fees'    => CommissionTable::where('status', 'paid')->sum('agent_fee'),
        ];

        // Top 10 highest-earning locations — by State, or by LGA within a chosen state
        $groupByLga = (bool) $this->topState;
        $topLocations = CommissionTable::where('status', 'paid')
            ->with('property:id,state,localarea')
            ->get(['id', 'property_id', 'total_amount'])
            ->filter(fn($c) => $c->property && (!$groupByLga || $c->property->state === $this->topState))
            ->groupBy(fn($c) => $groupByLga ? ($c->property->localarea ?: 'Unknown') : ($c->property->state ?: 'Unknown'))
            ->map(fn($group, $name) => (object) [
                'name'  => $name,
                'gross' => $group->sum('total_amount'),
                'deals' => $group->count(),
            ])
            ->sortByDesc('gross')
            ->take(10)
            ->values();

        return view('livewire.admin.commission', compact('commissions', 'summary', 'topLocations', 'groupByLga'))
            ->with([
                'states' => NigeriaLocations::states(),
                'lgas'   => $this->stateFilter ? NigeriaLocations::lgasFor($this->stateFilter) : [],
            ]);
    }
}
