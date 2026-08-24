<div class="max-w-2xl space-y-6">

    {{-- ── Profile Information ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-50">
            <h3 class="font-bold text-gray-900">Profile Information</h3>
            <p class="text-xs text-gray-400 mt-0.5">Update your name and phone number.</p>
        </div>
        <div class="p-6 space-y-4">
            {{-- Read-only info --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Email</p>
                    <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $agent->Email }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Assigned LGA</p>
                    <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $agent->Assigned_area }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">State</p>
                    <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $agent->State }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Specialization</p>
                    <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $agent->Specialization }}</p>
                </div>
            </div>

            {{-- Editable fields --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">First Name</label>
                    <input wire:model="first_name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('first_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Last Name</label>
                    <input wire:model="last_name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone Number</label>
                    <input wire:model="phone" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                @if($profileSaved)
                    <p class="text-xs text-green-600 font-semibold">Profile updated successfully!</p>
                @else
                    <span></span>
                @endif
                <button wire:click="updateProfile" wire:loading.attr="disabled"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition-colors">
                    <span wire:loading.remove wire:target="updateProfile">Save Profile</span>
                    <span wire:loading wire:target="updateProfile">Saving...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Password ─────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-50">
            <h3 class="font-bold text-gray-900">Update Password</h3>
            <p class="text-xs text-gray-400 mt-0.5">Choose a strong password with at least 8 characters.</p>
        </div>
        <div class="p-6 space-y-4">

            {{-- Current Password --}}
            <div x-data="{ show: false }">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Current Password</label>
                <div class="relative">
                    <input wire:model="current_password" :type="show ? 'text' : 'password'"
                           class="w-full px-4 py-2.5 pr-11 border @error('current_password') border-red-300 bg-red-50 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 transition-colors">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- New Password --}}
                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">New Password</label>
                    <div class="relative">
                        <input wire:model="new_password" :type="show ? 'text' : 'password'"
                               class="w-full px-4 py-2.5 pr-11 border @error('new_password') border-red-300 bg-red-50 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 transition-colors">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('new_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Confirm Password --}}
                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <input wire:model="confirm_password" :type="show ? 'text' : 'password'"
                               class="w-full px-4 py-2.5 pr-11 border @error('confirm_password') border-red-300 bg-red-50 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 transition-colors">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('confirm_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex items-center justify-between pt-2">
                @if($passwordSaved)
                    <p class="text-xs text-green-600 font-semibold">Password updated successfully!</p>
                @else
                    <span></span>
                @endif
                <button wire:click="updatePassword" wire:loading.attr="disabled"
                        class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-colors">
                    <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                    <span wire:loading wire:target="updatePassword">Updating...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Bank Information ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-50">
            <h3 class="font-bold text-gray-900">Bank Information</h3>
            <p class="text-xs text-gray-400 mt-0.5">Used for commission disbursements.</p>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bank Name</label>
                <input wire:model="bank_name" type="text" placeholder="e.g. GTBank" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                @error('bank_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Account Number</label>
                    <input wire:model="account_number" type="text" maxlength="10" placeholder="10-digit number" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('account_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Account Name</label>
                    <input wire:model="account_name" type="text" placeholder="As on bank records" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('account_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex items-center justify-between pt-2">
                @if($bankSaved)
                    <p class="text-xs text-green-600 font-semibold">Bank information saved!</p>
                @else
                    <span></span>
                @endif
                <button wire:click="saveBank" wire:loading.attr="disabled"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition-colors">
                    <span wire:loading.remove wire:target="saveBank">Save Bank Info</span>
                    <span wire:loading wire:target="saveBank">Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>
