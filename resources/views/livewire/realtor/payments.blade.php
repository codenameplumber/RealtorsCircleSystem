<div>
    {{-- Toast notification --}}
    <div x-data="{ show: false, message: '', type: 'success', timer: null,
            open(msg, t) { this.message = msg; this.type = t || 'success'; this.show = true; clearTimeout(this.timer); this.timer = setTimeout(() => this.show = false, 6000); } }"
         x-on:payout-notify.window="open($event.detail.message, $event.detail.type)"
         x-show="show" x-cloak x-transition
         class="fixed top-5 right-5 z-50 text-sm font-semibold px-5 py-3 rounded-xl shadow-lg max-w-sm border"
         :class="type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'">
        <span x-text="message"></span>
    </div>

    <div class="flex items-start justify-between mb-6 gap-4 flex-wrap">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Payments</h1>
            <p class="text-sm text-gray-500">Request payouts of your earned commission to your bank account</p>
        </div>
        <button wire:click="openRequest"
                class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
            Request Payout
        </button>
    </div>

    @unless($hasBank)
        <div class="mb-6 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>You haven't added your bank account details yet. Add them in Settings before requesting a payout.</span>
        </div>
    @endunless

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Total Deals Closed</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalDeals }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Available</p>
            <p class="text-2xl font-bold text-emerald-600">₦{{ number_format($available, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Withdrawn</p>
            <p class="text-2xl font-bold text-gray-900">₦{{ number_format($withdrawn, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Pending</p>
            <p class="text-2xl font-bold text-amber-600">₦{{ number_format($pending, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50">
            <h3 class="font-bold text-gray-900 text-sm">Payout Requests</h3>
        </div>
        @if($requests->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Reference</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Bank</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Requested</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($requests as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 font-mono text-xs text-gray-500">{{ $r->reference }}</td>
                                <td class="px-5 py-4 font-bold text-gray-900">₦{{ number_format($r->amount, 2) }}</td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <p class="text-xs text-gray-700">{{ $r->bank_name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $r->account_number }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $rBadge = match($r->status) {
                                            'paid' => 'bg-green-100 text-green-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            default => 'bg-amber-100 text-amber-700',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $rBadge }}">{{ ucfirst($r->status) }}</span>
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-xs hidden lg:table-cell">{{ $r->created_at->format('M j, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $requests->links() }}</div>
            @endif
        @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <p class="text-sm font-medium">No payout requests yet.</p>
                <p class="text-xs mt-1 text-gray-300">Request a payout above once you have an available balance.</p>
            </div>
        @endif
    </div>

    {{-- Request Payout Modal --}}
    @if($requestOpen)
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Request Payout</h3>
                    <p class="text-xs text-gray-400 mt-1">Available balance: <span class="font-bold text-emerald-600">₦{{ number_format($available, 2) }}</span></p>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Amount to Withdraw (₦)</label>
                        <input type="number" step="0.01" min="0" wire:model="requestAmount" placeholder="e.g. 150000"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('requestAmount') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button wire:click="closeRequest" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
                        <button wire:click="submitRequest" wire:loading.attr="disabled" wire:target="submitRequest"
                                class="flex-1 py-2.5 text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-colors disabled:opacity-60">
                            <span wire:loading.remove wire:target="submitRequest">Submit Request</span>
                            <span wire:loading wire:target="submitRequest">Submitting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
