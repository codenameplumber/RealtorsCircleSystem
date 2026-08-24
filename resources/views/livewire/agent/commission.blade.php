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
            <p class="text-xs text-gray-500 mb-1">Total Commission Earned</p>
            <p class="text-2xl font-bold text-primary-600">₦{{ number_format($totalEarned, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Pending Payout</p>
            <p class="text-2xl font-bold text-amber-600">₦{{ number_format($pendingPayout, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Total Deals</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalDeals }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50">
            <h3 class="font-bold text-gray-900 text-sm">Commission History</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Property</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Model</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Deal Amount</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Agent Fee</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Date</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Payment Link</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($commissions as $commission)
                        <tr class="hover:bg-gray-50 align-top">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-900 truncate max-w-[200px]">{{ $commission->property?->title ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ $commission->property?->city }}</p>
                                <p class="text-xs text-gray-300 mt-0.5">Ref: {{ $commission->deal_reference ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 hidden md:table-cell">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $commission->split_model === 1 ? 'bg-indigo-50 text-indigo-700' : 'bg-teal-50 text-teal-700' }}">
                                    {{ $commission->split_model_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-900">₦{{ number_format($commission->total_amount, 2) }}</p>
                                {{-- Full party breakdown — transparency on how the split was computed --}}
                                <div class="mt-2 space-y-0.5 text-xs text-gray-500">
                                    <p>Listing realtor ({{ $commission->listingRealtor?->First_name }} {{ $commission->listingRealtor?->Last_name }}): <span class="font-semibold text-gray-700">₦{{ number_format($commission->listing_realtor_amount, 2) }}</span></p>
                                    @if($commission->referringRealtor)
                                        <p>Referring realtor ({{ $commission->referringRealtor->First_name }} {{ $commission->referringRealtor->Last_name }}): <span class="font-semibold text-gray-700">₦{{ number_format($commission->referring_realtor_amount, 2) }}</span></p>
                                    @endif
                                    <p>Platform fee: <span class="font-semibold text-gray-700">₦{{ number_format($commission->platform_fee, 2) }}</span></p>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-primary-600">₦{{ number_format($commission->agent_fee, 2) }}</p>
                            </td>
                            <td class="px-5 py-4 hidden lg:table-cell">
                                @php
                                    $cBadge = match($commission->status) {
                                        'paid' => 'bg-emerald-50 text-emerald-700',
                                        'processing' => 'bg-blue-50 text-blue-700',
                                        'disputed' => 'bg-red-50 text-red-700',
                                        default => 'bg-amber-50 text-amber-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $cBadge }}">{{ ucfirst($commission->status) }}</span>
                            </td>
                            <td class="px-5 py-4 text-gray-400 text-xs hidden lg:table-cell">
                                {{ $commission->deal_date?->format('M j, Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <button type="button" wire:click="generatePaymentLink({{ $commission->id }})"
                                        wire:loading.attr="disabled" wire:target="generatePaymentLink({{ $commission->id }})"
                                        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors disabled:opacity-60">
                                    {{ $commission->payment_token ? 'Copy Link' : 'Generate Link' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm font-medium">No commission records yet</p>
                                <p class="text-xs mt-1 text-gray-300">Commission is recorded when you facilitate a closed deal</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($commissions->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $commissions->links() }}</div>
        @endif
    </div>
</div>
