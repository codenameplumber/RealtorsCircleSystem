@php $isEdit = (bool) $propertyId; @endphp

<div x-data="{ step: 1 }" class="max-w-3xl">

    {{-- Edit banner --}}
    @if($isEdit)
        <div class="mb-5 flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-amber-800">Editing property</p>
                <p class="text-xs text-amber-600 truncate">{{ $title }}</p>
            </div>
            <a href="{{ route('realtor.properties.show', \App\Helpers\IdHasher::encode($propertyId)) }}"
               class="text-xs text-amber-700 font-medium hover:underline flex-shrink-0">← Back to view</a>
        </div>
    @endif

    {{-- Step Indicator --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([1 => 'Basics', 2 => 'Location', 3 => 'Details', 4 => 'Photos & Videos'] as $n => $label)
            <div class="flex items-center gap-2 {{ $n < 4 ? 'flex-1' : '' }}">
                <div class="flex items-center gap-2">
                    <div :class="step >= {{ $n }} ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-400'"
                         class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-colors flex-shrink-0">
                        <span x-show="step > {{ $n }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span x-show="step <= {{ $n }}">{{ $n }}</span>
                    </div>
                    <span :class="step >= {{ $n }} ? 'text-gray-900 font-medium' : 'text-gray-400'"
                          class="text-xs hidden sm:block transition-colors">{{ $label }}</span>
                </div>
                @if($n < 4)
                    <div :class="step > {{ $n }} ? 'bg-primary-500' : 'bg-gray-200'"
                         class="flex-1 h-0.5 transition-colors mx-1"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Success --}}
    @if($saved)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">
                {{ $isEdit ? 'Property Updated!' : 'Property Submitted!' }}
            </h3>
            <p class="text-sm text-gray-500 mb-6">
                {{ $isEdit ? 'Your changes have been saved.' : 'Your listing is pending admin review and will go live once approved.' }}
            </p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('realtor.properties') }}"
                   class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    View My Properties
                </a>
                @if(!$isEdit)
                    <button wire:click="$set('saved', false)" @click="step=1"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                        Add Another
                    </button>
                @endif
            </div>
        </div>
    @else

        {{-- General Error --}}
        @if($saveError)
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl">{{ $saveError }}</div>
        @endif

        {{-- ===== STEP 1: BASICS ===== --}}
        <div x-show="step===1" x-cloak>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h3 class="font-bold text-gray-900">Property Basics</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Title</label>
                    <input wire:model="title" type="text" placeholder="e.g. 4 Bedroom Duplex in Lekki Phase 1"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('title') border-red-400 @else border-gray-200 @enderror">
                    @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Property Type</label>
                        <select wire:model="type"
                                class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('type') border-red-400 @else border-gray-200 @enderror">
                            <option value="">Select type</option>
                            <option value="Apartment">Apartment</option>
                            <option value="Duplex">Duplex</option>
                            <option value="Bungalow">Bungalow</option>
                            <option value="Detached House">Detached House</option>
                            <option value="Semi-Detached">Semi-Detached</option>
                            <option value="Terraced House">Terraced House</option>
                            <option value="Penthouse">Penthouse</option>
                            <option value="Studio">Studio</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Office Space">Office Space</option>
                            <option value="Warehouse">Warehouse</option>
                            <option value="Land">Land</option>
                            <option value="Farm Land">Farm Land</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Mixed Use">Mixed Use</option>
                        </select>
                        @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Listing Type</label>
                        <select wire:model="listing_type"
                                class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('listing_type') border-red-400 @else border-gray-200 @enderror">
                            <option value="">Select</option>
                            <option value="For Sale">For Sale</option>
                            <option value="For Rent">For Rent</option>
                            <option value="Short Let">Short Let</option>
                            <option value="Joint Venture">Joint Venture</option>
                        </select>
                        @error('listing_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price (₦)</label>
                    <input wire:model="price" type="text" placeholder="e.g. 45,000,000 or 250,000/yr"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('price') border-red-400 @else border-gray-200 @enderror">
                    @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button type="button" @click="step=2"
                            class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                        Next: Location
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== STEP 2: LOCATION ===== --}}
        <div x-show="step===2" x-cloak>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h3 class="font-bold text-gray-900">Property Location</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <select wire:model.live="state"
                                class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('state') border-red-400 @else border-gray-200 @enderror">
                            <option value="">Select State</option>
                            @foreach($this->states as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('state')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">LGA</label>
                        <select wire:model="lga"
                                class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('lga') border-red-400 @else border-gray-200 @enderror"
                                @if(!$state) disabled @endif>
                            <option value="">Select LGA</option>
                            @foreach($this->lgas as $l)
                                <option value="{{ $l }}">{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('lga')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input wire:model="city" type="text" placeholder="e.g. Lagos Island"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('city') border-red-400 @else border-gray-200 @enderror">
                    @error('city')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Neighborhood / Estate</label>
                    <input wire:model="neighborhood" type="text" placeholder="e.g. Lekki Phase 1"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('neighborhood') border-red-400 @else border-gray-200 @enderror">
                    @error('neighborhood')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                    <input wire:model="street" type="text" placeholder="e.g. 14 Admiralty Way"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('street') border-red-400 @else border-gray-200 @enderror">
                    @error('street')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input wire:model="country" type="text"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50">
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="step=1"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <button type="button" @click="step=3"
                            class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                        Next: Details
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== STEP 3: DETAILS ===== --}}
        <div x-show="step===3" x-cloak>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h3 class="font-bold text-gray-900">Property Details</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" rows="5"
                              placeholder="Describe the property — key features, condition, neighbourhood highlights..."
                              class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none @error('description') border-red-400 @else border-gray-200 @enderror"></textarea>
                    <p class="text-xs text-gray-400 mt-1">{{ strlen($description) }} characters (min 20)</p>
                    @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Amenities</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($availableAmenities as $amenity)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input wire:model="amenities" type="checkbox" value="{{ $amenity }}"
                                       class="w-4 h-4 rounded text-primary-600 border-gray-300 focus:ring-primary-500 cursor-pointer">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">{{ $amenity }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="step=2"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <button type="button" @click="step=4"
                            class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                        Next: Photos & Videos
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== STEP 4: MEDIA ===== --}}
        <div x-show="step===4" x-cloak>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
                <h3 class="font-bold text-gray-900">Photos & Videos</h3>

                {{-- Existing Photos --}}
                @if(count($existingPhotos))
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Current Photos</p>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            @foreach($existingPhotos as $i => $url)
                                <div class="relative group rounded-xl overflow-hidden aspect-square bg-gray-100">
                                    <img src="{{ $url }}" class="w-full h-full object-cover">
                                    <button wire:click="removeExistingPhoto({{ $i }})" type="button"
                                            class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold">
                                        ✕
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Hover a photo and click ✕ to remove it</p>
                    </div>
                @endif

                {{-- New Photos Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ count($existingPhotos) ? 'Add More Photos' : 'Photos' }}
                        @if(!$isEdit) <span class="text-red-500">*</span> @endif
                        <span class="text-gray-400 text-xs font-normal ml-1">JPG/PNG, max 10 MB each</span>
                    </label>

                    <div class="relative border-2 border-dashed rounded-xl p-5 text-center transition-colors
                        @error('photos') border-red-400 bg-red-50 @else border-gray-200 hover:border-primary-400 @enderror">
                        <input wire:model="photos" type="file" multiple accept=".jpg,.jpeg,.png,.webp"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Click or drag photos here</p>
                        <p class="text-xs text-gray-400 mt-1">You can select multiple files</p>
                    </div>
                    @error('photos')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    @error('photos.*')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror

                    @if(count($photos))
                        <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 gap-2">
                            @foreach($photos as $i => $photo)
                                <div class="relative group rounded-xl overflow-hidden aspect-square bg-gray-100">
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <button wire:click="removePhoto({{ $i }})" type="button"
                                            class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold">
                                        ✕
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ count($photos) }} new photo(s) selected</p>
                    @endif

                    <div wire:loading wire:target="photos" class="mt-2 flex items-center gap-2 text-primary-600 text-sm">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Processing photos...
                    </div>
                </div>

                {{-- Existing Videos --}}
                @if(count($existingVideos))
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Current Videos</p>
                        <div class="space-y-2">
                            @foreach($existingVideos as $i => $url)
                                <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded-xl text-sm">
                                    <div class="flex items-center gap-2 text-gray-700 min-w-0">
                                        <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.362a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="truncate text-xs">Video {{ $i + 1 }}</span>
                                    </div>
                                    <button wire:click="removeExistingVideo({{ $i }})" type="button"
                                            class="ml-2 text-red-400 hover:text-red-600 flex-shrink-0 font-bold text-xs">✕</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- New Videos --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ count($existingVideos) ? 'Add More Videos' : 'Videos' }}
                        <span class="text-gray-400 text-xs font-normal ml-1">Optional — MP4/MOV, max 100 MB each</span>
                    </label>
                    <div class="relative border-2 border-dashed border-gray-200 hover:border-primary-400 rounded-xl p-5 text-center transition-colors">
                        <input wire:model="videos" type="file" multiple accept=".mp4,.mov,.avi,.webm"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.362a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Click or drag videos here</p>
                    </div>

                    @if(count($videos))
                        <div class="mt-3 space-y-2">
                            @foreach($videos as $i => $video)
                                <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded-xl text-sm">
                                    <span class="truncate text-gray-700 text-xs">{{ $video->getClientOriginalName() }}</span>
                                    <button wire:click="removeVideo({{ $i }})" type="button"
                                            class="ml-2 text-red-400 hover:text-red-600 flex-shrink-0 font-bold text-xs">✕</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Review Summary --}}
                <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 space-y-1.5">
                    <p class="font-semibold text-gray-700 mb-2">{{ $isEdit ? 'Review changes' : 'Review before submitting' }}</p>
                    <div class="flex justify-between"><span class="text-gray-400">Title</span><span class="text-right ml-4 truncate max-w-[60%]">{{ $title ?: '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Type</span><span>{{ $type ?: '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Listing</span><span>{{ $listing_type ?: '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Price</span><span>{{ $price ? '₦'.$price : '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Location</span><span>{{ implode(', ', array_filter([$lga, $state])) ?: '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Photos</span><span>{{ count($existingPhotos) + count($photos) }} total</span></div>
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="step=3"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <button wire:click="submit" wire:loading.attr="disabled" wire:loading.class="opacity-75"
                            class="px-7 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                        <svg wire:loading wire:target="submit" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span wire:loading.remove wire:target="submit">{{ $isEdit ? 'Save Changes' : 'Submit Property' }}</span>
                        <span wire:loading wire:target="submit">{{ $isEdit ? 'Saving...' : 'Submitting...' }}</span>
                    </button>
                </div>
            </div>
        </div>

    @endif
</div>
