<div class="space-y-5">

    {{-- Toast --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:agency-pay-notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 5000)"
         x-show="show" x-cloak x-transition
         :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed top-5 right-5 z-50 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg max-w-sm">
        <span x-text="message"></span>
    </div>

    {{-- Withdrawal Modal --}}
    @if($withdrawOpen)
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         x-data x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Request Withdrawal</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Available balance: <span class="text-emerald-600 font-bold">₦{{ number_format($totals['available'], 2) }}</span></p>
                </div>
                <button wire:click="closeWithdraw" class="p-2 rounded-xl text-gray-400 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                @if($totals['available'] <= 0)
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                    <p class="text-sm text-amber-800 font-medium">No available balance to withdraw.</p>
                    <p class="text-xs text-amber-600 mt-1">Your balance becomes available once deal commissions are marked as paid by admin.</p>
                </div>
                @else
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Amount (₦)</label>
                    <input wire:model="withdrawAmount" type="number" step="0.01" min="1" max="{{ $totals['available'] }}"
                           placeholder="Enter amount to withdraw"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('withdrawAmount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Max: ₦{{ number_format($totals['available'], 2) }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Bank Name</label>
                    <input wire:model="withdrawBank" type="text" placeholder="e.g. First Bank"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('withdrawBank') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Account Number</label>
                        <input wire:model="withdrawAccount" type="text" placeholder="10-digit number"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('withdrawAccount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Account Name</label>
                        <input wire:model="withdrawName" type="text" placeholder="Name on account"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('withdrawName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                @if(!$agency->bank_name)
                <p class="text-xs text-blue-600 bg-blue-50 border border-blue-200 rounded-xl px-3 py-2">
                    Tip: Save your bank details in <a href="{{ route('agency.settings') }}" class="font-semibold underline">Settings</a> so they pre-fill automatically next time.
                </p>
                @endif
                @endif
            </div>
            <div class="px-6 pb-6 flex items-center justify-end gap-3">
                <button wire:click="closeWithdraw" type="button"
                        class="px-5 py-2.5 border border-gray-200 text-sm font-semibold text-gray-600 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                @if($totals['available'] > 0)
                <button wire:click="requestWithdrawal" wire:loading.attr="disabled" wire:target="requestWithdrawal"
                        class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition-colors disabled:opacity-60 inline-flex items-center gap-2">
                    <svg wire:loading wire:target="requestWithdrawal" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Submit Request
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total Override</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($totals['total'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">All-time earnings</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Settled</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">₦{{ number_format($totals['paid'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $totals['count_paid'] }} deal{{ $totals['count_paid'] !== 1 ? 's' : '' }} paid out</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">In Pipeline</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">₦{{ number_format($totals['pending'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Pending &amp; processing</p>
        </div>
        <div class="stat-card border-2 border-primary-100">
            <p class="text-xs text-primary-600 font-semibold uppercase tracking-wide">Available to Withdraw</p>
            <p class="text-2xl font-bold text-primary-700 mt-1">₦{{ number_format($totals['available'], 2) }}</p>
            <button wire:click="openWithdraw"
                    class="mt-2 w-full py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-lg transition-colors">
                Request Withdrawal
            </button>
        </div>
    </div>

    {{-- Withdrawal Request History --}}
    @if($withdrawals->count())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-base font-semibold text-gray-900">Withdrawal Requests</h2>
            <p class="text-sm text-gray-400">Track the status of your payout requests</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reference</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Bank Details</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Requested</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($withdrawals as $wd)
                    @php
                        $wdBadge = match($wd->status) {
                            'paid'       => 'bg-emerald-50 text-emerald-700',
                            'processing' => 'bg-blue-50 text-blue-700',
                            'rejected'   => 'bg-red-50 text-red-700',
                            default      => 'bg-amber-50 text-amber-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-lg">{{ $wd->reference }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-bold text-gray-900">₦{{ number_format($wd->amount, 2) }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-gray-700 text-xs font-medium">{{ $wd->bank_name }}</p>
                            <p class="text-xs text-gray-400">{{ $wd->account_number }} · {{ $wd->account_name }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $wdBadge }}">
                                {{ ucfirst($wd->status) }}
                            </span>
                            @if($wd->admin_note)
                            <p class="text-xs text-gray-400 mt-1 italic">{{ $wd->admin_note }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-gray-500 text-xs">
                            {{ $wd->created_at->format('M j, Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Commission History --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Commission Records</h2>
                <p class="text-sm text-gray-400">Override earnings from your network's closed deals</p>
            </div>
            <select wire:model.live="statusFilter"
                    class="text-xs border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                <option value="all">All Statuses</option>
                <option value="paid">Paid</option>
                <option value="processing">Processing</option>
                <option value="pending">Pending</option>
                <option value="disputed">Disputed</option>
            </select>
        </div>

        @if($payments->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/70">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Deal Ref</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Property</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Realtor</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Override Amount</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Deal Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($payments as $payment)
                        @php
                            $statusBadge = match($payment->status) {
                                'paid'       => 'bg-emerald-50 text-emerald-700',
                                'processing' => 'bg-blue-50 text-blue-700',
                                'disputed'   => 'bg-red-50 text-red-700',
                                default      => 'bg-amber-50 text-amber-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-lg">{{ $payment->deal_reference ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-900 truncate max-w-[160px]">{{ $payment->property?->Title ?? 'Unlisted Property' }}</p>
                                <p class="text-xs text-gray-400">₦{{ number_format($payment->total_amount, 2) }} total deal</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-gray-700">{{ $payment->listingRealtor?->First_name }} {{ $payment->listingRealtor?->Last_name }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-900">₦{{ number_format($payment->agency_override_amount, 2) }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-xs">
                                {{ $payment->deal_date ? \Carbon\Carbon::parse($payment->deal_date)->format('M j, Y') : '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="p-4 border-t border-gray-100">{{ $payments->links() }}</div>
            @endif
        @else
            <div class="py-16 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                <p class="text-sm">{{ $statusFilter !== 'all' ? 'No ' . $statusFilter . ' records found.' : 'No commission records yet. Override earnings appear here as deals are closed.' }}</p>
            </div>
        @endif
    </div>
</div>
