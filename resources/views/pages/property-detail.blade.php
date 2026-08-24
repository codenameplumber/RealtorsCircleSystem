@extends('layouts.public')

@section('title', $property->title . ' — Realtors Circle')
@section('meta_description', Str::limit(strip_tags($property->description ?? ''), 160))
@section('og_title', $property->title . ' — ' . $property->city . ', ' . $property->state)
@section('og_description', Str::limit(strip_tags($property->description ?? ''), 160))
@section('og_image', (is_array($property->photos) && count($property->photos)) ? $property->photos[0] : asset('img/logo.png'))
@section('og_type', 'article')
@section('canonical', route('property.detail', \App\Helpers\IdHasher::encode($property->id)))

@push('styles')
<style>
    .gallery-thumb { cursor: pointer; transition: all 0.2s; }
    .gallery-thumb.active { ring: 2px; ring-color: #059669; }
</style>
@endpush

@section('content')

    @php
        $photos    = is_array($property->photos) ? $property->photos : [];
        $amenities = is_array($property->amenities) ? $property->amenities : [];
        $realtor   = $property->user;
    @endphp

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center gap-2 text-sm text-gray-400">
                <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Home</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('properties') }}" class="hover:text-primary-600 transition-colors">Properties</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-700 font-medium line-clamp-1">{{ $property->title }}</span>
            </nav>
        </div>
    </div>

    <section class="py-10 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- Left: Gallery + Details -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Gallery -->
                    <div x-data="{ active: 0 }" class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
                        <div class="relative h-72 sm:h-96 bg-gray-100">
                            @if(!empty($photos))
                                @foreach($photos as $i => $photo)
                                    <img src="{{ $photo }}" alt="{{ $property->title }}"
                                         class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                                         x-show="active === {{ $i }}"
                                         x-cloak>
                                @endforeach
                            @else
                                <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
                                    <svg class="w-20 h-20 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                            @endif

                            <!-- Badges -->
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span class="px-3 py-1 text-xs font-bold bg-primary-600 text-white rounded-full">Verified</span>
                                <span class="px-3 py-1 text-xs font-bold bg-white text-gray-700 rounded-full capitalize">{{ $property->listing_type ?? 'For Sale' }}</span>
                                <span class="px-3 py-1 text-xs font-bold bg-white text-gray-700 rounded-full">{{ $property->type }}</span>
                            </div>

                            <!-- Nav arrows -->
                            @if(count($photos) > 1)
                                <button @click="active = active === 0 ? {{ count($photos) - 1 }} : active - 1"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow transition-colors">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button @click="active = active === {{ count($photos) - 1 }} ? 0 : active + 1"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow transition-colors">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            @endif
                        </div>

                        <!-- Thumbnails -->
                        @if(count($photos) > 1)
                            <div class="p-3 flex gap-2 overflow-x-auto">
                                @foreach($photos as $i => $photo)
                                    <button @click="active = {{ $i }}"
                                            :class="active === {{ $i }} ? 'ring-2 ring-primary-600' : 'opacity-60 hover:opacity-100'"
                                            class="gallery-thumb flex-shrink-0 w-16 h-14 rounded-lg overflow-hidden transition-all">
                                        <img src="{{ $photo }}" alt="" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Listed By + Share -->
                    @if($realtor)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            {{-- Realtor row --}}
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                    {{ strtoupper(substr($realtor->First_name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-400 font-medium mb-0.5">Listed by</p>
                                    <p class="font-semibold text-gray-900">{{ $realtor->First_name }} {{ $realtor->Last_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $realtor->Specialization ?? 'Realtor' }}</p>
                                </div>
                                <a href="{{ route('realtor.detail', \App\Helpers\IdHasher::encode($realtor->id)) }}"
                                   class="flex-shrink-0 px-4 py-2 text-xs font-semibold text-primary-600 border border-primary-200 rounded-xl hover:bg-primary-50 transition-colors">
                                    View Profile
                                </a>
                            </div>

                            {{-- Share row --}}
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Share this property</p>
                                <div class="flex gap-3">
                                    <a href="https://wa.me/?text={{ urlencode($property->title . ' — ' . request()->url()) }}" target="_blank"
                                       class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        WhatsApp
                                    </a>
                                    <button onclick="navigator.clipboard.writeText(window.location.href).then(()=>alert('Link copied!'))"
                                            class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        Copy Link
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Property Info -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $property->title }}</h1>
                                <div class="flex items-center gap-1.5 text-gray-500 text-sm">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $property->neigborhood }}, {{ $property->city }}, {{ $property->state }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-extrabold text-primary-600">₦{{ $property->price_display }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ ucfirst($property->listing_type ?? 'Sale') }} price</div>
                            </div>
                        </div>

                        <!-- Amenity chips -->
                        @if(!empty($amenities))
                            <div class="flex flex-wrap gap-3 py-4 border-y border-gray-100 mb-6">
                                @if(isset($amenities['bedrooms']))
                                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 6h18M3 18h18"/></svg>
                                        <span class="text-sm font-medium text-gray-700">{{ $amenities['bedrooms'] }} Bedrooms</span>
                                    </div>
                                @endif
                                @if(isset($amenities['bathrooms']))
                                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581"/></svg>
                                        <span class="text-sm font-medium text-gray-700">{{ $amenities['bathrooms'] }} Bathrooms</span>
                                    </div>
                                @endif
                                @if(isset($amenities['size']))
                                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                        <span class="text-sm font-medium text-gray-700">{{ $amenities['size'] }}</span>
                                    </div>
                                @endif
                                @foreach(array_diff_key($amenities, array_flip(['bedrooms','bathrooms','size'])) as $key => $val)
                                    <div class="flex items-center gap-1.5 bg-gray-50 rounded-lg px-3 py-2">
                                        <svg class="w-3.5 h-3.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        <span class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $key) }}: {{ $val }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Description -->
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 mb-3">About This Property</h2>
                            <div class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ $property->description }}</div>
                        </div>

                        <!-- Address -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h2 class="text-lg font-bold text-gray-900 mb-3">Location</h2>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p>{{ $property->street }}, {{ $property->neigborhood }}</p>
                                <p>{{ $property->city }}, {{ $property->state }}, {{ $property->country }}</p>
                            </div>
                        </div>

                        <!-- Meta -->
                        <div class="mt-6 pt-6 border-t border-gray-100 flex flex-wrap gap-4 text-xs text-gray-400">
                            <span>Views: {{ number_format($property->views) }}</span>
                            <span>Listed: {{ $property->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Related Properties -->
                    @if($related->count())
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 mb-5">Similar Properties</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                @foreach($related as $rel)
                                    @php $rPhotos = is_array($rel->photos) ? $rel->photos : []; @endphp
                                    <a href="{{ route('property.detail', \App\Helpers\IdHasher::encode($rel->id)) }}"
                                       class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
                                        <div class="h-36 overflow-hidden bg-gray-100">
                                            @if(!empty($rPhotos))
                                                <img src="{{ $rPhotos[0] }}" alt="{{ $rel->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                                    <svg class="w-8 h-8 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-3">
                                            <p class="text-xs font-bold text-primary-600 mb-0.5">₦{{ $rel->price_display }}</p>
                                            <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $rel->title }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $rel->city }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right: Booking + Realtor -->
                <div class="space-y-6">

                    <!-- Booking Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24 overflow-y-auto max-h-[calc(100vh-7rem)]">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Book a Viewing</h3>
                        <p class="text-xs text-gray-400 mb-5">Schedule an in-person or virtual tour of this property</p>
                        <livewire:property-booking :propertyId="$property->id" />
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
