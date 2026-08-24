@extends('layouts.public')
@section('title', $realtor->First_name . ' ' . $realtor->Last_name . ' — Realtors Circle')
@section('meta_description', ($realtor->Bio ? Str::limit(strip_tags($realtor->Bio), 160) : $realtor->First_name . ' ' . $realtor->Last_name . ', ' . ($realtor->Specialization ?? 'Real Estate Professional') . ' on Realtors Circle' . ($realtor->State ? ' — ' . $realtor->State . ', Nigeria' : '') . '.'))
@section('og_title', $realtor->First_name . ' ' . $realtor->Last_name . ' — ' . ($realtor->Specialization ?? 'Real Estate Professional'))
@section('og_description', $realtor->Bio ? Str::limit(strip_tags($realtor->Bio), 160) : 'View listings, closed deals, and reviews for this verified realtor on Realtors Circle.')
@section('og_type', 'profile')
@section('canonical', route('realtor.detail', $hash))

@section('content')

@php
    $fullName    = $realtor->First_name . ' ' . $realtor->Last_name;
    $avgRating   = $stats['avg_rating'];
    $ratingCount = $stats['rating_count'];
    $initials    = strtoupper(substr($realtor->First_name, 0, 1) . substr($realtor->Last_name, 0, 1));
@endphp

{{-- Breadcrumb --}}
<div class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('realtors') }}" class="hover:text-primary-600 transition-colors">Realtors</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium">{{ $fullName }}</span>
        </nav>
    </div>
</div>

