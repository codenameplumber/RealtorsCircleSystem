<div class="space-y-6">

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

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Commissions</h1>
        <p class="text-sm text-gray-500 mt-0.5">Track all deal commissions and payouts</p>
    </div>

    {{-- ============================== STAT CARDS ============================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5">

        {{-- Total Deals --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-primary-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Deals</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['total_deals'] ?? 0) }}</p>
            </div>
        </div>

        {{-- Total Paid --}}
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 flex items-start gap-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-white/70 uppercase tracking-wider">Total Paid</p>
                <p class="text-2xl font-bold text-white mt-1">₦{{ number_format($summary['total_paid'] ?? 0, 2) }}</p>
            </div>
        </div>

        {{-- Total Pending --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Pending</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($summary['total_pending'] ?? 0, 2) }}</p>
            </div>
        </div>

        {{-- Platform Fees --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Platform Fees</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($summary['platform_fees'] ?? 0, 2) }}</p>
            </div>
        </div>

        {{-- Agent Fees --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Agent Fees</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($summary['agent_fees'] ?? 0, 2) }}</p>
            </div>
        </div>

    </div>

    {{-- ============================== TOP 10 LOCATIONS ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Top 10 Locations by Commission Earned</h2>
                <p class="text-sm text-gray-400">
                    {{ $groupByLga ? 'Highest-grossing LGAs in ' . $topState : 'Highest-grossing states · pick a state to drill into LGAs' }}
                </p>
            </div>
            <select wire:model.live="topState"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[150px]">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state }}">{{ $state }}</option>
                @endforeach
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-12">Rank</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $groupByLga ? 'LGA' : 'State' }}</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gross Commission</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Paid Deals</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topLocations as $i => $loc)
                    <tr wire:key="top-loc-{{ $i }}-{{ $loc->name }}" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold
                                {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : ($i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-50 text-gray-400')) }}">
                                {{ $i + 1 }}
                            </span>
                        </td>
                        <td class="px-5 py-4 font-semibold text-gray-900">{{ $loc->name }}</td>
                        <td class="px-5 py-4 font-bold text-emerald-600">₦{{ number_format($loc->gross, 2) }}</td>
                        <td class="px-5 py-4 text-gray-500">{{ number_format($loc->deals) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <p class="text-gray-400 text-sm font-medium">No paid commissions for this selection yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================== TABLE ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-base font-semibold text-gray-900">Commission Ledger</h2>
            <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                <select wire:model.live="statusFilter"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[140px]">
                    <option value="">All Statuses</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select wire:model.live="stateFilter"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[150px]">
                    <option value="">All States</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}">{{ $state }}</option>
                    @endforeach
                </select>
                <select wire:model.live="lgaFilter"
                        @if(!$stateFilter) disabled @endif
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[160px] disabled:opacity-50 disabled:cursor-not-allowed">
                    <option value="">{{ $stateFilter ? 'All LGAs' : 'Select a state first' }}</option>
                    @foreach($lgas as $lga)
                        <option value="{{ $lga }}">{{ $lga }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Property</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Model</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Listing Realtor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Referring Realtor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Agent Fee</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Payment Link</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($commissions as $commission)
                    <tr wire:key="commission-{{ $commission->id }}" class="hover:bg-gray-50/50 transition-colors">

                        {{-- Property --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 line-clamp-1 max-w-[140px]">
                                        {{ $commission->property->Title ?? $commission->property->Property_name ?? 'Property #' . $commission->property_id }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $commission->property->City ?? '' }}{{ $commission->property->City && $commission->property->State ? ', ' : '' }}{{ $commission->property->State ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Model --}}
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $commission->split_model === 1 ? 'bg-indigo-50 text-indigo-700' : 'bg-teal-50 text-teal-700' }}">
                                {{ $commission->split_model_label }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1">Platform: ₦{{ number_format($commission->platform_fee ?? 0, 2) }}</p>
                        </td>

                        {{-- Listing Realtor --}}
                        <td class="px-5 py-4">
                            @if($commission->listingRealtor)
                            <div>
                                <p class="font-medium text-gray-700 text-xs">{{ $commission->listingRealtor->First_name }} {{ $commission->listingRealtor->Last_name }}</p>
                                <p class="text-xs text-emerald-600 font-semibold mt-0.5">₦{{ number_format($commission->listing_realtor_amount ?? 0, 2) }}</p>
                            </div>
                            @else
                            <span class="text-gray-300 text-sm">—</span>
                            @endif
                        </td>

                        {{-- Referring Realtor --}}
                        <td class="px-5 py-4">
                            @if($commission->referringRealtor)
                            <div>
                                <p class="font-medium text-gray-700 text-xs">{{ $commission->referringRealtor->First_name }} {{ $commission->referringRealtor->Last_name }}</p>
                                <p class="text-xs text-blue-600 font-semibold mt-0.5">₦{{ number_format($commission->referring_realtor_amount ?? 0, 2) }}</p>
                            </div>
                            @else
                            <span class="text-gray-300 text-sm">—</span>
                            @endif
                        </td>

                        {{-- Agent Fee --}}
                        <td class="px-5 py-4">
                            @if($commission->agent)
                            <div>
                                <p class="font-medium text-gray-700 text-xs">{{ $commission->agent->First_name }} {{ $commission->agent->Last_name }}</p>
                                <p class="text-xs text-violet-600 font-semibold mt-0.5">₦{{ number_format($commission->agent_fee ?? 0, 2) }}</p>
                            </div>
                            @else
                            <span class="text-gray-300 text-sm">—</span>
                            @endif
                        </td>

                        {{-- Total --}}
                        <td class="px-5 py-4">
                            <span class="font-bold text-gray-900">₦{{ number_format($commission->total_amount ?? $commission->amount ?? 0, 2) }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4">
                            @php
                                $cStatus = strtolower($commission->status ?? 'pending');
                                $cBadge = match($cStatus) {
                                    'paid', 'completed' => 'bg-emerald-50 text-emerald-700',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'processing' => 'bg-blue-50 text-blue-700',
                                    'cancelled', 'failed' => 'bg-red-50 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $cBadge }}">
                                {{ ucfirst($commission->status ?? 'pending') }}
                            </span>
                        </td>

                        {{-- Payment Link --}}
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
                        <td colspan="8" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="text-gray-400 text-sm font-medium">No commissions found</p>
                                <p class="text-gray-300 text-xs">Commission records will appear here after deals are closed</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($commissions->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">
            {{ $commissions->links() }}
        </div>
        @endif
    </div>

</div>
