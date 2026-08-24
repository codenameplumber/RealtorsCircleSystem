<div>
    {{-- Period filter --}}
    <div class="flex gap-1 bg-white border border-gray-100 rounded-xl p-1 shadow-sm mb-6 w-fit">
        @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month'] as $key => $label)
            <button wire:click="$set('period', '{{ $key }}')"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $period === $key ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Period stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">

        {{-- New Requests --}}
        <div class="stat-card" wire:loading.class="opacity-60">
            <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l-3-3m3 3l3-3"/>
                </svg>
            </div>
            <p class="text-xs text-gray-500 mb-1">New Requests</p>
            <p class="text-xl font-bold text-amber-600">{{ $periodStats['new_requests'] }}</p>
        </div>

        {{-- Closed Deals --}}
        <div class="stat-card" wire:loading.class="opacity-60">
            <div class="w-9 h-9 bg-primary-50 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-xs text-gray-500 mb-1">Closed Deals</p>
            <p class="text-xl font-bold text-primary-600">{{ $periodStats['closed_deals'] }}</p>
        </div>

        {{-- Properties Approved --}}
        <div class="stat-card" wire:loading.class="opacity-60">
            <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-xs text-gray-500 mb-1">Properties Approved</p>
            <p class="text-xl font-bold text-green-600">{{ $periodStats['approved_properties'] }}</p>
        </div>

        {{-- Realtors Approved --}}
        <div class="stat-card" wire:loading.class="opacity-60">
            <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 9l-2 2-2-2"/>
                </svg>
            </div>
            <p class="text-xs text-gray-500 mb-1">Realtors Approved</p>
            <p class="text-xl font-bold text-blue-600">{{ $periodStats['approved_realtors'] }}</p>
        </div>

        {{-- Commission Earned --}}
        <div class="stat-card col-span-2 sm:col-span-1" wire:loading.class="opacity-60">
            <div class="w-9 h-9 bg-purple-50 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-xs text-gray-500 mb-1">Commission Earned</p>
            <p class="text-xl font-bold text-purple-600">₦{{ number_format($periodStats['commission_earned'], 2) }}</p>
        </div>
    </div>

    {{-- All-time location summary --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h3 class="font-bold text-gray-900">All-Time Location Summary</h3>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            {{-- Active Realtors --}}
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p class="text-2xl font-black text-primary-600">{{ $allTimeStats['total_realtors'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Active Realtors</p>
            </div>

            {{-- Total Properties --}}
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                </div>
                <p class="text-2xl font-black text-gray-900">{{ $allTimeStats['total_properties'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Properties</p>
            </div>

            {{-- Listed Properties --}}
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-2xl font-black text-green-600">{{ $allTimeStats['listed_properties'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Listed Properties</p>
            </div>

            {{-- Deals Closed --}}
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <p class="text-2xl font-black text-amber-600">{{ $allTimeStats['total_deals'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Deals Closed</p>
            </div>

            {{-- Total Commission --}}
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-2xl font-black text-purple-600">₦{{ number_format($allTimeStats['total_commission'], 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Commission</p>
            </div>
        </div>
    </div>
</div>
