<div class="space-y-5">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total Realtors</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_realtors']) }}</p>
            <p class="text-xs text-gray-400 mt-1">In your network</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Closed Deals</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">₦{{ number_format($stats['closed_deals'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Override earned (paid)</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Pending Deals</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">₦{{ number_format($stats['pending_deals'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Override in pipeline</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total Listings</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_properties']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Assets uploaded by network</p>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name or email..."
               class="flex-1 text-sm focus:outline-none bg-transparent text-gray-900 placeholder-gray-400">
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">My Network</h2>
                <p class="text-sm text-gray-400">Realtors who registered via your referral link</p>
            </div>
            <div x-data="{ copied: false }" class="relative">
                <button @click="navigator.clipboard.writeText('{{ url('/register?ref=' . $agency->referral_code) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="text-xs px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors">
                    <span x-show="!copied">Copy Referral Link</span>
                    <span x-show="copied" x-cloak>Copied!</span>
                </button>
            </div>
        </div>

        @if($realtors->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/70">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Realtor</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Contact</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Location</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Listings</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($realtors as $realtor)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-900">{{ $realtor->First_name }} {{ $realtor->Last_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $realtor->Specialization }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-gray-700">{{ $realtor->Email }}</p>
                                    <p class="text-xs text-gray-400">{{ $realtor->Phone }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-gray-700">{{ $realtor->State ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $realtor->Lga ?? '' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                        {{ $realtor->properties_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @php $badge = match($realtor->Status) { 'active' => 'bg-emerald-50 text-emerald-700', 'pending' => 'bg-amber-50 text-amber-700', default => 'bg-red-50 text-red-700' }; @endphp
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ ucfirst($realtor->Status) }}</span>
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-xs">{{ $realtor->created_at?->format('M j, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($realtors->hasPages())
                <div class="p-4 border-t border-gray-100">{{ $realtors->links() }}</div>
            @endif
        @else
            <div class="py-16 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
                <p class="text-sm">{{ $search ? 'No realtors match your search.' : 'No realtors in your network yet. Share your referral link to get started.' }}</p>
            </div>
        @endif
    </div>
</div>
