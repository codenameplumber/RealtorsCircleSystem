<div class="space-y-6" x-data="{}">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Agency Partners</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create and manage agency accounts. Each agency gets a unique referral link to onboard realtors.</p>
        </div>
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Agency
        </button>
    </div>

    {{-- Stat Board --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Agencies</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['active']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Suspended / Pending</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ number_format($stats['suspended']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Agency Realtors</p>
            <p class="text-2xl font-bold text-violet-600 mt-1">{{ number_format($stats['total_realtors']) }}</p>
        </div>
    </div>

    {{-- Agency List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Agency List</h2>
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live="search" type="text" placeholder="Search by company, email or contact name…"
                           class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-gray-50"/>
                </div>
                <select wire:model.live="statusFilter"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 min-w-[140px]">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="suspended">Suspended</option>
                </select>
                <select wire:model.live="stateFilter"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 min-w-[150px]">
                    <option value="">All States</option>
                    @foreach($states as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Agency</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Contact</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Location</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Referral Code</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Realtors</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($agencies as $agency)
                    <tr wire:key="agt-{{ $agency->id }}" class="hover:bg-gray-50/50 transition-colors">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-emerald-700">{{ strtoupper(substr($agency->company_name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $agency->company_name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[170px]">{{ $agency->email }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <p class="text-gray-700 font-medium">{{ $agency->first_name }} {{ $agency->last_name }}</p>
                            <p class="text-xs text-gray-400">{{ $agency->phone ?? '—' }}</p>
                        </td>

                        <td class="px-5 py-4">
                            <p class="text-gray-700 text-xs font-medium">{{ $agency->state ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $agency->lga ?? '' }}</p>
                        </td>

                        <td class="px-5 py-4">
                            @php $refUrl = url('/register?ref=' . $agency->referral_code); @endphp
                            <div class="flex items-center gap-1.5" x-data="{ copied: false }" x-on:click.stop>
                                <code class="text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 font-mono">{{ $agency->referral_code }}</code>
                                <button x-on:click="navigator.clipboard.writeText('{{ $refUrl }}').then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                                        class="p-1 rounded-lg hover:bg-emerald-50 text-gray-400 hover:text-emerald-600 transition-colors"
                                        :title="copied ? 'Copied!' : 'Copy link'">
                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <svg x-show="copied" class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full text-xs font-bold
                                {{ $agency->realtors_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">
                                {{ $agency->realtors_count }}
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            @php
                                $badge = match($agency->status) {
                                    'active'    => 'bg-emerald-50 text-emerald-700',
                                    'pending'   => 'bg-amber-50 text-amber-700',
                                    'suspended' => 'bg-red-50 text-red-600',
                                    default     => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($agency->status) }}
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <button wire:click="viewProfile({{ $agency->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="viewProfile({{ $agency->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 disabled:opacity-60 px-3 py-1.5 rounded-lg transition-colors">
                                    <svg wire:loading wire:target="viewProfile({{ $agency->id }})" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    View
                                </button>
                                <button wire:click="toggleStatus({{ $agency->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleStatus({{ $agency->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors disabled:opacity-60
                                        {{ $agency->status === 'active' ? 'text-red-700 bg-red-50 hover:bg-red-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                    <svg wire:loading wire:target="toggleStatus({{ $agency->id }})" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    {{ $agency->status === 'active' ? 'Suspend' : 'Activate' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <p class="text-gray-400 text-sm font-medium">No agencies found</p>
                                <p class="text-gray-300 text-xs">Try adjusting your filters or add a new agency</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($agencies->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">
            {{ $agencies->links() }}
        </div>
        @endif
    </div>

    {{-- ============================== ADD AGENCY MODAL ============================== --}}
    @if($createOpen)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:key="add-modal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Add Agency Partner</h2>
                    <p class="text-xs text-gray-400 mt-0.5">A welcome email with login credentials is sent automatically.</p>
                </div>
                <button wire:click="closeCreate" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Company / Agency Name *</label>
                    <input wire:model="companyName" type="text" placeholder="e.g. Premier Property Group"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('companyName') border-red-300 @enderror"/>
                    @error('companyName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contact First Name *</label>
                        <input wire:model="firstName" type="text" placeholder="e.g. Emeka"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('firstName') border-red-300 @enderror"/>
                        @error('firstName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contact Last Name *</label>
                        <input wire:model="lastName" type="text" placeholder="e.g. Okonkwo"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('lastName') border-red-300 @enderror"/>
                        @error('lastName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Address *</label>
                    <input wire:model="email" type="email" placeholder="agency@example.com"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-300 @enderror"/>
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone <span class="text-gray-300 font-normal">(optional)</span></label>
                    <input wire:model="phone" type="tel" placeholder="+234 800 000 0000"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Initial Password *</label>
                    <div class="relative">
                        <input wire:model="password" :type="show ? 'text' : 'password'" placeholder="Min. 8 characters"
                               class="w-full px-3.5 py-2.5 pr-10 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('password') border-red-300 @enderror"/>
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Sent to the agency via email. They should reset it on first login.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">State *</label>
                    <select wire:model.live="state"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white @error('state') border-red-300 @enderror">
                        <option value="">Select State</option>
                        @foreach($states as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('state') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">LGA / Area *</label>
                    <select wire:model="lga"
                            @if(!$state) disabled @endif
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white disabled:opacity-50 disabled:cursor-not-allowed @error('lga') border-red-300 @enderror">
                        <option value="">{{ $state ? 'Select LGA' : 'Select a state first' }}</option>
                        @foreach($lgas as $l)
                            <option value="{{ $l }}">{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('lga') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-start gap-2.5 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-emerald-800">A unique referral code (AGY-XXXXXXXX) is generated automatically. The agency receives it in their welcome email and can access it from their portal dashboard.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                <button wire:click="closeCreate"
                        wire:loading.attr="disabled"
                        wire:target="create"
                        class="text-sm font-semibold text-gray-600 hover:text-gray-800 px-5 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-100 transition-colors disabled:opacity-60">
                    Cancel
                </button>
                <button wire:click="create"
                        wire:loading.attr="disabled"
                        wire:target="create"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-70 px-6 py-2.5 rounded-xl shadow-sm transition-colors">
                    <svg wire:loading wire:target="create" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="create">Create Agency</span>
                    <span wire:loading wire:target="create">Creating & emailing…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================== PROFILE MODAL ============================== --}}
    @if($showProfile && $profileAgency)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:key="profile-modal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Agency Profile</h2>
                <button wire:click="closeProfile" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                {{-- Agency info --}}
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-lg font-bold text-emerald-700">{{ strtoupper(substr($profileAgency->company_name, 0, 2)) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="text-xl font-bold text-gray-900">{{ $profileAgency->company_name }}</h3>
                            @php
                                $sb = match($profileAgency->status) {
                                    'active'    => 'bg-emerald-50 text-emerald-700',
                                    'suspended' => 'bg-red-50 text-red-700',
                                    default     => 'bg-amber-50 text-amber-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $sb }}">
                                {{ ucfirst($profileAgency->status) }}
                            </span>
                        </div>
                        <div class="mt-2 space-y-1 text-sm text-gray-600">
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $profileAgency->first_name }} {{ $profileAgency->last_name }}
                            </p>
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $profileAgency->email }}
                            </p>
                            @if($profileAgency->phone)
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $profileAgency->phone }}
                            </p>
                            @endif
                            @if($profileAgency->state)
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $profileAgency->lga ? $profileAgency->lga . ', ' : '' }}{{ $profileAgency->state }}
                            </p>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Joined {{ $profileAgency->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                {{-- Stat cards --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-emerald-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-emerald-700">{{ number_format($profileAgency->realtors_count) }}</p>
                        <p class="text-xs text-emerald-600 mt-1">Realtors in Network</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <p class="text-sm font-mono font-bold text-gray-700">{{ $profileAgency->referral_code }}</p>
                        <p class="text-xs text-gray-500 mt-1">Referral Code</p>
                    </div>
                </div>

                {{-- Referral link --}}
                @php $refUrl = url('/register?ref=' . $profileAgency->referral_code); @endphp
                <div class="bg-gray-50 rounded-xl px-4 py-3" x-data="{ copied: false }">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Referral Registration Link</p>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 text-xs text-gray-600 font-mono truncate">{{ $refUrl }}</code>
                        <button x-on:click="navigator.clipboard.writeText('{{ $refUrl }}').then(() => { copied = true; setTimeout(() => copied = false, 2500); })"
                                class="flex-shrink-0 inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                                :class="copied ? 'bg-emerald-100 text-emerald-700' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-100'">
                            <svg x-show="!copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <svg x-show="copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                <button wire:click="toggleStatus({{ $profileAgency->id }})"
                        wire:loading.attr="disabled"
                        wire:target="toggleStatus({{ $profileAgency->id }})"
                        class="inline-flex items-center gap-2 text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors disabled:opacity-60
                        {{ $profileAgency->status === 'active' ? 'text-red-700 bg-red-50 hover:bg-red-100 border border-red-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }}">
                    <svg wire:loading wire:target="toggleStatus({{ $profileAgency->id }})" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span wire:loading.remove wire:target="toggleStatus({{ $profileAgency->id }})">
                        {{ $profileAgency->status === 'active' ? 'Suspend Agency' : 'Reactivate Agency' }}
                    </span>
                    <span wire:loading wire:target="toggleStatus({{ $profileAgency->id }})">Updating…</span>
                </button>
                <button wire:click="closeProfile"
                        class="text-sm font-semibold text-gray-600 hover:text-gray-800 px-5 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-100 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
