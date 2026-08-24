<div>
    {{-- Toast --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:agent-notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 4000)"
         x-show="show" x-cloak x-transition
         :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed top-5 right-5 z-50 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg max-w-sm">
        <span x-text="message"></span>
    </div>

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-gray-50">
            <div class="flex gap-1 bg-gray-100 rounded-xl p-1">
                @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
                    <button wire:click="$set('tab', '{{ $key }}')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $tab === $key ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2 w-full sm:w-56">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search properties..." class="bg-transparent text-xs text-gray-600 placeholder-gray-400 outline-none w-full">
            </div>
        </div>

        {{-- Property list --}}
        @if($properties->count())
            <div class="divide-y divide-gray-50">
                @foreach($properties as $prop)
                    <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50">
                        {{-- Thumbnail --}}
                        <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                            @php $photos = is_array($prop->photos) ? $prop->photos : json_decode($prop->photos ?? '[]', true); @endphp
                            @if(!empty($photos[0]))
                                <img src="{{ $photos[0] }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $prop->title }}</p>
                            <p class="text-xs text-gray-500">{{ $prop->neigborhood }}, {{ $prop->city }} · ₦{{ $prop->price_display }}</p>
                            <p class="text-xs text-gray-400">By {{ $prop->user?->First_name }} {{ $prop->user?->Last_name }} · {{ $prop->user?->Phone }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @php $as = $prop->approval_status; $ac = match($as){ 'approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700',default=>'bg-amber-100 text-amber-700' }; @endphp
                            <span class="text-xs {{ $ac }} px-2.5 py-1 rounded-full font-semibold hidden sm:inline">{{ ucfirst($as ?? 'pending') }}</span>
                            <button wire:click="viewProperty({{ $prop->id }})"
                                    class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                                Review
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($properties->hasPages())
                <div class="p-4 border-t border-gray-100">{{ $properties->links() }}</div>
            @endif
        @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                <p class="text-sm">No properties found.</p>
            </div>
        @endif
    </div>

    {{-- ── Property Detail Modal ────────────────────────────────────────────── --}}
    @if($detailOpen && $viewingProperty)
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-900">Property Review</h2>
                    <button wire:click="closeDetail" class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Property info --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $viewingProperty->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $viewingProperty->street }}, {{ $viewingProperty->neigborhood }}, {{ $viewingProperty->city }}</p>
                        <p class="text-xl font-black text-primary-600 mt-2">₦{{ $viewingProperty->price_display }}</p>
                    </div>

                    {{-- Property details --}}
                    <div class="grid grid-cols-3 gap-3">
                        @foreach([
                            ['label' => 'Type', 'value' => $viewingProperty->property_type],
                            ['label' => 'Listing', 'value' => $viewingProperty->listing_type],
                            ['label' => 'Bedrooms', 'value' => $viewingProperty->bedrooms ?? '—'],
                            ['label' => 'Bathrooms', 'value' => $viewingProperty->bathrooms ?? '—'],
                            ['label' => 'Size', 'value' => $viewingProperty->size ?? '—'],
                            ['label' => 'Status', 'value' => ucfirst($viewingProperty->approval_status ?? 'pending')],
                        ] as $field)
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">{{ $field['label'] }}</p>
                                <p class="text-sm text-gray-900 font-medium mt-0.5">{{ $field['value'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Photos --}}
                    @php $photos = is_array($viewingProperty->photos) ? $viewingProperty->photos : json_decode($viewingProperty->photos ?? '[]', true); @endphp
                    @if(!empty($photos))
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Photos</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(array_slice($photos, 0, 6) as $photo)
                                    <img src="{{ $photo }}" alt="" class="w-full h-24 object-cover rounded-xl">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Realtor Profile Card --}}
                    @if($viewingProperty->user)
                        <div class="bg-primary-50 rounded-xl p-4">
                            <p class="text-xs font-semibold text-primary-500 uppercase tracking-wide mb-3">Realtor Details</p>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($viewingProperty->user->First_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $viewingProperty->user->First_name }} {{ $viewingProperty->user->Last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $viewingProperty->user->Specialization }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div class="bg-white rounded-lg p-2.5">
                                    <p class="text-[10px] text-gray-400">Phone</p>
                                    <p class="font-semibold text-gray-900">{{ $viewingProperty->user->Phone }}</p>
                                </div>
                                <div class="bg-white rounded-lg p-2.5">
                                    <p class="text-[10px] text-gray-400">Email</p>
                                    <p class="font-semibold text-gray-900 text-xs truncate">{{ $viewingProperty->user->Email }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    @if($viewingProperty->approval_status !== 'approved')
                        <div class="flex gap-3 pt-2">
                            <button wire:click="approve({{ $viewingProperty->id }})" wire:loading.attr="disabled"
                                    class="flex-1 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-colors text-sm">
                                <span wire:loading.remove wire:target="approve({{ $viewingProperty->id }})">Approve Property</span>
                                <span wire:loading wire:target="approve({{ $viewingProperty->id }})">Approving...</span>
                            </button>
                            <button wire:click="openReject({{ $viewingProperty->id }})"
                                    class="flex-1 py-3 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-xl transition-colors text-sm">
                                Reject
                            </button>
                        </div>
                    @else
                        <div class="flex gap-3 pt-2">
                            <button wire:click="openReject({{ $viewingProperty->id }})"
                                    class="flex-1 py-3 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-xl transition-colors text-sm">
                                Revoke Approval
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Reject Modal --}}
    @if($rejectOpen)
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Reason for Rejection</h3>
                    <p class="text-xs text-gray-400 mt-1">This will be emailed to the realtor.</p>
                </div>
                <div class="p-6 space-y-4">
                    <textarea wire:model="rejectReason" rows="4" placeholder="Describe what needs to be corrected..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
                    @error('rejectReason') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    <div class="flex gap-3">
                        <button wire:click="$set('rejectOpen', false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
                        <button wire:click="submitReject" wire:loading.attr="disabled"
                                class="flex-1 py-2.5 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">
                            <span wire:loading.remove wire:target="submitReject">Send Rejection</span>
                            <span wire:loading wire:target="submitReject">Sending...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
