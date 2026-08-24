<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Properties</h1>
            <p class="text-sm text-gray-500 mt-0.5">Review and manage every listing on the platform</p>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('prop_success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-5 py-3.5 rounded-xl">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('prop_success') }}
    </div>
    @endif
    @if(session('prop_error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm font-medium px-5 py-3.5 rounded-xl">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        {{ session('prop_error') }}
    </div>
    @endif

    {{-- Stat Board --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Listings</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
            @if($stateFilter || $lgaFilter)
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $lgaFilter ?: $stateFilter }}</p>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Approved</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['approved']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rejected</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ number_format($stats['rejected']) }}</p>
        </div>
    </div>

    {{-- Filters + Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-base font-semibold text-gray-900 mb-4">All Listings</h2>
            <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live="search"
                           type="text"
                           placeholder="Search title, city, state..."
                           class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50"/>
                </div>
                <select wire:model.live="statusFilter"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[150px]">
                    <option value="">All Statuses</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>
                <select wire:model.live="typeFilter"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent min-w-[140px]">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
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
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Property</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Realtor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Price</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Views</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($properties as $prop)
                    <tr wire:key="prop-{{ $prop->id }}" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-900 truncate max-w-[200px]">{{ $prop->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $prop->city }}, {{ $prop->state }} · {{ $prop->type }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if($prop->user)
                            <p class="text-gray-700 font-medium">{{ $prop->user->First_name }} {{ $prop->user->Last_name }}</p>
                            <p class="text-xs text-gray-400">{{ $prop->user->Email }}</p>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-semibold text-gray-900">₦{{ $prop->price_display }}</td>
                        <td class="px-5 py-4">
                            @php
                                $s = $prop->approval_status ?? 'pending';
                                $badge = match($s) {
                                    'approved' => 'bg-emerald-50 text-emerald-700',
                                    'rejected' => 'bg-red-50 text-red-700',
                                    default    => 'bg-amber-50 text-amber-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($s) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ number_format($prop->views ?? 0) }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($s === 'pending')
                                    <button wire:click="approveProperty({{ $prop->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="approveProperty({{ $prop->id }})"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors disabled:opacity-60">
                                        <svg wire:loading wire:target="approveProperty({{ $prop->id }})" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Approve
                                    </button>
                                    <button wire:click="openReject({{ $prop->id }})"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors disabled:opacity-60">
                                        Reject
                                    </button>
                                @endif
                                <a href="{{ route('property.detail', \App\Helpers\IdHasher::encode($prop->id)) }}"
                                   target="_blank"
                                   class="text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                    View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <p class="text-gray-400 text-sm font-medium">No properties found</p>
                                <p class="text-gray-300 text-xs">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($properties->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">
            {{ $properties->links() }}
        </div>
        @endif
    </div>

    {{-- ============================== Reject Reason Modal ============================== --}}
    @if($rejectOpen)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4" wire:click.self="closeReject">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-1">Reject Property</h3>
            <p class="text-sm text-gray-500 mb-4">Please provide a reason. This will be emailed to the realtor.</p>

            <textarea wire:model="rejectReason"
                      rows="4"
                      placeholder="Explain why this listing is being rejected..."
                      class="w-full text-sm border border-gray-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
            @error('rejectReason')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3 mt-5">
                <button wire:click="closeReject" type="button"
                        class="text-sm font-medium text-gray-600 hover:text-gray-800 px-4 py-2.5 rounded-xl transition-colors">
                    Cancel
                </button>
                <button wire:click="submitReject"
                        wire:loading.attr="disabled"
                        wire:target="submitReject"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 disabled:opacity-60 px-4 py-2.5 rounded-xl transition-colors">
                    <svg wire:loading wire:target="submitReject" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Confirm Rejection
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
