<div class="livewire-fade">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- Filters Sidebar -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filters
                    </h2>
                    <button wire:click="clearFilters" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Clear all</button>
                </div>

                <div class="space-y-5">
                    <!-- Keyword Search -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Search</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input wire:model.live.debounce.500ms="search" type="text"
                                   placeholder="State, LGA, street, keyword..."
                                   class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <!-- State -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">State</label>
                        <select wire:model.live="state"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">All States</option>
                            @foreach($this->states as $s)
                                <option value="{{ $s }}" {{ $state === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- LGA -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            LGA / Area
                        </label>
                        <select wire:model.live="lga"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                                @if(!$state) disabled @endif>
                            <option value="">{{ $state ? 'All LGAs' : 'Select state first' }}</option>
                            @foreach($this->lgas as $l)
                                <option value="{{ $l }}" {{ $lga === $l ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Transaction Type -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Transaction</label>
                        <div class="grid grid-cols-2 gap-1.5">
                            @foreach(['' => 'All', 'For Sale' => 'Buy', 'For Rent' => 'Rent', 'Short Let' => 'Short Let'] as $val => $label)
                                <button wire:click="$set('listingType', '{{ $val }}')"
                                        class="py-2 text-xs font-medium rounded-lg border transition-colors
                                               {{ $listingType === $val ? 'bg-primary-600 text-white border-primary-600' : 'text-gray-600 border-gray-200 hover:border-primary-400' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Property Type -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Property Type</label>
                        <select wire:model.live="type"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">All Types</option>
                            @foreach(['Apartment','Duplex','Bungalow','Detached House','Semi-Detached','Terraced House','Penthouse','Studio','Commercial','Office Space','Warehouse','Land','Farm Land','Industrial','Mixed Use'] as $pt)
                                <option value="{{ $pt }}">{{ $pt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sort By</label>
                        <select wire:model.live="sortBy"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="latest">Newest First</option>
                            <option value="price_asc">Price: Low → High</option>
                            <option value="price_desc">Price: High → Low</option>
                            <option value="views">Most Viewed</option>
                        </select>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Results -->
        <div class="lg:col-span-3">
            <!-- Results header -->
            <div class="flex items-center justify-between mb-5">
                <p class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">{{ $total }}</span> {{ Str::plural('property', $total) }} found
                    @if($search || $type || $listingType || $state || $lga)
                        <button wire:click="clearFilters" class="ml-2 text-primary-600 hover:underline text-xs">Reset</button>
                    @endif
                </p>
                <div wire:loading class="flex items-center gap-2 text-xs text-gray-400">
                    <svg class="animate-spin w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Updating...
                </div>
            </div>

            @if($properties->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5" wire:loading.class="opacity-60">
                    @foreach($properties as $property)
                        @php
                            $photos = is_array($property->photos) ? $property->photos : [];
                            $coverPhoto = $photos[0] ?? null;
                        @endphp
                        <div class="property-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
                            <!-- Image -->
                            <div class="relative h-48 overflow-hidden bg-gray-100">
                                @if($coverPhoto)
                                    <img src="{{ $coverPhoto }}" alt="{{ $property->title }}"
                                         class="property-image w-full h-full object-cover transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
                                        <svg class="w-12 h-12 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Badges -->
                                <div class="absolute top-3 left-3 flex gap-1.5">
                                    <span class="px-2 py-0.5 text-xs font-semibold bg-primary-600 text-white rounded-full">Verified</span>
                                    @if($property->listing_type)
                                        <span class="px-2 py-0.5 text-xs font-semibold bg-white text-gray-700 rounded-full capitalize">{{ $property->listing_type }}</span>
                                    @endif
                                </div>

                                <!-- Price -->
                                <div class="absolute bottom-3 right-3">
                                    <span class="px-3 py-1 text-sm font-bold bg-white/95 text-gray-900 rounded-xl shadow-sm">
                                        ₦{{ $property->price_display }}
                                    </span>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="p-4">
                                <div class="flex items-start gap-1 text-xs text-gray-500 mb-1.5">
                                    <svg class="w-3.5 h-3.5 mt-0.5 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $property->neigborhood }}, {{ $property->city }}
                                </div>

                                <h3 class="font-semibold text-gray-900 text-sm mb-3 line-clamp-1 group-hover:text-primary-600 transition-colors">
                                    {{ $property->title }}
                                </h3>

                                @php $amenities = is_array($property->amenities) ? $property->amenities : []; @endphp
                                @if(!empty($amenities))
                                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-4">
                                        @if(isset($amenities['bedrooms']))
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 6h18M3 18h18"/></svg>
                                                {{ $amenities['bedrooms'] }} Beds
                                            </span>
                                        @endif
                                        @if(isset($amenities['bathrooms']))
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                {{ $amenities['bathrooms'] }} Baths
                                            </span>
                                        @endif
                                        @if(isset($amenities['size']))
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                                {{ $amenities['size'] }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <a href="{{ route('property.detail', \App\Helpers\IdHasher::encode($property->id)) }}"
                                   class="block w-full text-center py-2 text-sm font-semibold text-primary-600 border border-primary-600 rounded-lg hover:bg-primary-600 hover:text-white transition-colors">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $properties->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-1">No properties found</h3>
                    <p class="text-sm text-gray-400 mb-5">Try adjusting your search filters</p>
                    <button wire:click="clearFilters" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        Clear all filters
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
