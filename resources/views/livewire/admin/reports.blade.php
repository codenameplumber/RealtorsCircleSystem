<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
            <p class="text-sm text-gray-500 mt-0.5">Platform-wide insights and performance data</p>
        </div>

        {{-- Filters --}}
        <div class="flex items-center gap-3 flex-wrap">
            <select wire:model.live="stateFilter"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[160px]">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state }}">{{ $state }}</option>
                @endforeach
            </select>
            <select wire:model.live="lgaFilter"
                    @if(!$stateFilter) disabled @endif
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[160px] disabled:opacity-50 disabled:cursor-not-allowed">
                <option value="">{{ $stateFilter ? 'All LGAs' : 'Select a state first' }}</option>
                @foreach($lgas as $lga)
                    <option value="{{ $lga }}">{{ $lga }}</option>
                @endforeach
            </select>
            <select wire:model.live="dateRange"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[140px]">
                <option value="all">All Time</option>
                <option value="year">This Year</option>
                <option value="month">This Month</option>
            </select>
        </div>
    </div>

    {{-- ============================== ROW 1: 6 STAT CARDS ============================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Properties</p>
                <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_properties'] ?? 0) }}</p>
            <div class="flex items-center gap-1 mt-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                <span class="text-xs text-gray-500">{{ number_format($stats['approved_properties'] ?? 0) }} approved</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Approved</p>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-emerald-700">{{ number_format($stats['approved_properties'] ?? 0) }}</p>
            @php
                $approvalRate = ($stats['total_properties'] ?? 0) > 0
                    ? round((($stats['approved_properties'] ?? 0) / $stats['total_properties']) * 100, 1)
                    : 0;
            @endphp
            <p class="text-xs text-gray-500 mt-2">{{ $approvalRate }}% approval rate</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending Listings</p>
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-amber-600">{{ number_format($stats['pending_properties'] ?? 0) }}</p>
            <p class="text-xs text-amber-600 font-medium mt-2">Awaiting review</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Closed Deals</p>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['closed_deals'] ?? 0) }}</p>
            <p class="text-xs text-gray-500 mt-2">Transactions completed</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Closed Value</p>
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['closed_value'] ?? 0, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">Total transaction value</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-white/70 uppercase tracking-wider">Platform Revenue</p>
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-white">₦{{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
            <p class="text-xs text-white/70 mt-2">Net platform earnings</p>
        </div>

    </div>

    {{-- ============================== ROW 2: 4 STAT CARDS ============================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Realtors</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_realtors'] ?? 0) }}</p>
            <div class="flex items-center gap-1 mt-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                <span class="text-xs text-gray-500">{{ number_format($stats['active_realtors'] ?? 0) }} active</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Agents</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_agents'] ?? 0) }}</p>
            <div class="flex items-center gap-1 mt-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 inline-block"></span>
                <span class="text-xs text-gray-500">{{ number_format($stats['active_agents'] ?? 0) }} active</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pending Deals</p>
            <p class="text-3xl font-bold text-amber-600">{{ number_format($stats['pending_deals'] ?? 0) }}</p>
            <p class="text-xs text-amber-600 font-medium mt-2">In progress</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pending Value</p>
            <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['pending_value'] ?? 0, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">Deals in pipeline</p>
        </div>

    </div>

    {{-- ============================== CHARTS ============================== --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Monthly Closed Deals Chart --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
             wire:key="chart-deals-{{ $stateFilter }}-{{ $lgaFilter }}-{{ $dateRange }}">
            <div class="mb-5">
                <h3 class="text-base font-semibold text-gray-900">Monthly Closed Deals</h3>
                <p class="text-sm text-gray-400">Jan — Dec deal closures</p>
            </div>
            <div
                x-data="{
                    chart: null,
                    renderChart() {
                        if (typeof Chart === 'undefined' || !this.$refs.dealsCanvas) { return; }
                        const ctx = this.$refs.dealsCanvas.getContext('2d');
                        if (this.chart) { this.chart.destroy(); this.chart = null; }
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                                datasets: [{
                                    label: 'Deals Closed',
                                    data: {{ json_encode(array_values($monthlyDeals)) }},
                                    backgroundColor: 'rgba(5,150,105,0.15)',
                                    borderColor: '#059669',
                                    borderWidth: 2,
                                    borderRadius: 6,
                                    borderSkipped: false,
                                    hoverBackgroundColor: 'rgba(5,150,105,0.3)',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: '#1f2937',
                                        titleColor: '#f9fafb',
                                        bodyColor: '#d1d5db',
                                        padding: 10,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: function(context) {
                                                return ' ' + context.parsed.y + ' deals';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 } } },
                                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { color: '#9ca3af', font: { size: 11 }, stepSize: 1 } }
                                }
                            }
                        });
                    }
                }"
                x-init="$nextTick(() => renderChart())"
            >
                <canvas x-ref="dealsCanvas" height="100"></canvas>
            </div>
        </div>

        {{-- Monthly Realtor Registrations Chart --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
             wire:key="chart-realtors-{{ $stateFilter }}-{{ $lgaFilter }}-{{ $dateRange }}">
            <div class="mb-5">
                <h3 class="text-base font-semibold text-gray-900">Monthly Realtor Registrations</h3>
                <p class="text-sm text-gray-400">Jan — Dec new realtor sign-ups</p>
            </div>
            <div
                x-data="{
                    chart: null,
                    renderChart() {
                        if (typeof Chart === 'undefined' || !this.$refs.realtorsCanvas) { return; }
                        const ctx = this.$refs.realtorsCanvas.getContext('2d');
                        if (this.chart) { this.chart.destroy(); this.chart = null; }
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                                datasets: [{
                                    label: 'Realtors Registered',
                                    data: {{ json_encode(array_values($monthlyRealtors)) }},
                                    backgroundColor: 'rgba(59,130,246,0.15)',
                                    borderColor: '#3b82f6',
                                    borderWidth: 2,
                                    borderRadius: 6,
                                    borderSkipped: false,
                                    hoverBackgroundColor: 'rgba(59,130,246,0.3)',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: '#1f2937',
                                        titleColor: '#f9fafb',
                                        bodyColor: '#d1d5db',
                                        padding: 10,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: function(context) {
                                                return ' ' + context.parsed.y + ' realtors';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 } } },
                                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { color: '#9ca3af', font: { size: 11 }, stepSize: 1 } }
                                }
                            }
                        });
                    }
                }"
                x-init="$nextTick(() => renderChart())"
            >
                <canvas x-ref="realtorsCanvas" height="100"></canvas>
            </div>
        </div>

    </div>

    {{-- ============================== BREAKDOWNS ============================== --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Property Type Breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-base font-semibold text-gray-900">Properties by Type</h3>
                <p class="text-sm text-gray-400">Breakdown of asset types listed</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/70">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Approved</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-32">Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php $typeTotal = $byType->sum('total') ?: 1; @endphp
                        @forelse($byType as $row)
                        <tr wire:key="type-{{ $loop->index }}" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-medium text-gray-800">{{ $row->type ?? $row['type'] ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ number_format($row->total ?? $row['total'] ?? 0) }}</td>
                            <td class="px-5 py-3.5">
                                <span class="text-emerald-700 font-medium">{{ number_format($row->approved ?? $row['approved'] ?? 0) }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @php $pct = round((($row->total ?? $row['total'] ?? 0) / $typeTotal) * 100, 1); @endphp
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 w-8 text-right">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Listing Type Breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-base font-semibold text-gray-900">Properties by Listing Type</h3>
                <p class="text-sm text-gray-400">Sale vs Rent vs Lease breakdown</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/70">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Listing Type</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Count</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-36">Distribution</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php
                            $listingTotal = $byListingType->sum('total') ?: 1;
                            $listingColors = ['bg-emerald-500', 'bg-blue-500', 'bg-violet-500', 'bg-amber-500', 'bg-pink-500'];
                        @endphp
                        @forelse($byListingType as $i => $row)
                        <tr wire:key="listing-type-{{ $loop->index }}" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $listingColors[$i % count($listingColors)] }} flex-shrink-0"></span>
                                    <span class="font-medium text-gray-800">{{ $row->listing_type ?? $row['listing_type'] ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-gray-700">{{ number_format($row->total ?? $row['total'] ?? 0) }}</td>
                            <td class="px-5 py-3.5">
                                @php $lpct = round((($row->total ?? $row['total'] ?? 0) / $listingTotal) * 100, 1); @endphp
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full rounded-full transition-all {{ $listingColors[$i % count($listingColors)] }}" style="width: {{ $lpct }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 w-8 text-right">{{ $lpct }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-gray-400 text-sm">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ============================== CITY STATS ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h3 class="text-base font-semibold text-gray-900">Properties by City</h3>
            <p class="text-sm text-gray-400">Price ranges and listing counts across cities</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">City</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">State</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Properties</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Min Price</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Max Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($byCity as $row)
                    <tr wire:key="city-{{ $loop->index }}" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-gray-800">{{ $row->city ?? $row['city'] ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-600">{{ $row->state ?? $row['state'] ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-700">
                                {{ number_format($row->total ?? $row['total'] ?? 0) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-600 font-medium">
                            ₦{{ number_format($row->min_price ?? $row['min_price'] ?? 0, 2) }}
                        </td>
                        <td class="px-5 py-3.5 text-gray-600 font-medium">
                            ₦{{ number_format($row->max_price ?? $row['max_price'] ?? 0, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                <p class="text-gray-400 text-sm">No city data available</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
