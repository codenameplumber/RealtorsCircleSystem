<div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- ===== FILTERS SIDEBAR ===== --}}
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filter Realtors
                    </h2>
                    <button wire:click="clearFilters" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Clear</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">State</label>
                        <select wire:model.live="state"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">All States</option>
                            @foreach($this->states as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">LGA / Area</label>
                        <select wire:model.live="lga"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                                @if(!$state) disabled @endif>
                            <option value="">{{ $state ? 'All LGAs' : 'Select state first' }}</option>
                            @foreach($this->lgas as $l)
                                <option value="{{ $l }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Specialization</label>
                        <select wire:model.live="specialization"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">All Types</option>
                            @foreach(['Residential','Commercial','Land','Industrial','Mixed'] as $sp)
                                <option value="{{ $sp }}">{{ $sp }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sort By</label>
                        <select wire:model.live="sortBy"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="newest">Newest Members</option>
                            <option value="most_assets">Most Properties</option>
                            <option value="most_viewed">Most Viewed</option>
                            <option value="highest_rated">Highest Rated</option>
                        </select>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ===== REALTOR GRID ===== --}}
        <div class="lg:col-span-3">

            {{-- Loading indicator --}}
            <div wire:loading class="flex items-center gap-2 text-sm text-gray-400 mb-4">
                <svg class="animate-spin w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Updating...
            </div>

            @if($realtors->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5" wire:loading.class="opacity-60">
                    @foreach($realtors as $realtor)
                        @php
                            $realtorListings = $listings[$realtor->id] ?? collect();
                            $avgRating       = round($realtor->avg_rating ?? 0, 1);
                            $ratingCount     = $realtor->rating_count ?? 0;
                            $rated           = ($ratingRealtorId === $realtor->id);
                        @endphp

                        <div x-data="{ expanded: false }"
                             class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden">

                            {{-- Realtor Card --}}
                            <div class="p-5">
                                {{-- Avatar + Name --}}
                                <div class="flex items-start gap-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
                                        {{ strtoupper(substr($realtor->First_name, 0, 1)) }}{{ strtoupper(substr($realtor->Last_name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 truncate">{{ $realtor->First_name }} {{ $realtor->Last_name }}</p>
                                        <p class="text-xs text-primary-600 font-medium">{{ $realtor->Specialization ?? 'Realtor' }}</p>
                                        @if($realtor->State)
                                            <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                {{ $realtor->Lga ? $realtor->Lga . ', ' : '' }}{{ $realtor->State }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Star Rating --}}
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-1">
                                        @for($s = 1; $s <= 5; $s++)
                                            <button wire:click="submitRating({{ $realtor->id }}, {{ $s }})"
                                                    class="text-{{ $s <= $avgRating ? 'amber' : 'gray' }}-{{ $s <= $avgRating ? '400' : '200' }} hover:text-amber-400 transition-colors"
                                                    title="Rate {{ $s }} star{{ $s > 1 ? 's' : '' }}">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </button>
                                        @endfor
                                        <span class="text-xs text-gray-400 ml-1">
                                            {{ $avgRating > 0 ? $avgRating : '–' }}
                                            @if($ratingCount > 0)({{ $ratingCount }})@endif
                                        </span>
                                    </div>
                                    @if($realtor->Yrs_experience)
                                        <span class="text-xs text-gray-400">{{ $realtor->Yrs_experience }} yrs exp</span>
                                    @endif
                                </div>

                                {{-- Rating feedback --}}
                                @if($rated && $ratingMessage)
                                    <p class="text-xs text-primary-600 font-medium mb-3">{{ $ratingMessage }}</p>
                                @endif

                                {{-- Bio --}}
                                @if($realtor->Bio)
                                    <p class="text-xs text-gray-500 line-clamp-2 mb-4 leading-relaxed">{{ $realtor->Bio }}</p>
                                @endif

                                {{-- Stats --}}
                                <div class="grid grid-cols-3 gap-2 mb-4">
                                    <div class="text-center bg-gray-50 rounded-xl py-2">
                                        <p class="text-sm font-bold text-gray-900">{{ $realtor->total_assets }}</p>
                                        <p class="text-[10px] text-gray-400 leading-none mt-0.5">Assets</p>
                                    </div>
                                    <div class="text-center bg-gray-50 rounded-xl py-2">
                                        <p class="text-sm font-bold text-gray-900">{{ number_format($realtor->total_views) }}</p>
                                        <p class="text-[10px] text-gray-400 leading-none mt-0.5">Views</p>
                                    </div>
                                    <div class="text-center bg-gray-50 rounded-xl py-2">
                                        <p class="text-sm font-bold text-gray-900">{{ $realtor->total_appointments }}</p>
                                        <p class="text-[10px] text-gray-400 leading-none mt-0.5">Bookings</p>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex gap-2">
                                    <a href="{{ route('realtor.detail', \App\Helpers\IdHasher::encode($realtor->id)) }}"
                                       class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        View Profile
                                    </a>
                                    @if($realtorListings->count())
                                        <button @click="expanded=!expanded"
                                                class="flex-1 flex items-center justify-center gap-1 py-2 text-xs font-semibold text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors">
                                            <span x-text="expanded ? 'Hide' : '{{ $realtorListings->count() }} Listings'"></span>
                                            <svg :class="expanded ? 'rotate-180' : ''" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Listings Drawer --}}
                            <div x-show="expanded" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="border-t border-gray-100 px-4 pt-4 pb-4 bg-gray-50 space-y-2.5 max-h-80 overflow-y-auto">
                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Newest Listings</p>
                                @foreach($realtorListings as $listing)
                                    @php $thumb = ($listing->photos ?? [])[0] ?? null; @endphp
                                    <a href="{{ route('property.detail', \App\Helpers\IdHasher::encode($listing->id)) }}"
                                       class="flex items-center gap-2.5 p-2 bg-white rounded-xl hover:shadow-sm transition-shadow group">
                                        <div class="w-12 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                            @if($thumb)
                                                <img src="{{ $thumb }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                                    <svg class="w-4 h-4 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-900 line-clamp-1 group-hover:text-primary-600 transition-colors">{{ $listing->title }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $listing->city }} · ₦{{ $listing->price_display }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>

                @if($realtors->hasPages())
                    <div class="mt-8">{{ $realtors->links() }}</div>
                @endif
            @else
                <div class="text-center py-20 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="font-medium">No realtors found for your filters.</p>
                    <button wire:click="clearFilters" class="mt-3 text-sm text-primary-600 hover:underline">Clear filters</button>
                </div>
            @endif
        </div>
    </div>
</div>
