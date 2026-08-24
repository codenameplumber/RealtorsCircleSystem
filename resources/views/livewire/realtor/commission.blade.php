<div>
    {{-- Toast for link-copy feedback --}}
    <div x-data="{ show: false, message: '' }"
         x-on:copy-payment-link.window="
            navigator.clipboard.writeText($event.detail.url).then(() => {
                message = 'Payment link copied to clipboard';
                show = true; setTimeout(() => show = false, 3500);
            })
         "
         x-show="show" x-cloak x-transition
         class="fixed top-5 right-5 z-50 bg-gray-900 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg max-w-sm">
        <span x-text="message"></span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Total Earned</p>
            <p class="text-2xl font-bold text-primary-600">₦{{ number_format($totalEarned, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Pending Payout</p>
            <p class="text-2xl font-bold text-amber-600">₦{{ number_format($pendingAmount, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Total Deals</p>
            <p class="text-2xl font-bold text-gray-900">{{ $commissions->total() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="font-bold text-gray-900 text-sm">Commission History</h3>
            <div class="relative w-full sm:w-72">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search by property or deal ref..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50">
            </div>
        </div>

        @if($commissions->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Property</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Your Role</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Model</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Total Deal</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Your Share</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Date</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Payment Link</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($commissions as $c)
                            @php
                                $isListing = $c->listing_realtor_id == $userId;
                                $share     = $isListing ? $c->listing_realtor_amount : $c->referring_realtor_amount;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    <p class="font-medium text-gray-900 text-xs truncate max-w-[180px]">{{ $c->property?->title ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $c->deal_reference ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $isListing ? 'bg-primary-50 text-primary-700' : 'bg-blue-50 text-blue-700' }}">
                                        {{ $isListing ? 'Listing Realtor' : 'Referring Realtor' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $c->split_model === 1 ? 'bg-indigo-50 text-indigo-700' : 'bg-teal-50 text-teal-700' }}">
                                        {{ $c->split_model_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-semibold text-gray-900">₦{{ number_format($c->total_amount, 2) }}</td>
                                <td class="px-5 py-4 font-bold text-primary-600">₦{{ number_format($share, 2) }}</td>
                                <td class="px-5 py-4">
                                    @php
                                        $sBadge = match($c->status) {
                                            'paid' => 'bg-green-100 text-green-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'disputed' => 'bg-red-100 text-red-700',
                                            default => 'bg-amber-100 text-amber-700',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sBadge }}">{{ ucfirst($c->status) }}</span>
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-xs hidden lg:table-cell">{{ $c->deal_date?->format('M j, Y') ?? $c->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-4">
                                    @if($c->payment_token)
                                        <button type="button"
                                                x-data
                                                x-on:click="$dispatch('copy-payment-link', { url: @js(route('payment.show', $c->payment_token)) })"
                                                class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                                            Copy Link
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-300">Not yet generated</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($commissions->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $commissions->links() }}</div>
            @endif
        @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium">No commission records yet.</p>
                <p class="text-xs mt-1 text-gray-300">You'll see deals here once an agent logs a closed transaction involving your listing or referral.</p>
            </div>
        @endif
    </div>
</div>
