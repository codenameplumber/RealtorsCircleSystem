<?php

namespace App\Livewire\Agency;

use App\Models\AgencyPartner;
use App\Models\CommissionTable;
use App\Models\PayoutRequest;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Payments extends Component
{
    use WithPagination;

    public AgencyPartner $agency;
    public string $statusFilter = 'all';

    // Withdrawal modal
    public bool   $withdrawOpen    = false;
    public string $withdrawAmount  = '';
    public string $withdrawBank    = '';
    public string $withdrawAccount = '';
    public string $withdrawName    = '';

    public function mount(): void
    {
        $this->agency = AgencyPartner::findOrFail(session('agency_id'));
    }

    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function openWithdraw(): void
    {
        // Pre-fill from agency profile if bank details are saved
        $this->withdrawBank    = $this->agency->bank_name ?? '';
        $this->withdrawAccount = $this->agency->account_number ?? '';
        $this->withdrawName    = $this->agency->account_name ?? '';
        $this->withdrawAmount  = '';
        $this->resetErrorBag();
        $this->withdrawOpen = true;
    }

    public function closeWithdraw(): void
    {
        $this->withdrawOpen = false;
        $this->resetErrorBag();
    }

    public function requestWithdrawal(): void
    {
        $available = $this->availableBalance();

        $this->validate([
            'withdrawAmount'  => ['required', 'numeric', 'min:1', 'max:' . $available],
            'withdrawBank'    => ['required', 'string', 'max:100'],
            'withdrawAccount' => ['required', 'string', 'max:20'],
            'withdrawName'    => ['required', 'string', 'max:150'],
        ], [
            'withdrawAmount.max' => "Amount cannot exceed your available balance of ₦" . number_format($available, 2) . '.',
            'withdrawAmount.min' => 'Minimum withdrawal amount is ₦1.',
        ]);

        $reference = 'WDR-' . strtoupper(Str::random(10));
        while (PayoutRequest::where('reference', $reference)->exists()) {
            $reference = 'WDR-' . strtoupper(Str::random(10));
        }

        PayoutRequest::create([
            'agency_id'      => $this->agency->id,
            'amount'         => $this->withdrawAmount,
            'reference'      => $reference,
            'bank_name'      => $this->withdrawBank,
            'account_number' => $this->withdrawAccount,
            'account_name'   => $this->withdrawName,
            'status'         => 'pending',
        ]);

        $this->closeWithdraw();
        $this->dispatch('agency-pay-notify', message: 'Withdrawal request submitted successfully. You will be notified once processed.', type: 'success');
    }

    private function availableBalance(): float
    {
        $totalPaid   = (float) CommissionTable::where('agency_id', $this->agency->id)->where('status', 'paid')->sum('agency_override_amount');
        $alreadySent = (float) PayoutRequest::where('agency_id', $this->agency->id)->whereIn('status', ['pending', 'processing', 'paid'])->sum('amount');
        return max(0, $totalPaid - $alreadySent);
    }

    public function render()
    {
        $payments = CommissionTable::where('agency_id', $this->agency->id)
            ->with(['property', 'listingRealtor'])
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('deal_date')
            ->paginate(15);

        $withdrawals = PayoutRequest::where('agency_id', $this->agency->id)
            ->orderByDesc('created_at')
            ->get();

        $totalPaid = (float) CommissionTable::where('agency_id', $this->agency->id)->where('status', 'paid')->sum('agency_override_amount');

        $totals = [
            'total'      => CommissionTable::where('agency_id', $this->agency->id)->sum('agency_override_amount'),
            'paid'       => $totalPaid,
            'pending'    => CommissionTable::where('agency_id', $this->agency->id)->whereIn('status', ['pending', 'processing'])->sum('agency_override_amount'),
            'count_paid' => CommissionTable::where('agency_id', $this->agency->id)->where('status', 'paid')->count(),
            'available'  => $this->availableBalance(),
        ];

        return view('livewire.agency.payments', compact('payments', 'totals', 'withdrawals'));
    }
}
