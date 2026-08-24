<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Payments</h1>
        <p class="text-sm text-gray-500 mt-0.5">Aggregated view of payout requests by location</p>
    </div>

    {{-- ============================== LOCATION FILTER ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Filter by Location</h2>
            <p class="text-sm text-gray-400">
                @if($stateFilter)
                    Showing payout requests from realtors and agents in {{ $stateFilter }}{{ $lgaFilter ? ' · ' . $lgaFilter : '' }}
                @else
                    Showing all payout requests · select a state to drill in
                @endif
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <select wire:model.live="stateFilter"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[160px]">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state }}">{{ $state }}</option>
                @endforeach
            </select>
            <select wire:model.live="lgaFilter"
                    @if(!$stateFilter) disabled @endif
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[170px] disabled:opacity-50 disabled:cursor-not-allowed">
                <option value="">{{ $stateFilter ? 'All LGAs' : 'Select a state first' }}</option>
                @foreach($lgas as $lga)
                    <option value="{{ $lga }}">{{ $lga }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ============================== STAT CARDS ============================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4">
            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-primary-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Requesters{{ $stateFilter ? ' in ' . $stateFilter : '' }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['requesters']) }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl p-5 flex items-start gap-4 shadow-sm">
            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-white/70 uppercase tracking-wider">Total Requested</p>
                <p class="text-2xl font-bold text-white mt-1">₦{{ number_format($summary['total_requested'], 2) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4">
            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Paid Out</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($summary['total_paid'], 2) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4">
            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending / Processing</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($summary['total_pending'], 2) }}</p>
            </div>
        </div>
    </div>

    {{-- ============================== SPENDING BREAKDOWN ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-base font-semibold text-gray-900">Spending Breakdown{{ $stateFilter ? ' — ' . $stateFilter . ($lgaFilter ? ', ' . $lgaFilter : '') : '' }}</h2>
            <p class="text-sm text-gray-400">Requested amounts grouped by status for the selected area</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Requests</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Share of Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($breakdown as $row)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            @php
                                $bBadge = match($row->status) {
                                    'paid' => 'bg-emerald-50 text-emerald-700',
                                    'processing' => 'bg-blue-50 text-blue-700',
                                    'rejected' => 'bg-red-50 text-red-700',
                                    default => 'bg-amber-50 text-amber-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $bBadge }}">{{ ucfirst($row->status) }}</span>
                        </td>
                        <td class="px-5 py-4 text-gray-700">{{ number_format($row->count) }}</td>
                        <td class="px-5 py-4 font-bold text-gray-900">₦{{ number_format($row->amount, 2) }}</td>
                        <td class="px-5 py-4 text-gray-500">
                            {{ $summary['total_requested'] > 0 ? number_format(($row->amount / $summary['total_requested']) * 100, 1) : '0.0' }}%
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm font-medium">No payout requests for this selection yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================== TOP LOCATIONS ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-base font-semibold text-gray-900">Top Locations by Payout Requested</h2>
            <p class="text-sm text-gray-400">
                {{ $groupByLga ? 'Highest-requesting LGAs in ' . $stateFilter : 'Highest-requesting states · pick a state above to drill into LGAs' }}
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-12">Rank</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $groupByLga ? 'LGA' : 'State' }}</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Requesters</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Requests</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Requested</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($locations as $i => $loc)
                    <tr wire:key="pay-loc-{{ $i }}-{{ $loc->name }}" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold
                                {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : ($i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-50 text-gray-400')) }}">
                                {{ $i + 1 }}
                            </span>
                        </td>
                        <td class="px-5 py-4 font-semibold text-gray-900">{{ $loc->name }}</td>
                        <td class="px-5 py-4 text-gray-500">{{ number_format($loc->requesters) }}</td>
                        <td class="px-5 py-4 text-gray-500">{{ number_format($loc->count) }}</td>
                        <td class="px-5 py-4 font-bold text-primary-600">₦{{ number_format($loc->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm font-medium">No payout requests for this selection yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
