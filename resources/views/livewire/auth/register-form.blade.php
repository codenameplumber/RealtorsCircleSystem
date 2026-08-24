<div>
    @if($agencyName)
        <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
            <p class="text-sm text-emerald-800">You're registering under <strong>{{ $agencyName }}</strong>. Your account will be linked to their network once approved.</p>
        </div>
    @endif

    <form wire:submit="register" class="space-y-5">

        <!-- Personal Information -->
        <div>
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Personal Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input wire:model="first_name" type="text" placeholder="John"
                           class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition @error('first_name') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                    @error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input wire:model="last_name" type="text" placeholder="Doe"
                           class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition @error('last_name') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                    @error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input wire:model="email" type="email" placeholder="you@example.com" autocomplete="email"
                   class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <input wire:model="phone" type="tel" placeholder="+234 800 000 0000"
                   class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition @error('phone') border-red-400 bg-red-50 @else border-gray-300 @enderror">
            @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div x-data="{ show: false, showConfirm: false }">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input wire:model="password" :type="show ? 'text' : 'password'" placeholder="Min 8 characters"
                               class="w-full px-4 py-2.5 pr-10 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition @error('password') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                        <button type="button" @click="show=!show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <input wire:model="password_confirmation" :type="showConfirm ? 'text' : 'password'" placeholder="Repeat password"
                               class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                        <button type="button" @click="showConfirm=!showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="pt-3 border-t border-gray-100">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Professional Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                    <select wire:model="specialization"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition @error('specialization') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                        <option value="">Select specialization</option>
                        <option value="Residential">Residential</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Land">Land</option>
                        <option value="Industrial">Industrial</option>
                        <option value="Mixed">Mixed Portfolio</option>
                    </select>
                    @error('specialization')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Years of Experience</label>
                    <select wire:model="years_experience"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition @error('years_experience') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                        <option value="">Select years</option>
                        <option value="0-1">Less than 1 year</option>
                        <option value="1-3">1 – 3 years</option>
                        <option value="3-5">3 – 5 years</option>
                        <option value="5-10">5 – 10 years</option>
                        <option value="10+">10+ years</option>
                    </select>
                    @error('years_experience')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Terms -->
        <div class="flex items-start gap-3 pt-1">
            <input wire:model="terms_agreed" type="checkbox" id="terms" class="mt-0.5 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
            <label for="terms" class="text-sm text-gray-600 leading-snug">
                I agree to the
                <a href="{{ route('terms') }}" target="_blank" class="text-primary-600 hover:underline font-medium">Terms of Service</a>
                and
                <a href="{{ route('privacy') }}" target="_blank" class="text-primary-600 hover:underline font-medium">Privacy Policy</a>
            </label>
        </div>
        @error('terms_agreed')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

        <!-- Submit -->
        <button type="submit"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm"
                wire:loading.class="opacity-75 cursor-not-allowed" wire:loading.attr="disabled">
            <svg wire:loading class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span wire:loading.remove>Create Account</span>
            <span wire:loading>Creating account...</span>
        </button>
    </form>
</div>
