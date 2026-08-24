<?php

namespace App\Livewire\Realtor;

use App\Models\BankInformation;
use App\Models\CommissionTable;
use App\Models\PayoutRequest;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Payments extends Component
{
    use WithPagination;

    public bool $requestOpen = false;
    public string $requestAmount = '';

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
        $userId = auth()->id();
        $bank   = BankInformation::where('user_id', $userId)->first();

        if (! $bank) {
            $this->dispatch('payout-notify', message: 'Add your bank account details in Settings before requesting a payout.', type: 'error');
            return;
        }

        $available = $this->availableBalance($userId);

        $this->validate([
            'requestAmount' => ['required', 'numeric', 'min:1', 'max:' . max($available, 0)],
        ], [
            'requestAmount.max' => 'You can request at most ₦' . number_format($available, 2) . ' (your available balance).',
        ]);

        do {
            $reference = 'PR-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
        } while (PayoutRequest::where('reference', $reference)->exists());

        PayoutRequest::create([
            'realtor_id'     => $userId,
            'amount'         => (float) $this->requestAmount,
            'reference'      => $reference,
            'bank_name'      => $bank->Bank_Name,
            'account_number' => $bank->Bank_Account,
            'account_name'   => $bank->Account_Name,
            'status'         => 'pending',
        ]);

        $this->closeRequest();
        $this->dispatch('payout-notify', message: "Payout request {$reference} submitted — you'll be notified once it's processed.", type: 'success');
    }

    private function availableBalance(int $userId): float
    {
        $totalEarned = CommissionTable::where('status', 'paid')
            ->where(fn ($q) => $q->where('listing_realtor_id', $userId)->orWhere('referring_realtor_id', $userId))
            ->get()
            ->sum(fn ($c) => $c->listing_realtor_id == $userId ? $c->listing_realtor_amount : $c->referring_realtor_amount);

        $totalRequested = PayoutRequest::where('realtor_id', $userId)
            ->whereIn('status', ['pending', 'processing', 'paid'])
            ->sum('amount');

        return max($totalEarned - $totalRequested, 0);
    }

    public function render()
    {
        $userId = auth()->id();

        $totalDeals = CommissionTable::where(fn ($q) => $q->where('listing_realtor_id', $userId)->orWhere('referring_realtor_id', $userId))
            ->count();

        $available = $this->availableBalance($userId);

        $withdrawn = (float) PayoutRequest::where('realtor_id', $userId)->where('status', 'paid')->sum('amount');

        $pending = (float) PayoutRequest::where('realtor_id', $userId)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        $hasBank = BankInformation::where('user_id', $userId)->exists();

        $requests = PayoutRequest::where('realtor_id', $userId)
            ->latest()
            ->paginate(15);

        return view('livewire.realtor.payments', compact('totalDeals', 'available', 'withdrawn', 'pending', 'hasBank', 'requests'));
    }
}
