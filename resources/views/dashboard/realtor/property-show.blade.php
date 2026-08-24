@extends('layouts.dashboard')
@section('title', $property->title)
@section('role_label', 'Realtor')
@section('page_title', 'Property Details')
@section('page_subtitle', $property->title)
@section('sidebar_nav') @include('dashboard.realtor._sidebar') @endsection

@section('content')
<div class="max-w-4xl">

    {{-- Back + Actions --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('realtor.properties') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Properties
        </a>
        <a href="{{ route('realtor.properties.edit', \App\Helpers\IdHasher::encode($property->id)) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Property
        </a>
    </div>

    {{-- Photos --}}
    @php $photos = $property->photos ?? []; @endphp
    @if(count($photos))
        <div class="mb-6">
            @if(count($photos) === 1)
                <div class="w-full h-64 sm:h-80 rounded-2xl overflow-hidden bg-gray-100">
                    <img src="{{ $photos[0] }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div class="col-span-2 row-span-2 rounded-2xl overflow-hidden bg-gray-100 aspect-video sm:aspect-auto sm:h-64">
                        <img src="{{ $photos[0] }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                    </div>
                    @foreach(array_slice($photos, 1, 4) as $i => $photo)
                        <div class="rounded-2xl overflow-hidden bg-gray-100 aspect-video relative">
                            <img src="{{ $photo }}" alt="Photo {{ $i + 2 }}" class="w-full h-full object-cover">
                            @if($i === 3 && count($photos) > 5)
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center rounded-2xl">
                                    <span class="text-white text-lg font-bold">+{{ count($photos) - 5 }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="w-full h-48 rounded-2xl bg-gray-100 flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Header --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl font-bold text-gray-900 leading-snug">{{ $property->title }}</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $property->neigborhood }}, {{ $property->city }}, {{ $property->state }}
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $property->approval_status === 'approved' ? 'bg-green-100 text-green-700' : ($property->approval_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ ucfirst($property->approval_status) }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            {{ $property->listing_type }}
                        </span>
                    </div>
                </div>

                <p class="text-2xl font-bold text-primary-600 mb-4">₦{{ $property->price_display }}</p>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach([
                        ['label' => 'Type',    'value' => $property->type],
                        ['label' => 'LGA',     'value' => $property->localarea],
                        ['label' => 'Country', 'value' => $property->country],
                        ['label' => 'Views',   'value' => number_format($property->views)],
                        ['label' => 'Listed',  'value' => $property->created_at->format('M j, Y')],
                        ['label' => 'Status',  'value' => ucfirst($property->listing_status)],
                    ] as $detail)
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 mb-0.5">{{ $detail['label'] }}</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detail['value'] ?? '—' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-3">Description</h3>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $property->description }}</p>
            </div>

            {{-- Amenities --}}
            @php $amenities = $property->amenities ?? []; @endphp
            @if(count($amenities))
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Amenities</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($amenities as $amenity)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 text-primary-700 text-xs font-medium rounded-xl">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $amenity }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Videos --}}
            @php $videos = $property->videos ?? []; @endphp
            @if(count($videos))
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Videos</h3>
                    <div class="space-y-3">
                        @foreach($videos as $i => $video)
                            <video controls class="w-full rounded-xl bg-black" style="max-height:300px">
                                <source src="{{ $video }}">
                                Your browser does not support video playback.
                            </video>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar Panel --}}
        <div class="space-y-4">

            {{-- Quick Actions --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h4 class="font-semibold text-gray-900 mb-3 text-sm">Quick Actions</h4>
                <div class="space-y-2">
                    <a href="{{ route('realtor.properties.edit', \App\Helpers\IdHasher::encode($property->id)) }}"
                       class="flex items-center gap-2.5 w-full px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Property
                    </a>
                    <a href="{{ route('realtor.leads') }}"
                       class="flex items-center gap-2.5 w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        View Leads
                    </a>
                </div>
            </div>

            {{-- Location Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h4 class="font-semibold text-gray-900 mb-3 text-sm">Location</h4>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Street</p>
                        <p class="text-gray-700 font-medium">{{ $property->street }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Neighborhood</p>
                        <p class="text-gray-700 font-medium">{{ $property->neigborhood }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">City / LGA</p>
                        <p class="text-gray-700 font-medium">{{ $property->city }}, {{ $property->localarea }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">State</p>
                        <p class="text-gray-700 font-medium">{{ $property->state }}</p>
                    </div>
                </div>
            </div>

            {{-- All Photos Strip --}}
            @if(count($photos) > 1)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h4 class="font-semibold text-gray-900 mb-3 text-sm">All Photos ({{ count($photos) }})</h4>
                    <div class="grid grid-cols-3 gap-1.5">
                        @foreach($photos as $photo)
                            <a href="{{ $photo }}" target="_blank"
                               class="aspect-square rounded-lg overflow-hidden bg-gray-100 block">
                                <img src="{{ $photo }}" class="w-full h-full object-cover hover:opacity-80 transition-opacity">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
