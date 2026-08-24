<div class="space-y-6">

    {{-- Header + State/LGA Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Viewing:
                <span class="font-medium text-gray-700">
                    {{ $selectedLga ? "{$selectedLga}, {$selectedState}" : ($selectedState ? $selectedState : 'All Nigeria') }}
                </span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-gray-600">Filter by:</label>
            <select wire:model.live="selectedState"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[160px]">
                <option value="">All Nigeria (State)</option>
                @foreach($states as $state)
                    <option value="{{ $state }}">{{ $state }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedLga"
                    @if(!$selectedState) disabled @endif
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[160px] disabled:opacity-50 disabled:cursor-not-allowed">
                <option value="">{{ $selectedState ? 'All LGAs' : 'Select a state first' }}</option>
                @foreach($lgas as $lga)
                    <option value="{{ $lga }}">{{ $lga }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Stats Grid Row 1 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        {{-- Total Realtors --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Realtors</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_realtors']) }}</p>
                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                        {{ number_format($stats['active_realtors']) }} Active
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>
                        {{ number_format($stats['pending_realtors']) }} Pending
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-50 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>
                        {{ number_format($stats['suspended_realtors']) }} Suspended
                    </span>
                </div>
            </div>
        </div>

        {{-- Total Agents --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Agents</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_agents']) }}</p>
                <div class="flex items-center gap-3 mt-2">
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                        {{ number_format($stats['active_agents']) }} Active
                    </span>
                </div>
            </div>
        </div>

        {{-- Total Properties --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Properties</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_properties']) }}</p>
                <div class="flex items-center gap-3 mt-2">
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                        {{ number_format($stats['approved_properties']) }} Approved
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>
                        {{ number_format($stats['pending_properties']) }} Pending
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- Stats Grid Row 2 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        {{-- Closed Deals --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Closed Deals</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['closed_deals']) }}</p>
                <p class="text-xs text-gray-500 mt-2">Successfully completed transactions</p>
            </div>
        </div>

        {{-- Platform Revenue --}}
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white/70 uppercase tracking-wider">Platform Revenue</p>
                <p class="text-2xl font-bold text-white mt-1">₦{{ number_format($stats['total_revenue'], 2) }}</p>
                <p class="text-xs text-white/70 mt-2">Total earnings collected</p>
            </div>
        </div>

        {{-- Pending Deals --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending Deals</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['pending_deals']) }}</p>
                <p class="text-xs text-amber-600 font-medium mt-2">Awaiting completion</p>
            </div>
        </div>

    </div>

    {{-- Monthly Registrations Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
         wire:key="chart-{{ $selectedState ?: 'all' }}-{{ $selectedLga ?: 'all' }}">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Monthly Registrations</h2>
                <p class="text-sm text-gray-500">Realtors vs Agents — Jan to Dec</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                    Realtors
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                    Agents
                </span>
            </div>
        </div>
        <div
            x-data="{
                chart: null,
                renderChart() {
                    if (typeof Chart === 'undefined' || !this.$refs.canvas) { return; }
                    const ctx = this.$refs.canvas.getContext('2d');
                    if (this.chart) { this.chart.destroy(); this.chart = null; }
                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                            datasets: [
                                {
                                    label: 'Realtors',
                                    data: {{ json_encode(array_values($monthlyRealtors)) }},
                                    borderColor: '#059669',
                                    backgroundColor: 'rgba(5,150,105,0.08)',
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#059669',
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    borderWidth: 2.5
                                },
                                {
                                    label: 'Agents',
                                    data: {{ json_encode(array_values($monthlyAgents)) }},
                                    borderColor: '#3b82f6',
                                    backgroundColor: 'rgba(59,130,246,0.08)',
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#3b82f6',
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    borderWidth: 2.5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1f2937',
                                    titleColor: '#f9fafb',
                                    bodyColor: '#d1d5db',
                                    padding: 10,
                                    cornerRadius: 8
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#9ca3af', font: { size: 12 } }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f3f4f6' },
                                    ticks: { color: '#9ca3af', font: { size: 12 }, stepSize: 1 }
                                }
                            }
                        }
                    });
                }
            }"
            x-init="$nextTick(() => renderChart())"
        >
            <canvas x-ref="canvas" height="90"></canvas>
        </div>
    </div>

    {{-- Recent Tables --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Recent Realtors --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Recent Realtors</h2>
                <span class="text-xs text-gray-400">Last 5 registered</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Specialization</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentRealtors as $realtor)
                        <tr wire:key="rr-{{ $realtor->id ?? $loop->index }}" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-emerald-700">{{ strtoupper(substr($realtor->First_name, 0, 1)) }}{{ strtoupper(substr($realtor->Last_name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $realtor->First_name }} {{ $realtor->Last_name }}</p>
                                        <p class="text-xs text-gray-400 truncate max-w-[120px]">{{ $realtor->Email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-gray-600 text-xs">{{ $realtor->Specialization ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $status = strtolower($realtor->Status ?? 'pending');
                                    $statusClass = match($status) {
                                        'active', 'approved' => 'bg-emerald-50 text-emerald-700',
                                        'pending' => 'bg-amber-50 text-amber-700',
                                        'suspended', 'rejected', 'inactive' => 'bg-red-50 text-red-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ ucfirst($realtor->Status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($realtor->created_at)->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm">No realtors found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Agents --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Recent Agents</h2>
                <span class="text-xs text-gray-400">Last 5 registered</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Area / State</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentAgents as $agent)
                        <tr wire:key="ra-{{ $agent->id ?? $loop->index }}" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-blue-700">{{ strtoupper(substr($agent->First_name, 0, 1)) }}{{ strtoupper(substr($agent->Last_name, 0, 1)) }}</span>
                                    </div>
                                    <p class="font-medium text-gray-800">{{ $agent->First_name }} {{ $agent->Last_name }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="text-gray-700 text-xs font-medium">{{ $agent->Assigned_area ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ $agent->State ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $status = strtolower($agent->Status ?? 'pending');
                                    $statusClass = match($status) {
                                        'active', 'approved' => 'bg-emerald-50 text-emerald-700',
                                        'pending' => 'bg-amber-50 text-amber-700',
                                        'suspended', 'rejected', 'inactive' => 'bg-red-50 text-red-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ ucfirst($agent->Status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($agent->created_at)->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm">No agents found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
