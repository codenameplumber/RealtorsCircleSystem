<?php

namespace App\Livewire\Agent;

use App\Models\AgentsTable;
use App\Models\CommissionTable;
use App\Models\PayoutRequest;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Payments extends Component
{
    use WithPagination;

    public AgentsTable $agent;
    public bool $requestOpen = false;
    public string $requestAmount = '';

    public function mount(): void
    {
        $this->agent = AgentsTable::findOrFail(session('agent_id'));
    }

    public function openRequest(): void
    {
        $this->requestAmount = '';
        $this->resetErrorBag();
        $this->requestOpen = true;
    }

    public function closeRequest(): void
    {
        $this->requestOpen = false;
        $this->requestAmount = '';
        $this->resetErrorBag();
    }

    public function submitRequest(): void
    {
        if (! $this->hasBank()) {
            $this->dispatch('payout-notify', message: 'Add your bank account details in Settings before requesting a withdrawal.', type: 'error');
            return;
        }

        $available = $this->availableBalance();

        $this->validate([
            'requestAmount' => ['required', 'numeric', 'min:1', 'max:' . max($available, 0)],
        ], [
            'requestAmount.max' => 'You can request at most ₦' . number_format($available, 2) . ' (your available balance).',
        ]);

        do {
            $reference = 'PR-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
        } while (PayoutRequest::where('reference', $reference)->exists());

        PayoutRequest::create([
            'agent_id'       => $this->agent->id,
            'amount'         => (float) $this->requestAmount,
            'reference'      => $reference,
            'bank_name'      => $this->agent->bank_name,
            'account_number' => $this->agent->account_number,
            'account_name'   => $this->agent->account_name,
            'status'         => 'pending',
        ]);

        $this->closeRequest();
        $this->dispatch('payout-notify', message: "Withdrawal request {$reference} submitted — you'll be notified once it's processed.", type: 'success');
    }

    private function hasBank(): bool
    {
        return filled($this->agent->bank_name) && filled($this->agent->account_number) && filled($this->agent->account_name);
    }

    private function availableBalance(): float
    {
        $totalEarned = (float) CommissionTable::where('agent_id', $this->agent->id)
            ->where('status', 'paid')
            ->sum('agent_fee');

        $totalRequested = (float) PayoutRequest::where('agent_id', $this->agent->id)
            ->whereIn('status', ['pending', 'processing', 'paid'])
            ->sum('amount');

        return max($totalEarned - $totalRequested, 0);
    }

    public function render()
    {
        $totalDeals = CommissionTable::where('agent_id', $this->agent->id)->count();

        $available = $this->availableBalance();

        $withdrawn = (float) PayoutRequest::where('agent_id', $this->agent->id)->where('status', 'paid')->sum('amount');

        $pending = (float) PayoutRequest::where('agent_id', $this->agent->id)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        $hasBank = $this->hasBank();

        $requests = PayoutRequest::where('agent_id', $this->agent->id)
            ->latest()
            ->paginate(15);

        return view('livewire.agent.payments', compact('totalDeals', 'available', 'withdrawn', 'pending', 'hasBank', 'requests'));
    }
}