<section class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ===== LEFT: PROFILE CARD ===== --}}
            <div class="lg:col-span-1 space-y-5">

                {{-- Main Profile Card --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    {{-- Banner --}}
                    <div class="h-24 bg-gradient-to-br from-primary-600 to-primary-800"></div>
                    <div class="px-6 pb-6">
                        {{-- Avatar --}}
                        <div class="-mt-10 mb-4">
                            <div class="w-20 h-20 bg-white border-4 border-white rounded-2xl shadow-md flex items-center justify-center bg-gradient-to-br from-primary-500 to-primary-700">
                                <span class="text-white text-2xl font-bold">{{ $initials }}</span>
                            </div>
                        </div>

                        <h1 class="text-xl font-bold text-gray-900">{{ $fullName }}</h1>
                        <p class="text-sm text-primary-600 font-semibold mt-0.5">{{ $realtor->Specialization ?? 'Real Estate Professional' }}</p>

                        @if($realtor->State)
                            <p class="text-sm text-gray-500 mt-1.5 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $realtor->Lga ? $realtor->Lga . ', ' : '' }}{{ $realtor->State }}, Nigeria
                            </p>
                        @endif

                        {{-- Star Rating --}}
                        <div class="flex items-center gap-1.5 mt-3">
                            @for($s = 1; $s <= 5; $s++)
                                <svg class="w-5 h-5 {{ $s <= $avgRating ? 'text-amber-400' : 'text-gray-200' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="text-sm font-semibold text-gray-700 ml-1">
                                {{ $avgRating > 0 ? $avgRating : '–' }}
                            </span>
                            @if($ratingCount > 0)
                                <span class="text-sm text-gray-400">({{ $ratingCount }} {{ Str::plural('review', $ratingCount) }})</span>
                            @endif
                        </div>

                        @if($realtor->Bio)
                            <p class="text-sm text-gray-600 mt-4 leading-relaxed">{{ $realtor->Bio }}</p>
                        @endif
                    </div>
                </div>

                {{-- Stats --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Performance Stats</h3>
                    <div class="space-y-3">
                        @foreach([
                            ['label' => 'Total Listings',      'value' => number_format($stats['total_assets']),        'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'primary'],
                            ['label' => 'Total Property Views', 'value' => number_format($stats['total_views']),         'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'color' => 'blue'],
                            ['label' => 'Viewing Appointments', 'value' => number_format($stats['total_appointments']),  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'amber'],
                            ['label' => 'Closed Deals',         'value' => number_format($stats['closed_deals']),        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'green'],
                        ] as $stat)
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-{{ $stat['color'] }}-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $stat['icon'] }}"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $stat['value'] }}</p>
                                <p class="text-xs text-gray-400">{{ $stat['label'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Profile Details --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Profile Details</h3>
                    <div class="space-y-2.5 text-sm">
                        @if($realtor->Yrs_experience)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Experience</span>
                            <span class="font-medium text-gray-700">{{ $realtor->Yrs_experience }} years</span>
                        </div>
                        @endif
                        @if($realtor->Specialization)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Specialization</span>
                            <span class="font-medium text-gray-700">{{ $realtor->Specialization }}</span>
                        </div>
                        @endif
                        @if($realtor->State)
                        <div class="flex justify-between">
                            <span class="text-gray-400">State</span>
                            <span class="font-medium text-gray-700">{{ $realtor->State }}</span>
                        </div>
                        @endif
                        @if($realtor->Lga)
                        <div class="flex justify-between">
                            <span class="text-gray-400">LGA</span>
                            <span class="font-medium text-gray-700">{{ $realtor->Lga }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-400">KYC Status</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $realtor->kyc_status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $realtor->kyc_status === 'approved' ? 'Verified' : ucfirst($realtor->kyc_status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Member since</span>
                            <span class="font-medium text-gray-700">{{ $realtor->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Rate this realtor --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5"
                     x-data="realtorRating({{ $realtor->id }}, {{ $avgRating }})">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Rate this Realtor</h3>
                    <div x-show="!submitted">
                        <p class="text-xs text-gray-400 mb-3">How would you rate your experience?</p>
                        <div class="flex items-center gap-1 mb-4">
                            <template x-for="s in 5" :key="s">
                                <button @click="hover = s" @mouseleave="hover = selected"
                                        @click.prevent="rate(s)"
                                        class="transition-transform hover:scale-110">
                                    <svg :class="s <= (hover || selected) ? 'text-amber-400' : 'text-gray-200'"
                                         class="w-8 h-8 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            </template>
                        </div>
                        <p class="text-xs text-gray-400" x-text="selected ? labels[selected-1] : 'Click a star to rate'"></p>
                    </div>
                    <div x-show="submitted" class="text-center py-4">
                        <svg class="w-10 h-10 text-primary-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-900">Thanks for rating!</p>
                        <p class="text-xs text-gray-400 mt-1" x-text="message"></p>
                    </div>
                </div>

                {{-- Contact via agent --}}
                <div class="bg-primary-50 border border-primary-100 rounded-2xl p-5 text-center">
                    <svg class="w-8 h-8 text-primary-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <p class="text-sm font-bold text-primary-800 mb-1">Contact via Realtors Circle</p>
                    <p class="text-xs text-primary-600 leading-relaxed">
                        To maintain privacy and security, all contact is handled through our in-house agents.
                        Use the booking form on any property to request a viewing.
                    </p>
                </div>

            </div>

            {{-- ===== RIGHT: LISTINGS ===== --}}
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-gray-900">
                        Active Listings
                        <span class="text-sm font-normal text-gray-400 ml-1">({{ $stats['total_assets'] }} total)</span>
                    </h2>
                </div>

                @if($listings->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($listings as $property)
                            @php $thumb = ($property->photos ?? [])[0] ?? null; @endphp
                            <a href="{{ route('property.detail', \App\Helpers\IdHasher::encode($property->id)) }}"
                               class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
                                <div class="h-40 overflow-hidden bg-gray-100 relative">
                                    @if($thumb)
                                        <img src="{{ $thumb }}" alt="{{ $property->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                            <svg class="w-10 h-10 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="absolute top-2 left-2 flex gap-1">
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-primary-600 text-white rounded-full">{{ $property->type }}</span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-white text-gray-700 rounded-full">{{ $property->listing_type }}</span>
                                    </div>
                                    <div class="absolute bottom-2 right-2">
                                        <span class="px-2.5 py-1 text-xs font-bold bg-white/95 text-gray-900 rounded-xl shadow-sm">
                                            ₦{{ $property->price_display }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <p class="text-sm font-semibold text-gray-900 line-clamp-1 group-hover:text-primary-600 transition-colors">
                                        {{ $property->title }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        {{ $property->neigborhood }}, {{ $property->city }}
                                    </p>
                                    <p class="text-[10px] text-gray-300 mt-1">{{ $property->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if($listings->hasPages())
                        <div class="mt-8">{{ $listings->links() }}</div>
                    @endif
                @else
                    <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-sm text-gray-400">No active listings at the moment.</p>
                    </div>
                @endif
            </div>

            {{-- ===== CLOSED DEALS ===== --}}
            @if($closedDeals->count())
            <div class="mt-10">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-gray-900">
                        Closed Deals
                        <span class="text-sm font-normal text-gray-400 ml-1">({{ $stats['closed_deals'] }} completed)</span>
                    </h2>
                </div>
                <div class="space-y-3">
                    @foreach($closedDeals as $deal)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $deal->property?->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $deal->property?->neigborhood }}, {{ $deal->property?->city }} ·
                                {{ $deal->preferred_date?->format('M Y') }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Completed</span>
                            <p class="text-xs text-gray-400 mt-1 capitalize">{{ str_replace('_',' ',$deal->visit_type) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</section>

@push('scripts')
<script>
function realtorRating(realtorId, currentAvg) {
    return {
        selected: 0,
        hover:    currentAvg > 0 ? Math.round(currentAvg) : 0,
        submitted: false,
        message:  '',
        labels: ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'],
        rate(stars) {
            if (this.submitted) return;
            this.selected = stars;
            fetch('/api/rate-realtor', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                body: JSON.stringify({ realtor_id: realtorId, rating: stars })
            })
            .then(r => r.json())
            .then(data => {
                this.submitted = true;
                this.message   = data.message || 'Rating submitted!';
            })
            .catch(() => {
                this.submitted = true;
                this.message   = 'Rating saved!';
            });
        }
    }
}
</script>
@endpush

@endsection
