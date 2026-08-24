<div>
    {{-- Toast --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:agent-notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 4000)"
         x-show="show" x-cloak x-transition
         :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed top-5 right-5 z-50 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg max-w-sm">
        <span x-text="message"></span>
    </div>

    {{-- Filter tabs --}}
    <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-4 w-fit">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label)
            <button wire:click="$set('statusFilter', '{{ $key }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $statusFilter === $key ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($appointments->count())
            <div class="divide-y divide-gray-50">
                @foreach($appointments as $apt)
                    <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50">
                        {{-- Date block --}}
                        <div class="w-12 h-12 bg-primary-50 rounded-xl flex flex-col items-center justify-center flex-shrink-0">
                            <span class="text-[9px] font-bold text-primary-700 leading-none">{{ $apt->preferred_date?->format('M') }}</span>
                            <span class="text-xl font-black text-primary-700 leading-none">{{ $apt->preferred_date?->format('d') }}</span>
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $apt->property?->title }}</p>
                            <p class="text-xs text-gray-500">Client: {{ $apt->name }} · {{ $apt->phone }}</p>
                            <p class="text-xs text-gray-400">Realtor: {{ $apt->property?->user?->First_name }} {{ $apt->property?->user?->Last_name }} · {{ $apt->preferred_time }}</p>
                        </div>
                        {{-- Status + actions --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @php
                                $sc = match($apt->status) {
                                    'confirmed'  => 'bg-green-100 text-green-700',
                                    'completed'  => 'bg-gray-100 text-gray-600',
                                    'cancelled'  => 'bg-red-100 text-red-700',
                                    default      => 'bg-amber-100 text-amber-700',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }} hidden sm:inline">{{ ucfirst($apt->status) }}</span>
                            <button wire:click="viewDetail({{ $apt->id }})" wire:loading.attr="disabled" wire:target="viewDetail({{ $apt->id }})"
                                    class="text-xs px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors disabled:opacity-60 inline-flex items-center gap-1.5">
                                <svg wire:loading wire:target="viewDetail({{ $apt->id }})" class="animate-spin w-3.5 h-3.5 text-gray-700" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Details
                            </button>
                            @if($apt->status === 'pending')
                                <button wire:click="confirm({{ $apt->id }})" wire:loading.attr="disabled" wire:target="confirm({{ $apt->id }})"
                                        class="text-xs px-2.5 py-1.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-60 inline-flex items-center gap-1.5">
                                    <svg wire:loading wire:target="confirm({{ $apt->id }})" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Confirm
                                </button>
                            @elseif($apt->status === 'confirmed')
                                <button wire:click="complete({{ $apt->id }})" wire:loading.attr="disabled" wire:target="complete({{ $apt->id }})"
                                        class="text-xs px-2.5 py-1.5 bg-gray-700 hover:bg-gray-800 text-white font-semibold rounded-lg transition-colors disabled:opacity-60 inline-flex items-center gap-1.5">
                                    <svg wire:loading wire:target="complete({{ $apt->id }})" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Done
                                </button>
                            @elseif($apt->status === 'completed')
                                @if(in_array($apt->property_id, $loggedPropertyIds))
                                    <span class="text-xs px-2.5 py-1.5 bg-green-50 text-green-600 font-semibold rounded-lg">Commission Logged ✓</span>
                                @else
                                    <button wire:click="openLogDeal({{ $apt->id }})" wire:loading.attr="disabled" wire:target="openLogDeal({{ $apt->id }})"
                                            class="text-xs px-2.5 py-1.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-60 inline-flex items-center gap-1.5">
                                        <svg wire:loading wire:target="openLogDeal({{ $apt->id }})" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Log Deal
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($appointments->hasPages())
                <div class="p-4 border-t border-gray-100">{{ $appointments->links() }}</div>
            @endif
        @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-sm">No appointments found.</p>
            </div>
        @endif
    </div>

    {{-- ── Detail Modal ─────────────────────────────────────────────────────── --}}
    @if($detailOpen && $viewing)
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-900">Appointment Details</h2>
                    <button wire:click="closeDetail" class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Property --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Property</p>
                        <p class="font-bold text-gray-900">{{ $viewing->property?->title }}</p>
                        <p class="text-sm text-gray-500">{{ $viewing->property?->street }}, {{ $viewing->property?->neigborhood }}, {{ $viewing->property?->city }}</p>
                        <p class="text-sm font-bold text-primary-600 mt-1">₦{{ $viewing->property?->price_display }}</p>
                    </div>

                    {{-- Client + Realtor side by side --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-blue-50 rounded-xl p-4">
                            <p class="text-xs font-semibold text-blue-400 uppercase tracking-wide mb-2">Client</p>
                            <p class="font-bold text-gray-900 text-sm">{{ $viewing->name }}</p>
                            <p class="text-xs text-gray-500">{{ $viewing->email }}</p>
                            <p class="text-xs text-gray-500">{{ $viewing->phone }}</p>
                        </div>
                        <div class="bg-primary-50 rounded-xl p-4">
                            <p class="text-xs font-semibold text-primary-500 uppercase tracking-wide mb-2">Realtor</p>
                            <p class="font-bold text-gray-900 text-sm">{{ $viewing->property?->user?->First_name }} {{ $viewing->property?->user?->Last_name }}</p>
                            <p class="text-xs text-gray-500">{{ $viewing->property?->user?->Email }}</p>
                            <p class="text-xs text-gray-500">{{ $viewing->property?->user?->Phone }}</p>
                        </div>
                    </div>

                    {{-- Schedule info --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] text-gray-400">Date</p>
                            <p class="text-sm font-bold text-gray-900">{{ $viewing->preferred_date?->format('M j, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] text-gray-400">Time</p>
                            <p class="text-sm font-bold text-gray-900">{{ $viewing->preferred_time }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] text-gray-400">Visit Type</p>
                            <p class="text-sm font-bold text-gray-900">{{ str_replace('_', ' ', $viewing->visit_type) }}</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        @if($viewing->status === 'pending')
                            <button wire:click="confirm({{ $viewing->id }})" wire:loading.attr="disabled" wire:target="confirm({{ $viewing->id }})"
                                    class="flex-1 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition-colors disabled:opacity-60 inline-flex items-center justify-center gap-2">
                                <svg wire:loading wire:target="confirm({{ $viewing->id }})" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Confirm Appointment
                            </button>
                            <button wire:click="openCancel({{ $viewing->id }})" wire:loading.attr="disabled" wire:target="openCancel({{ $viewing->id }})"
                                    class="flex-1 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-bold rounded-xl transition-colors disabled:opacity-60 inline-flex items-center justify-center gap-2">
                                <svg wire:loading wire:target="openCancel({{ $viewing->id }})" class="animate-spin w-4 h-4 text-red-700" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Cancel
                            </button>
                        @elseif($viewing->status === 'confirmed')
                            <button wire:click="complete({{ $viewing->id }})" wire:loading.attr="disabled" wire:target="complete({{ $viewing->id }})"
                                    class="flex-1 py-2.5 bg-gray-700 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-colors disabled:opacity-60 inline-flex items-center justify-center gap-2">
                                <svg wire:loading wire:target="complete({{ $viewing->id }})" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Mark as Completed
                            </button>
                            <button wire:click="openCancel({{ $viewing->id }})" wire:loading.attr="disabled" wire:target="openCancel({{ $viewing->id }})"
                                    class="flex-1 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-bold rounded-xl transition-colors disabled:opacity-60 inline-flex items-center justify-center gap-2">
                                <svg wire:loading wire:target="openCancel({{ $viewing->id }})" class="animate-spin w-4 h-4 text-red-700" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Cancel
                            </button>
                        @elseif($viewing->status === 'completed' && in_array($viewing->property_id, $loggedPropertyIds))
                            <div class="flex-1 text-center text-sm text-green-600 font-semibold py-2.5">Commission Logged ✓</div>
                        @elseif($viewing->status === 'completed')
                            <button wire:click="openLogDeal({{ $viewing->id }})" wire:loading.attr="disabled" wire:target="openLogDeal({{ $viewing->id }})"
                                    class="flex-1 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition-colors disabled:opacity-60 inline-flex items-center justify-center gap-2">
                                <svg wire:loading wire:target="openLogDeal({{ $viewing->id }})" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Log Deal
                            </button>
                        @else
                            <div class="flex-1 text-center text-sm text-gray-400 py-2.5">Status: <span class="font-semibold text-gray-600 capitalize">{{ $viewing->status }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Cancel Modal --}}
    @if($cancelOpen)
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Cancel Appointment</h3>
                    <p class="text-xs text-gray-400 mt-1">Both the client and realtor will be notified.</p>
                </div>
                <div class="p-6 space-y-4">
                    <textarea wire:model="cancelReason" rows="3" placeholder="Reason (optional)..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
                    <div class="flex gap-3">
                        <button wire:click="$set('cancelOpen', false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Go Back</button>
                        <button wire:click="submitCancel" wire:loading.attr="disabled" wire:target="submitCancel"
                                class="flex-1 py-2.5 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors disabled:opacity-60 inline-flex items-center justify-center gap-2">
                            <svg wire:loading wire:target="submitCancel" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="submitCancel">Confirm Cancel</span>
                            <span wire:loading wire:target="submitCancel">Cancelling...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Log Deal Modal ─────────────────────────────────────────────────────── --}}
    @if($logDealOpen)
        @php $logDealApt = $appointments->firstWhere('id', $logDealId) ?? \App\Models\PropertyInquiry::with('property.user')->find($logDealId); @endphp
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Log Closed Deal</h3>
                    <p class="text-xs text-gray-400 mt-1">Pick the split model and enter the agreed commission — the system computes and records each party's share.</p>
                </div>
                <div class="p-6 space-y-5">
                    @if($logDealApt)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Property</p>
                            <p class="font-bold text-gray-900 text-sm">{{ $logDealApt->property?->title }}</p>
                            <p class="text-xs text-gray-500">Listing realtor: {{ $logDealApt->property?->user?->First_name }} {{ $logDealApt->property?->user?->Last_name }}</p>
                        </div>
                    @endif

                    {{-- Model selection --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Split Model</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="cursor-pointer border rounded-xl p-3 transition-colors {{ $dealModel === 1 ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="dealModel" value="1" class="sr-only">
                                <p class="text-sm font-bold text-gray-900">Model 1 — Standard</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Listing {{ $splitSettings->m1_listing_pct }}% · Referring {{ $splitSettings->m1_referring_pct }}% ·
                                    Agent {{ $splitSettings->m1_agent_pct }}% · Platform {{ $splitSettings->m1_platform_pct }}%
                                </p>
                            </label>
                            <label class="cursor-pointer border rounded-xl p-3 transition-colors {{ $dealModel === 2 ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="dealModel" value="2" class="sr-only">
                                <p class="text-sm font-bold text-gray-900">Model 2 — Direct</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Listing {{ $splitSettings->m2_listing_pct }}% ·
                                    Agent {{ $splitSettings->m2_agent_pct }}% · Platform {{ $splitSettings->m2_platform_pct }}%
                                </p>
                            </label>
                        </div>
                        @error('dealModel') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- Referring realtor (Model 1 only) — search by email/phone, then confirm identity --}}
                    @if($dealModel === 1)
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Referring Realtor</label>

                            @if(! $referringMatch)
                                <div class="flex gap-2">
                                    <input type="text" wire:model="referringSearch" wire:keydown.enter.prevent="searchReferringRealtor"
                                           placeholder="Enter their email or phone number..."
                                           class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <button type="button" wire:click="searchReferringRealtor" wire:loading.attr="disabled" wire:target="searchReferringRealtor"
                                            class="px-4 py-2.5 text-sm font-semibold text-white bg-gray-700 hover:bg-gray-800 rounded-xl transition-colors disabled:opacity-60 inline-flex items-center justify-center gap-2">
                                        <svg wire:loading wire:target="searchReferringRealtor" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Search
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5">Look them up to confirm their identity before attributing the referral — avoids picking the wrong person.</p>

                                @if($referringSearched)
                                    <p class="text-xs text-red-500 mt-1.5">No active realtor found with that email or phone number. Double-check and try again.</p>
                                @endif
                            @else
                                <div class="border border-emerald-200 bg-emerald-50 rounded-xl p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide mb-1">Match Found — Confirm Identity</p>
                                            <p class="font-bold text-gray-900 text-sm">{{ $referringMatch['name'] }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $referringMatch['email'] }} · {{ $referringMatch['phone'] }}</p>
                                        </div>
                                        <button type="button" wire:click="clearReferringMatch" class="text-xs px-3 py-1.5 bg-white hover:bg-gray-100 text-gray-600 font-semibold rounded-lg border border-gray-200 transition-colors flex-shrink-0">
                                            Change
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 mt-3 text-xs">
                                        <div>
                                            <p class="text-gray-400 uppercase tracking-wide font-semibold">State</p>
                                            <p class="text-gray-700 font-medium mt-0.5">{{ $referringMatch['state'] ?: '—' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 uppercase tracking-wide font-semibold">City / LGA</p>
                                            <p class="text-gray-700 font-medium mt-0.5">{{ $referringMatch['city'] ?: '—' }}</p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-gray-400 uppercase tracking-wide font-semibold">Bank Account Status</p>
                                            <p class="mt-0.5">
                                                @if($referringMatch['has_bank'])
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                        On file{{ $referringMatch['bank_status'] ? ' · ' . ucfirst($referringMatch['bank_status']) : '' }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Not added yet</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @error('referringRealtorId') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    {{-- Amount --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Total Agreed Commission (₦)</label>
                        <input type="number" step="0.01" min="0" wire:model="dealAmount" placeholder="e.g. 500000"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('dealAmount') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button wire:click="closeLogDeal" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
                        <button wire:click="submitLogDeal" wire:loading.attr="disabled" wire:target="submitLogDeal"
                                class="flex-1 py-2.5 text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-colors disabled:opacity-60 inline-flex items-center justify-center gap-2">
                            <svg wire:loading wire:target="submitLogDeal" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="submitLogDeal">Record Deal</span>
                            <span wire:loading wire:target="submitLogDeal">Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
