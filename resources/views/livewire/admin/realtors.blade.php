<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Realtors</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage all realtors on the platform</p>
        </div>
    </div>

    {{-- Stat Board --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Realtors</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
            @if($stateFilter || $lgaFilter)
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $lgaFilter ?: $stateFilter }}</p>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['active']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Suspended / Inactive</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ number_format($stats['suspended']) }}</p>
        </div>
    </div>

    {{-- ============================== SECTION 1: Realtor List ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Realtor List</h2>
            {{-- Filters --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live="search"
                           type="text"
                           placeholder="Search by name, email..."
                           class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50"/>
                </div>
                <select wire:model.live="statusFilter"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[140px]">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="suspended">Suspended</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select wire:model.live="stateFilter"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[160px]">
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
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Realtor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">State</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Specialization</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">KYC</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($realtors as $realtor)
                    <tr wire:key="realtor-{{ $realtor->id }}" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-emerald-700">{{ strtoupper(substr($realtor->First_name, 0, 1)) }}{{ strtoupper(substr($realtor->Last_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $realtor->First_name }} {{ $realtor->Last_name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[160px]">{{ $realtor->Email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $realtor->Phone ?? '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $realtor->State ?? '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $realtor->Specialization ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @php
                                $kyc = strtolower($realtor->kyc_status ?? 'pending');
                                $kycBadge = match($kyc) {
                                    'verified', 'approved' => 'bg-emerald-50 text-emerald-700',
                                    'pending', 'submitted' => 'bg-amber-50 text-amber-700',
                                    'rejected', 'failed' => 'bg-red-50 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $kycBadge }}">
                                {{ ucfirst($realtor->kyc_status ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $status = strtolower($realtor->Status ?? 'pending');
                                $badge = match($status) {
                                    'active', 'approved' => 'bg-emerald-50 text-emerald-700',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'suspended', 'inactive' => 'bg-red-50 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($realtor->Status ?? 'pending') }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <button wire:click="viewProfile({{ $realtor->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="viewProfile({{ $realtor->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors disabled:opacity-60">
                                    <svg wire:loading wire:target="viewProfile({{ $realtor->id }})" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    View
                                </button>
                                <button wire:click="toggleStatus({{ $realtor->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleStatus({{ $realtor->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors disabled:opacity-60
                                        {{ $status === 'active' ? 'text-red-700 bg-red-50 hover:bg-red-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                    <svg wire:loading wire:target="toggleStatus({{ $realtor->id }})" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    {{ $status === 'active' ? 'Suspend' : 'Activate' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-gray-400 text-sm font-medium">No realtors found</p>
                                <p class="text-gray-300 text-xs">Try adjusting your search or filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($realtors->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">
            {{ $realtors->links() }}
        </div>
        @endif
    </div>

    {{-- ============================== SECTION 2: Top 10 Realtors ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Top 10 Performing Realtors</h2>
                <p class="text-sm text-gray-400">Ranked by total deals and earnings</p>
            </div>
            <select wire:model.live="topState"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[160px]">
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
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Realtor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">State</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Specialization</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Deals</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Earned</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topRealtors as $i => $realtor)
                    <tr wire:key="top-realtor-{{ $realtor->id }}" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            @if($i === 0)
                                <span class="text-xl">🥇</span>
                            @elseif($i === 1)
                                <span class="text-xl">🥈</span>
                            @elseif($i === 2)
                                <span class="text-xl">🥉</span>
                            @else
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-xs font-bold text-gray-500">{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-emerald-700">{{ strtoupper(substr($realtor->First_name, 0, 1)) }}{{ strtoupper(substr($realtor->Last_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $realtor->First_name }} {{ $realtor->Last_name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[140px]">{{ $realtor->Email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $realtor->State ?? '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $realtor->Specialization ?? '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                {{ number_format($realtor->total_deals ?? 0) }} deals
                            </span>
                        </td>
                        <td class="px-5 py-4 font-semibold text-gray-800">
                            ₦{{ number_format($realtor->total_earned ?? 0, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <p class="text-gray-400 text-sm">No performance data available</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================== PROFILE MODAL ============================== --}}
    @if($showProfile && $profileRealtor)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:key="realtor-profile-modal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Realtor Profile</h2>
                <button wire:click="closeProfile" wire:loading.attr="disabled" wire:target="closeProfile" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors disabled:opacity-60">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-6">

                {{-- Personal Info --}}
                <div class="flex items-start gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-xl font-bold text-emerald-700">{{ strtoupper(substr($profileRealtor->First_name, 0, 1)) }}{{ strtoupper(substr($profileRealtor->Last_name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="text-xl font-bold text-gray-900">{{ $profileRealtor->First_name }} {{ $profileRealtor->Last_name }}</h3>
                            @php
                                $st = strtolower($profileRealtor->Status ?? 'pending');
                                $sb = match($st) {
                                    'active', 'approved' => 'bg-emerald-50 text-emerald-700',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'suspended', 'inactive' => 'bg-red-50 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                                $kyc = strtolower($profileRealtor->kyc_status ?? 'pending');
                                $kycBadge = match($kyc) {
                                    'verified', 'approved' => 'bg-emerald-50 text-emerald-700',
                                    'pending', 'submitted' => 'bg-amber-50 text-amber-700',
                                    'rejected', 'failed' => 'bg-red-50 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $sb }}">
                                {{ ucfirst($profileRealtor->Status ?? 'pending') }}
                            </span>
                        </div>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-sm text-gray-600">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $profileRealtor->Email }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $profileRealtor->Phone ?? '—' }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $profileRealtor->State ?? '—' }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $profileRealtor->Specialization ?? '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs text-gray-500">KYC Status:</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $kycBadge }}">
                                {{ ucfirst($profileRealtor->kyc_status ?? 'N/A') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Joined {{ \Carbon\Carbon::parse($profileRealtor->created_at)->format('M d, Y') }}</p>
                    </div>
                </div>

                {{-- KYC Documents --}}
                @if($profileRealtor->kyc_front_url || $profileRealtor->kyc_back_url)
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        KYC Documents
                        @if($profileRealtor->kyc_id_type)
                            <span class="normal-case font-normal text-gray-400">— {{ $profileRealtor->kyc_id_type }}</span>
                        @endif
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        @if($profileRealtor->kyc_front_url)
                            <a href="{{ $profileRealtor->kyc_front_link }}" target="_blank">
                                <p class="text-xs text-gray-400 mb-1">Front of ID</p>
                                <img src="{{ $profileRealtor->kyc_front_link }}" alt="ID Front" class="w-full h-40 object-cover rounded-xl border border-gray-100 hover:opacity-90 transition-opacity">
                            </a>
                        @endif
                        @if($profileRealtor->kyc_back_url)
                            <a href="{{ $profileRealtor->kyc_back_link }}" target="_blank">
                                <p class="text-xs text-gray-400 mb-1">Back of ID</p>
                                <img src="{{ $profileRealtor->kyc_back_link }}" alt="ID Back" class="w-full h-40 object-cover rounded-xl border border-gray-100 hover:opacity-90 transition-opacity">
                            </a>
                        @endif
                    </div>
                </div>
                @else
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 text-sm text-amber-700">
                    No KYC documents have been uploaded by this realtor yet.
                </div>
                @endif

                {{-- Bank Information --}}
                @if($profileRealtor->bankInformation)
                <div class="bg-gray-50 rounded-xl p-4">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Bank Information</h4>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Account Name</p>
                            <p class="font-medium text-gray-800 mt-0.5">{{ $profileRealtor->bankInformation->Account_Name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Bank Name</p>
                            <p class="font-medium text-gray-800 mt-0.5">{{ $profileRealtor->bankInformation->Bank_Name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Account Number</p>
                            <p class="font-medium text-gray-800 mt-0.5">{{ $profileRealtor->bankInformation->Bank_Account ?? '—' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Deal Stats --}}
                @if($profileStats)
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Performance Summary</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-xl font-bold text-gray-900">{{ number_format($profileStats['total_properties'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Properties</p>
                        </div>
                        <div class="bg-emerald-50 rounded-xl p-3 text-center">
                            <p class="text-xl font-bold text-emerald-700">{{ number_format($profileStats['approved_properties'] ?? 0) }}</p>
                            <p class="text-xs text-emerald-600 mt-0.5">Approved</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-xl font-bold text-gray-900">{{ number_format($profileStats['total_deals'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Total Deals</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-lg font-bold text-gray-900">₦{{ number_format($profileStats['total_earned'] ?? 0, 0) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Total Earned</p>
                        </div>
                    </div>

                    {{-- Breakdown Table --}}
                    <div class="border border-gray-100 rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50/70">
                                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase">Type</th>
                                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase">Deals</th>
                                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase">Earned</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr>
                                    <td class="px-4 py-2.5 text-gray-700 font-medium">Listing Deals</td>
                                    <td class="px-4 py-2.5 text-gray-600">{{ number_format($profileStats['listing_deals'] ?? 0) }}</td>
                                    <td class="px-4 py-2.5 font-semibold text-gray-800">₦{{ number_format($profileStats['listing_earned'] ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2.5 text-gray-700 font-medium">Referring Deals</td>
                                    <td class="px-4 py-2.5 text-gray-600">{{ number_format($profileStats['referring_deals'] ?? 0) }}</td>
                                    <td class="px-4 py-2.5 font-semibold text-gray-800">₦{{ number_format($profileStats['referring_earned'] ?? 0, 2) }}</td>
                                </tr>
                                <tr class="bg-gray-50/50 font-semibold">
                                    <td class="px-4 py-2.5 text-gray-900">Total</td>
                                    <td class="px-4 py-2.5 text-gray-900">{{ number_format($profileStats['total_deals'] ?? 0) }}</td>
                                    <td class="px-4 py-2.5 text-emerald-700">₦{{ number_format($profileStats['total_earned'] ?? 0, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                <button wire:click="toggleStatus({{ $profileRealtor->id }})"
                        wire:loading.attr="disabled"
                        wire:target="toggleStatus({{ $profileRealtor->id }})"
                        class="inline-flex items-center gap-2 text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors disabled:opacity-60
                        {{ strtolower($profileRealtor->Status ?? '') === 'active' ? 'text-red-700 bg-red-50 hover:bg-red-100 border border-red-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }}">
                    <svg wire:loading wire:target="toggleStatus({{ $profileRealtor->id }})" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="toggleStatus({{ $profileRealtor->id }})">{{ strtolower($profileRealtor->Status ?? '') === 'active' ? 'Suspend Realtor' : 'Activate Realtor' }}</span>
                    <span wire:loading wire:target="toggleStatus({{ $profileRealtor->id }})">Updating…</span>
                </button>
                <button wire:click="closeProfile"
                        wire:loading.attr="disabled"
                        wire:target="closeProfile"
                        class="text-sm font-semibold text-gray-600 hover:text-gray-800 px-5 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-100 transition-colors disabled:opacity-60">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
