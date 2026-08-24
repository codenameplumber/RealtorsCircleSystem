<div>
    @if($submitted)
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Request Submitted!</h3>
            <p class="text-gray-500 text-sm mb-1">Your viewing request has been received.</p>
            <p class="text-gray-500 text-sm mb-1">A confirmation has been sent to <strong>{{ $client_email }}</strong>.</p>
            <p class="text-gray-500 text-sm">Our <strong>in-house agent</strong> will contact you to confirm the appointment.</p>
        </div>
    @else
        <form wire:submit="book" class="space-y-4">

            {{-- ── CLIENT INFO ── --}}
            @if($is_logged_in)
                {{-- Logged-in realtor: show pre-filled badge --}}
                <div class="flex items-center gap-3 bg-primary-50 border border-primary-100 rounded-xl px-4 py-3">
                    <div class="w-9 h-9 bg-primary-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-primary-900">Booking as {{ $client_name }}</p>
                        <p class="text-xs text-primary-600 truncate">{{ $client_email }}</p>
                    </div>
                    <svg class="w-4 h-4 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            @else
                {{-- Guest: collect contact details --}}
                <div class="border border-gray-200 rounded-xl p-4 space-y-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Your Contact Details</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input wire:model="client_name" type="text" placeholder="e.g. John Okafor"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('client_name') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('client_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                            <input wire:model="client_phone" type="tel" placeholder="080XXXXXXXX"
                                   class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('client_phone') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                            @error('client_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                            <input wire:model="client_whatsapp" type="tel" placeholder="Same as phone?"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                        <input wire:model="client_email" type="email" placeholder="you@example.com"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('client_email') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('client_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            @endif

            {{-- ── DATE & TIME ── --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Date</label>
                    <input wire:model="preferred_date" type="date"
                           min="{{ now()->addDay()->format('Y-m-d') }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('preferred_date') border-red-400 @else border-gray-200 @enderror">
                    @error('preferred_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Time</label>
                    <select wire:model="preferred_time"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('preferred_time') border-red-400 @else border-gray-200 @enderror">
                        <option value="">Select time</option>
                        @foreach(['9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('preferred_time')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- ── TOUR TYPE ── --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tour Type</label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" wire:click="$set('visit_type','in_person')"
                            class="flex items-center justify-center gap-2 px-4 py-3 border-2 rounded-xl text-sm font-medium transition-colors
                                   {{ $visit_type === 'in_person' ? 'border-primary-600 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        In-Person
                    </button>
                    <button type="button" wire:click="$set('visit_type','virtual')"
                            class="flex items-center justify-center gap-2 px-4 py-3 border-2 rounded-xl text-sm font-medium transition-colors
                                   {{ $visit_type === 'virtual' ? 'border-primary-600 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Virtual Tour
                    </button>
                </div>
            </div>

            {{-- ── NOTES ── --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Notes <span class="text-gray-400 text-xs">(optional)</span>
                </label>
                <textarea wire:model="message" rows="2"
                          placeholder="Any special requirements for the viewing..."
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
            </div>

            {{-- ── INSPECTION FEE NOTICE ── --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-amber-800 mb-1">Inspection Fee Required</p>
                        <p class="text-xs text-amber-700 leading-relaxed">
                            A <strong>₦10,000 inspection fee</strong> is required before your viewing can take place.
                            This fee is collected on-site by our in-house agent and is split equally between the two realtors
                            involved (₦5,000 each). This ensures verified, professionally coordinated viewings.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── FEE ACKNOWLEDGEMENT CHECKBOX ── --}}
            <div>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input wire:model="fee_acknowledged" type="checkbox"
                           class="mt-0.5 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 flex-shrink-0">
                    <span class="text-sm text-gray-700 leading-relaxed">
                        I understand that a <strong>₦10,000 inspection fee</strong> is required and will be paid to the
                        Realtors Circle in-house agent on the day of viewing.
                    </span>
                </label>
                @error('fee_acknowledged')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- ── PRIVACY NOTICE ── --}}
            <div class="bg-primary-50 border border-primary-100 rounded-xl p-3 text-xs text-primary-700 flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Your contact details are never shared directly with the realtor. All viewings are coordinated through our in-house agents.</span>
            </div>

            <button type="submit"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors flex items-center justify-center gap-2"
                    wire:loading.class="opacity-75 cursor-not-allowed" wire:loading.attr="disabled">
                <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span wire:loading.remove>
                    {{ $visit_type === 'virtual' ? 'Request Virtual Tour' : 'Request Viewing' }}
                </span>
                <span wire:loading>Submitting...</span>
            </button>
        </form>
    @endif
</div>
