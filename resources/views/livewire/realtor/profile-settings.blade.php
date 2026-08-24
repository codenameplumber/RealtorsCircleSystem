<div x-data="{ tab: 'profile' }">

    {{-- Tab Nav --}}
    <div class="flex gap-1 mb-6 bg-gray-100 p-1 rounded-xl w-full overflow-x-auto">
        <button @click="tab='profile'" :class="tab==='profile' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 min-w-max px-4 py-2 text-sm font-medium rounded-lg transition-all">
            Profile
        </button>
        <button @click="tab='security'" :class="tab==='security' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 min-w-max px-4 py-2 text-sm font-medium rounded-lg transition-all">
            Security
        </button>
        <button @click="tab='kyc'" :class="tab==='kyc' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 min-w-max px-4 py-2 text-sm font-medium rounded-lg transition-all">
            KYC Verification
        </button>
        <button @click="tab='bank'" :class="tab==='bank' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 min-w-max px-4 py-2 text-sm font-medium rounded-lg transition-all">
            Bank Details
        </button>
    </div>

    {{-- ========== PROFILE TAB ========== --}}
    <div x-show="tab==='profile'" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-2xl">
            <h3 class="font-bold text-gray-900 mb-5">Profile Information</h3>

            @if($profileSaved)
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Profile updated successfully!
                </div>
            @endif

            <form wire:submit="updateProfile" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input wire:model="first_name" type="text"
                               class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('first_name') border-red-400 @else border-gray-200 @enderror">
                        @error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input wire:model="last_name" type="text"
                               class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('last_name') border-red-400 @else border-gray-200 @enderror">
                        @error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input wire:model="phone" type="tel"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('phone') border-red-400 @else border-gray-200 @enderror">
                    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                    <select wire:model="specialization"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Select</option>
                        <option value="Residential">Residential</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Land">Land</option>
                        <option value="Industrial">Industrial</option>
                        <option value="Mixed">Mixed Portfolio</option>
                    </select>
                    @error('specialization')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <select wire:model.live="state"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">Select State</option>
                            @foreach($this->states as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">LGA</label>
                        <select wire:model="lga"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                                @if(!$state) disabled @endif>
                            <option value="">Select LGA</option>
                            @foreach($this->lgas as $l)
                                <option value="{{ $l }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input wire:model="address" type="text" placeholder="Street address"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Bio <span class="text-gray-400 text-xs">(optional)</span>
                    </label>
                    <textarea wire:model="bio" rows="3" placeholder="Tell clients about yourself..."
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                    <p class="text-xs text-gray-400 mt-1">{{ strlen($bio) }}/500</p>
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                        class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                    <svg wire:loading wire:target="updateProfile" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span wire:loading.remove wire:target="updateProfile">Save Profile</span>
                    <span wire:loading wire:target="updateProfile">Saving...</span>
                </button>
            </form>
        </div>
    </div>

    {{-- ========== SECURITY TAB ========== --}}
    <div x-show="tab==='security'" x-cloak>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Change Password --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-5">Change Password</h3>

                @if($passwordSaved)
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Password changed successfully!
                    </div>
                @endif

                <form wire:submit="updatePassword" class="space-y-4">
                    {{-- Current Password --}}
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <div class="relative">
                            <input wire:model="current_password" :type="show ? 'text' : 'password'"
                                   class="w-full px-4 py-2.5 pr-10 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('current_password') border-red-400 @else border-gray-200 @enderror">
                            <button type="button" @click="show=!show"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('current_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- New Password --}}
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <div class="relative">
                            <input wire:model="new_password" :type="show ? 'text' : 'password'" placeholder="Min 8 characters"
                                   class="w-full px-4 py-2.5 pr-10 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('new_password') border-red-400 @else border-gray-200 @enderror">
                            <button type="button" @click="show=!show"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('new_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <div class="relative">
                            <input wire:model="confirm_password" :type="show ? 'text' : 'password'"
                                   class="w-full px-4 py-2.5 pr-10 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('confirm_password') border-red-400 @else border-gray-200 @enderror">
                            <button type="button" @click="show=!show"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('confirm_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                            class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                        <svg wire:loading wire:target="updatePassword" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                        <span wire:loading wire:target="updatePassword">Updating...</span>
                    </button>
                </form>
            </div>

            {{-- Account Info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 h-fit">
                <h4 class="text-sm font-semibold text-gray-700 mb-4">Account Info</h4>
                <div class="space-y-3 text-sm text-gray-600">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Email</span>
                        <span class="font-medium truncate ml-4">{{ $user->Email }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Status</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $user->Status === 'active' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ ucfirst($user->Status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Member since</span>
                        <span>{{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== KYC TAB ========== --}}
    <div x-show="tab==='kyc'" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-2xl">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h3 class="font-bold text-gray-900">KYC Verification</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Upload a government-issued ID for identity verification</p>
                </div>
                @php
                    $kycStatus = $user->fresh()->kyc_status ?? 'not_submitted';
                    $badge = match($kycStatus) {
                        'pending'  => ['bg-amber-100 text-amber-700', 'Under Review'],
                        'approved' => ['bg-green-100 text-green-700', 'Verified'],
                        'rejected' => ['bg-red-100 text-red-700', 'Rejected'],
                        default    => ['bg-gray-100 text-gray-500', 'Not Submitted'],
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge[0] }}">{{ $badge[1] }}</span>
            </div>

            @if($kycSaved)
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Documents submitted! Your KYC is under review.
                </div>
            @endif

            @if($kycError)
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl">{{ $kycError }}</div>
            @endif

            <form wire:submit="uploadKyc" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                    <select wire:model="kyc_id_type"
                            class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('kyc_id_type') border-red-400 @else border-gray-200 @enderror">
                        <option value="">Select document type</option>
                        <option value="NIN Card">NIN Card</option>
                        <option value="International Passport">International Passport</option>
                        <option value="Driver's License">Driver's License</option>
                        <option value="Voter's Card">Voter's Card</option>
                    </select>
                    @error('kyc_id_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Front ID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Front of ID <span class="text-red-500">*</span>
                    </label>
                    <div class="relative border-2 border-dashed rounded-xl p-4 text-center transition-colors
                        @error('kyc_front') border-red-400 bg-red-50 @else border-gray-200 hover:border-primary-400 @enderror">
                        <input wire:model="kyc_front" type="file" accept=".jpg,.jpeg,.png,.pdf"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        @if($kyc_front)
                            <div class="flex items-center justify-center gap-2 text-primary-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium">{{ $kyc_front->getClientOriginalName() }}</span>
                            </div>
                        @else
                            <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm text-gray-500">Click or drag to upload front of ID</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG or PDF — max 5 MB</p>
                        @endif
                    </div>
                    @error('kyc_front')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    @if($user->kyc_front_url && !$kyc_front)
                        <p class="mt-1 text-xs text-gray-400">Previously uploaded: <a href="{{ $user->kyc_front_link }}" target="_blank" class="text-primary-600 underline">view</a></p>
                    @endif
                </div>

                {{-- Back ID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Back of ID <span class="text-gray-400 text-xs">(optional)</span>
                    </label>
                    <div class="relative border-2 border-dashed border-gray-200 hover:border-primary-400 rounded-xl p-4 text-center transition-colors">
                        <input wire:model="kyc_back" type="file" accept=".jpg,.jpeg,.png,.pdf"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        @if($kyc_back)
                            <div class="flex items-center justify-center gap-2 text-primary-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium">{{ $kyc_back->getClientOriginalName() }}</span>
                            </div>
                        @else
                            <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm text-gray-500">Click or drag to upload back of ID</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG or PDF — max 5 MB</p>
                        @endif
                    </div>
                    @error('kyc_back')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    @if($user->kyc_back_url && !$kyc_back)
                        <p class="mt-1 text-xs text-gray-400">Previously uploaded: <a href="{{ $user->kyc_back_link }}" target="_blank" class="text-primary-600 underline">view</a></p>
                    @endif
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                        class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                    <svg wire:loading wire:target="uploadKyc" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span wire:loading.remove wire:target="uploadKyc">Submit Documents</span>
                    <span wire:loading wire:target="uploadKyc">Uploading...</span>
                </button>
            </form>
        </div>
    </div>

    {{-- ========== BANK TAB ========== --}}
    <div x-show="tab==='bank'" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-2xl">
            <h3 class="font-bold text-gray-900 mb-1">Bank Details</h3>
            <p class="text-xs text-gray-400 mb-5">Used for commission payouts</p>

            @if($bankSaved)
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Bank details saved!
                </div>
            @endif

            <form wire:submit="saveBank" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                    <input wire:model="account_name" type="text" placeholder="As it appears on your bank account"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('account_name') border-red-400 @else border-gray-200 @enderror">
                    @error('account_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                    <select wire:model="bank_name"
                            class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('bank_name') border-red-400 @else border-gray-200 @enderror">
                        <option value="">Select bank</option>
                        <option>Access Bank</option>
                        <option>Citibank Nigeria</option>
                        <option>Ecobank Nigeria</option>
                        <option>Fidelity Bank</option>
                        <option>First Bank of Nigeria</option>
                        <option>First City Monument Bank (FCMB)</option>
                        <option>Globus Bank</option>
                        <option>Guaranty Trust Bank (GTBank)</option>
                        <option>Heritage Bank</option>
                        <option>Keystone Bank</option>
                        <option>Kuda Bank</option>
                        <option>Opay</option>
                        <option>Palmpay</option>
                        <option>Polaris Bank</option>
                        <option>Providus Bank</option>
                        <option>Stanbic IBTC Bank</option>
                        <option>Standard Chartered Bank</option>
                        <option>Sterling Bank</option>
                        <option>Suntrust Bank</option>
                        <option>Titan Trust Bank</option>
                        <option>Union Bank of Nigeria</option>
                        <option>United Bank for Africa (UBA)</option>
                        <option>Unity Bank</option>
                        <option>VFD MFB</option>
                        <option>Wema Bank</option>
                        <option>Zenith Bank</option>
                    </select>
                    @error('bank_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                    <input wire:model="account_number" type="text" maxlength="10" placeholder="10 digits"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('account_number') border-red-400 @else border-gray-200 @enderror">
                    @error('account_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                        class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                    <svg wire:loading wire:target="saveBank" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span wire:loading.remove wire:target="saveBank">{{ $bankId ? 'Update Bank Details' : 'Save Bank Details' }}</span>
                    <span wire:loading wire:target="saveBank">Saving...</span>
                </button>
            </form>
        </div>
    </div>

</div>
