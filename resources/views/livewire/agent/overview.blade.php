<div>
    {{-- ── Toast listener ─────────────────────────────────────────────────── --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:agent-notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 4000)"
         x-show="show" x-cloak x-transition
         :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed top-5 right-5 z-50 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg max-w-sm">
        <span x-text="message"></span>
    </div>

    {{-- ── Location banner ─────────────────────────────────────────────────── --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Your Assigned Location</p>
                <p class="text-base font-bold text-gray-900">{{ $agent->Assigned_area }}, {{ $agent->State }}</p>
                <p class="text-xs text-gray-500">{{ $agent->Specialization }} · <span class="capitalize text-primary-600 font-medium">{{ $agent->Status }}</span></p>
            </div>
        </div>
        <div class="hidden sm:block text-right">
            <p class="text-xs text-gray-400">Agent ID</p>
            <p class="text-sm font-bold text-gray-700">#{{ str_pad($agent->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    {{-- ── Stat cards ───────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Pending Realtors</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_realtors'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting KYC review</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Active Realtors</p>
            <p class="text-2xl font-bold text-primary-600">{{ $stats['active_realtors'] }}</p>
            <p class="text-xs text-gray-400 mt-1">In your LGA</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Total Properties</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_properties'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['pending_properties'] }} pending approval</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-500 mb-1">Pending Appointments</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_appointments'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Need confirmation</p>
        </div>
        <div class="stat-card col-span-2 sm:col-span-2">
            <p class="text-xs text-gray-500 mb-1">Commission Earned</p>
            <p class="text-2xl font-bold text-primary-600">₦{{ number_format($stats['commission_earned'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Your total earnings</p>
        </div>
    </div>

    {{-- ── Two-column feed ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Pending Realtors --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                <h3 class="font-bold text-gray-900 text-sm">Pending Realtors</h3>
                <a href="{{ route('agent.realtors') }}" class="text-xs text-primary-600 font-medium hover:underline">View all →</a>
            </div>
            @forelse($recentRealtors as $realtor)
                <div class="flex items-center gap-3 px-5 py-3.5 {{ !$loop->last ? 'border-b border-gray-50' : '' }} hover:bg-gray-50">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center text-amber-700 text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($realtor->First_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $realtor->First_name }} {{ $realtor->Last_name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $realtor->Email }} · {{ $realtor->Phone }}</p>
                    </div>
                    <span class="text-xs bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full font-semibold flex-shrink-0">KYC Pending</span>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm">No pending realtors</p>
                </div>
            @endforelse
        </div>

        {{-- Pending Appointments --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                <h3 class="font-bold text-gray-900 text-sm">Pending Appointments</h3>
                <a href="{{ route('agent.appointments') }}" class="text-xs text-primary-600 font-medium hover:underline">View all →</a>
            </div>
            @forelse($recentAppointments as $apt)
                <div class="flex items-center gap-3 px-5 py-3.5 {{ !$loop->last ? 'border-b border-gray-50' : '' }} hover:bg-gray-50">
                    <div class="w-9 h-9 bg-primary-50 rounded-xl flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-[9px] font-bold text-primary-700 leading-none">{{ $apt->preferred_date?->format('M') }}</span>
                        <span class="text-sm font-black text-primary-700 leading-none">{{ $apt->preferred_date?->format('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $apt->property?->title }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $apt->name }} · {{ $apt->property?->user?->First_name }} {{ $apt->property?->user?->Last_name }}</p>
                    </div>
                    <span class="text-xs bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full font-semibold flex-shrink-0">Pending</span>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400">
                    <p class="text-sm">No pending appointments</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
