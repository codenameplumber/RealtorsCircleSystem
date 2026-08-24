<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Agents</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage all field agents on the platform</p>
        </div>
        <button wire:click="openAdd"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Agent
        </button>
    </div>

    {{-- Flash Message --}}
    @if(session('agent_success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-5 py-3.5 rounded-xl">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('agent_success') }}
    </div>
    @endif

    {{-- Stat Board --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Agents</p>
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
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Inactive / Suspended</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ number_format($stats['inactive']) }}</p>
        </div>
    </div>

    {{-- ============================== SECTION 1: Agent List ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Agent List</h2>
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
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[150px]">
                    <option value="">All States</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}">{{ $state }}</option>
                    @endforeach
                </select>
                <select wire:model.live="lgaFilter"
                        @if(!$stateFilter) disabled @endif
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[160px] disabled:opacity-50 disabled:cursor-not-allowed">
                    <option value="">{{ $stateFilter ? 'All LGAs / Areas' : 'Select a state first' }}</option>
                    @foreach($filterLgas as $lgaOpt)
                        <option value="{{ $lgaOpt }}">{{ $lgaOpt }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Agent</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">State</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">LGA / Area</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Specialization</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($agents as $agent)
                    <tr wire:key="agent-{{ $agent->id }}" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-blue-700">{{ strtoupper(substr($agent->First_name, 0, 1)) }}{{ strtoupper(substr($agent->Last_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $agent->First_name }} {{ $agent->Last_name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[150px]">{{ $agent->Email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $agent->Phone ?? '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $agent->State ?? '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $agent->Assigned_area ?? '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $agent->Specialization ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @php
                                $status = strtolower($agent->Status ?? 'pending');
                                $badge = match($status) {
                                    'active' => 'bg-emerald-50 text-emerald-700',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'suspended', 'inactive' => 'bg-red-50 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($agent->Status ?? 'pending') }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <button wire:click="viewProfile({{ $agent->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="viewProfile({{ $agent->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 disabled:opacity-60 px-3 py-1.5 rounded-lg transition-colors">
                                    <svg wire:loading wire:target="viewProfile({{ $agent->id }})" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    View
                                </button>
                                <button wire:click="toggleStatus({{ $agent->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleStatus({{ $agent->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors disabled:opacity-60
                                        {{ $status === 'active' ? 'text-red-700 bg-red-50 hover:bg-red-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                    <svg wire:loading wire:target="toggleStatus({{ $agent->id }})" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <p class="text-gray-400 text-sm font-medium">No agents found</p>
                                <p class="text-gray-300 text-xs">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($agents->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">
            {{ $agents->links() }}
        </div>
        @endif
    </div>

    {{-- ============================== SECTION 2: Top 10 Performers ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Top 10 Performing Agents</h2>
                <p class="text-sm text-gray-400">Ranked by deals closed</p>
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
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Agent</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">State / LGA</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Deals Closed</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Fees Earned</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topAgents as $i => $agent)
                    <tr wire:key="top-agent-{{ $agent->id }}" class="hover:bg-gray-50/50 transition-colors">
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
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-blue-700">{{ strtoupper(substr($agent->First_name, 0, 1)) }}{{ strtoupper(substr($agent->Last_name, 0, 1)) }}</span>
                                </div>
                                <p class="font-semibold text-gray-800">{{ $agent->First_name }} {{ $agent->Last_name }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-gray-700 text-xs font-medium">{{ $agent->State ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $agent->Assigned_area ?? '' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                {{ number_format($agent->deals_count) }} deals
                            </span>
                        </td>
                        <td class="px-5 py-4 font-semibold text-gray-800">
                            ₦{{ number_format($agent->total_value ?? 0, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <p class="text-gray-400 text-sm">No performance data available</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================== ADD AGENT MODAL ============================== --}}
    @if($showAddModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:key="add-modal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Add New Agent</h2>
                <button wire:click="closeAdd" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">First Name</label>
                        <input wire:model="firstName" type="text" placeholder="e.g. Emeka"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"/>
                        @error('firstName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Last Name</label>
                        <input wire:model="lastName" type="text" placeholder="e.g. Okonkwo"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"/>
                        @error('lastName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Address</label>
                    <input wire:model="email" type="email" placeholder="agent@example.com"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"/>
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
                    <div class="relative">
                        <input wire:model="password"
                               :type="show ? 'text' : 'password'"
                               placeholder="Min. 8 characters"
                               class="w-full px-3.5 py-2.5 pr-10 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"/>
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">This will be emailed to the agent with their welcome message.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone Number</label>
                    <input wire:model="phone" type="tel" placeholder="+234 800 000 0000"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"/>
                    @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">State</label>
                    <select wire:model.live="state"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white">
                        <option value="">Select State</option>
                        @foreach($states as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('state') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">LGA / Assigned Area</label>
                    <select wire:model="lga"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white">
                        <option value="">Select LGA</option>
                        @foreach($lgas as $l)
                            <option value="{{ $l }}">{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('lga') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Specialization</label>
                    <select wire:model="specialization"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white">
                        <option value="">Select Specialization</option>
                        @foreach($specializations as $spec)
                            <option value="{{ $spec }}">{{ $spec }}</option>
                        @endforeach
                    </select>
                    @error('specialization') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                <button wire:click="closeAdd"
                        wire:loading.attr="disabled"
                        wire:target="addAgent"
                        class="text-sm font-semibold text-gray-600 hover:text-gray-800 px-5 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-100 transition-colors disabled:opacity-60">
                    Cancel
                </button>
                <button wire:click="addAgent"
                        wire:loading.attr="disabled"
                        wire:target="addAgent"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-70 px-6 py-2.5 rounded-xl shadow-sm transition-colors">
                    <svg wire:loading wire:target="addAgent" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="addAgent">Save Agent</span>
                    <span wire:loading wire:target="addAgent">Saving & emailing…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================== PROFILE MODAL ============================== --}}
    @if($showProfile && $profileAgent)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:key="profile-modal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Agent Profile</h2>
                <button wire:click="closeProfile" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-6">
                {{-- Agent Info --}}
                <div class="flex items-start gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-xl font-bold text-blue-700">{{ strtoupper(substr($profileAgent->First_name, 0, 1)) }}{{ strtoupper(substr($profileAgent->Last_name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="text-xl font-bold text-gray-900">{{ $profileAgent->First_name }} {{ $profileAgent->Last_name }}</h3>
                            @php
                                $st = strtolower($profileAgent->Status ?? 'pending');
                                $sb = match($st) {
                                    'active' => 'bg-emerald-50 text-emerald-700',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'suspended', 'inactive' => 'bg-red-50 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $sb }}">
                                {{ ucfirst($profileAgent->Status ?? 'pending') }}
                            </span>
                        </div>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-sm text-gray-600">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $profileAgent->Email }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $profileAgent->Phone ?? '—' }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $profileAgent->State ?? '—' }}, {{ $profileAgent->Assigned_area ?? '' }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $profileAgent->Specialization ?? '—' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Joined {{ \Carbon\Carbon::parse($profileAgent->created_at)->format('M d, Y') }}</p>
                    </div>
                </div>

                {{-- Stats Cards --}}
                @if($profileStats)
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($profileStats['area_realtors'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Area Realtors</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($profileStats['area_properties'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Area Properties</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-emerald-700">{{ number_format($profileStats['deals_closed'] ?? 0) }}</p>
                        <p class="text-xs text-emerald-600 mt-1">Deals Closed</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center col-span-2 sm:col-span-1">
                        <p class="text-xl font-bold text-gray-900">₦{{ number_format($profileStats['agent_fee_total'] ?? 0, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total Fees Earned</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-4 text-center col-span-2 sm:col-span-1">
                        <p class="text-2xl font-bold text-amber-700">{{ number_format($profileStats['deals_pending'] ?? 0) }}</p>
                        <p class="text-xs text-amber-600 mt-1">Pending Deals</p>
                    </div>
                </div>
                @endif
            </div>

            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                <button wire:click="toggleStatus({{ $profileAgent->id }})"
                        wire:loading.attr="disabled"
                        wire:target="toggleStatus({{ $profileAgent->id }})"
                        class="inline-flex items-center gap-2 text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors disabled:opacity-60
                        {{ strtolower($profileAgent->Status ?? '') === 'active' ? 'text-red-700 bg-red-50 hover:bg-red-100 border border-red-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }}">
                    <svg wire:loading wire:target="toggleStatus({{ $profileAgent->id }})" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="toggleStatus({{ $profileAgent->id }})">{{ strtolower($profileAgent->Status ?? '') === 'active' ? 'Suspend Agent' : 'Activate Agent' }}</span>
                    <span wire:loading wire:target="toggleStatus({{ $profileAgent->id }})">Updating…</span>
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
