<div class="space-y-6">
    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 xl:grid-cols-3 gap-4">
        <div class="stat-card flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Realtors</p>
                <p class="text-2xl font-black text-gray-900 mt-0.5">{{ number_format($stats['total_realtors']) }}</p>
                <p class="text-xs text-gray-400">{{ $stats['active_realtors'] }} active</p>
            </div>
        </div>
        <div class="stat-card flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Network Deals</p>
                <p class="text-2xl font-black text-gray-900 mt-0.5">{{ number_format($stats['total_deals']) }}</p>
                <p class="text-xs text-gray-400">all time</p>
            </div>
        </div>
        <div class="stat-card flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Override Earned</p>
                <p class="text-2xl font-black text-gray-900 mt-0.5">₦{{ number_format($stats['total_override'], 2) }}</p>
                <p class="text-xs text-gray-400">₦{{ number_format($stats['paid_override'], 2) }} paid · ₦{{ number_format($stats['pending_override'], 2) }} pending</p>
            </div>
        </div>
    </div>

    {{-- Top Realtors --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-base font-semibold text-gray-900">Top Realtors by Deals Closed</h2>
            <p class="text-sm text-gray-400">Your highest-performing network members</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-10">#</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Realtor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Deals</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Deal Value</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Your Override</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topRealtors as $i => $r)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : 'bg-gray-50 text-gray-400') }}">{{ $i + 1 }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-900">{{ $r->name }}</p>
                                <p class="text-xs text-gray-400">{{ $r->email }}</p>
                            </td>
                            <td class="px-5 py-4">
                                @php $badge = match($r->status) { 'active' => 'bg-emerald-50 text-emerald-700', 'pending' => 'bg-amber-50 text-amber-700', default => 'bg-red-50 text-red-700' }; @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ ucfirst($r->status) }}</span>
                            </td>
                            <td class="px-5 py-4 font-bold text-gray-900">{{ $r->deal_count }}</td>
                            <td class="px-5 py-4 text-gray-700">₦{{ number_format($r->total_value, 2) }}</td>
                            <td class="px-5 py-4 font-bold text-primary-600">₦{{ number_format($r->your_override, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No deals logged by your network yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
