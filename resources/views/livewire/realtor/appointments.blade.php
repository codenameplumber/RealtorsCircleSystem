<div>
    {{-- ── Toast notification (driven by apt-notify browser event) ── --}}
    <div x-data="{
            show: false,
            message: '',
            type: 'success',
            timer: null,
            open(msg, t) {
                this.message = msg;
                this.type    = t || 'success';
                this.show    = true;
                clearTimeout(this.timer);
                this.timer   = setTimeout(() => this.show = false, 6000);
            }
         }"
         @apt-notify.window="open($event.detail.message, $event.detail.type)"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium border"
         :class="{
             'bg-green-50 border-green-200 text-green-800' : type === 'success',
             'bg-amber-50 border-amber-200 text-amber-800' : type === 'warning',
             'bg-red-50   border-red-200   text-red-800'   : type === 'error'
         }">
        <svg x-show="type === 'success'" class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <svg x-show="type === 'warning'" class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <svg x-show="type === 'error'" class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span x-text="message" class="flex-1"></span>
        <button @click="show = false" class="text-current opacity-50 hover:opacity-100 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ── Appointments list ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        @if($appointments->count())
            <div class="divide-y divide-gray-50">
                @foreach($appointments as $apt)
                <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors">

                    {{-- Date badge --}}
                    <div class="w-14 h-14 bg-primary-50 rounded-xl flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-[10px] font-bold text-primary-600 uppercase">
                            {{ ($apt->reschedule_date ?? $apt->preferred_date)?->format('M') }}
                        </span>
                        <span class="text-xl font-extrabold text-primary-700 leading-none">
                            {{ ($apt->reschedule_date ?? $apt->preferred_date)?->format('d') }}
                        </span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $apt->name }}</p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ $apt->property?->title }} · {{ $apt->reschedule_time ?? $apt->preferred_time }}
                        </p>
                        @if($apt->reschedule_date)
                            <p class="text-xs text-amber-600 font-medium mt-0.5">
                                Rescheduled → {{ $apt->reschedule_date->format('M j, Y') }}
                            </p>
                        @endif
                    </div>

                    {{-- Status --}}
                    @php
                        $colors = [
                            'confirmed' => 'bg-green-100 text-green-700',
                            'pending'   => 'bg-amber-100 text-amber-700',
                            'cancelled' => 'bg-red-100 text-red-600',
                            'completed' => 'bg-gray-100 text-gray-600',
                        ];
                    @endphp
                    <div class="text-right flex-shrink-0 hidden sm:block">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors[$apt->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($apt->status) }}
                        </span>
                        <p class="text-xs text-gray-400 mt-1 capitalize">{{ str_replace('_',' ',$apt->visit_type) }}</p>
                    </div>

                    {{-- Actions --}}
                    @if(!in_array($apt->status, ['cancelled','completed']))
                    <div class="flex items-center gap-2 flex-shrink-0">

                        @if($apt->status === 'pending')
                        <button wire:click="accept({{ $apt->id }})"
                                wire:loading.attr="disabled"
                                wire:target="accept({{ $apt->id }})"
                                class="relative px-3 py-1.5 bg-green-600 hover:bg-green-700 disabled:opacity-60 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5 min-w-[70px] justify-center">
                            <svg wire:loading wire:target="accept({{ $apt->id }})" class="animate-spin w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="accept({{ $apt->id }})">Accept</span>
                            <span wire:loading       wire:target="accept({{ $apt->id }})">Wait...</span>
                        </button>
                        @endif

                        <button wire:click="openReschedule({{ $apt->id }})"
                                wire:loading.attr="disabled"
                                wire:target="openReschedule({{ $apt->id }})"
                                class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 disabled:opacity-60 text-amber-700 border border-amber-200 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                            <svg wire:loading wire:target="openReschedule({{ $apt->id }})" class="animate-spin w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="openReschedule({{ $apt->id }})">Reschedule</span>
                        </button>

                        <button wire:click="openCancel({{ $apt->id }})"
                                wire:loading.attr="disabled"
                                wire:target="openCancel({{ $apt->id }})"
                                class="px-3 py-1.5 bg-red-50 hover:bg-red-100 disabled:opacity-60 text-red-600 border border-red-200 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                            <svg wire:loading wire:target="openCancel({{ $apt->id }})" class="animate-spin w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="openCancel({{ $apt->id }})">Cancel</span>
                        </button>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($appointments->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between gap-4 flex-wrap">
                    <p class="text-xs text-gray-400">Page {{ $appointments->currentPage() }} of {{ $appointments->lastPage() }}</p>
                    <div class="flex items-center gap-1">
                        @if($appointments->onFirstPage())
                            <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">← Prev</span>
                        @else
                            <button wire:click="previousPage" class="px-3 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-primary-400 hover:text-primary-600 transition-colors">← Prev</button>
                        @endif
                        @foreach($appointments->getUrlRange(max(1,$appointments->currentPage()-2), min($appointments->lastPage(),$appointments->currentPage()+2)) as $page => $url)
                            @if($page == $appointments->currentPage())
                                <span class="px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 rounded-lg">{{ $page }}</span>
                            @else
                                <button wire:click="gotoPage({{ $page }})" class="px-3 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-primary-400 hover:text-primary-600 transition-colors">{{ $page }}</button>
                            @endif
                        @endforeach
                        @if($appointments->hasMorePages())
                            <button wire:click="nextPage" class="px-3 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-primary-400 hover:text-primary-600 transition-colors">Next →</button>
                        @else
                            <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">Next →</span>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm">No appointments found.</p>
            </div>
        @endif
    </div>

    {{-- ══════ RESCHEDULE MODAL ══════ --}}
    @if($rescheduleOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="$set('rescheduleOpen', false)"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900">Reschedule Viewing</h3>
                <button wire:click="$set('rescheduleOpen', false)" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5 text-xs text-amber-700">
                The client will be notified by email of the new date and time.
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Date</label>
                    <input wire:model="rescheduleDate" type="date"
                           min="{{ now()->addDay()->format('Y-m-d') }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500
                                  @error('rescheduleDate') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                    @error('rescheduleDate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Time</label>
                    <select wire:model="rescheduleTime"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500
                                   @error('rescheduleTime') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        <option value="">Select time</option>
                        @foreach(['9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('rescheduleTime')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('rescheduleOpen', false)"
                            class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                        Go Back
                    </button>
                    <button wire:click="submitReschedule"
                            wire:loading.attr="disabled"
                            wire:target="submitReschedule"
                            class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:opacity-60 text-white text-sm font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="submitReschedule" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span wire:loading.remove wire:target="submitReschedule">Confirm Reschedule</span>
                        <span wire:loading       wire:target="submitReschedule">Sending email...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════ CANCEL MODAL ══════ --}}
    @if($cancelOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="$set('cancelOpen', false)"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900">Cancel Viewing</h3>
                <button wire:click="$set('cancelOpen', false)" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-5 text-xs text-red-600">
                The client will be notified by email that this viewing has been cancelled.
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Reason <span class="text-gray-400 text-xs">(optional — shown to client)</span>
                    </label>
                    <textarea wire:model="cancelReason" rows="3"
                              placeholder="e.g. Property is no longer available on this date..."
                              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('cancelOpen', false)"
                            class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                        Go Back
                    </button>
                    <button wire:click="submitCancel"
                            wire:loading.attr="disabled"
                            wire:target="submitCancel"
                            class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 disabled:opacity-60 text-white text-sm font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="submitCancel" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span wire:loading.remove wire:target="submitCancel">Cancel Viewing</span>
                        <span wire:loading       wire:target="submitCancel">Sending email...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
