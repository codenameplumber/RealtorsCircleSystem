<?php

namespace App\Livewire\Agent;

use App\Models\AgentsTable;
use App\Models\CommissionTable;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Commission extends Component
{
    use WithPagination;

    public AgentsTable $agent;
    public float $totalEarned = 0;
    public float $pendingPayout = 0;
    public int $totalDeals = 0;

    public function mount(): void
    {
        $this->agent = AgentsTable::findOrFail(session('agent_id'));

        try {
            $base = CommissionTable::where('agent_id', $this->agent->id);
            $this->totalEarned   = (float) (clone $base)->where('status', 'paid')->sum('agent_fee');
            $this->pendingPayout = (float) (clone $base)->whereIn('status', ['pending', 'processing'])->sum('agent_fee');
            $this->totalDeals    = (clone $base)->count();
        } catch (\Throwable) {
            $this->totalEarned = $this->pendingPayout = 0;
            $this->totalDeals = 0;
        }
    }

    public function generatePaymentLink(int $commissionId): void
    {
        $commission = CommissionTable::where('agent_id', $this->agent->id)->findOrFail($commissionId);

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
        try {
            $commissions = CommissionTable::where('agent_id', $this->agent->id)
                ->with(['property', 'listingRealtor', 'referringRealtor'])
                ->latest('deal_date')
                ->paginate(15);
        } catch (\Throwable) {
            $commissions = collect()->paginate(15);
        }

        return view('livewire.agent.commission', compact('commissions'));
    }
}
