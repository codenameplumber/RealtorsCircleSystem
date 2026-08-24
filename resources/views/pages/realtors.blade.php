@extends('layouts.public')
@section('title', 'Find Realtors — Realtors Circle')

@section('content')

<div class="bg-gradient-to-br from-gray-900 to-primary-900 py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary-600/30 text-primary-300 text-xs font-semibold rounded-full mb-4 border border-primary-500/30">
            <span class="w-1.5 h-1.5 bg-primary-400 rounded-full animate-pulse"></span>
            {{ $totalRealtors }}+ Verified Professionals
        </span>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-3">Find a Realtor</h1>
        <p class="text-gray-300 text-lg max-w-xl mx-auto">
            Browse our network of verified, professional realtors across all 36 states of Nigeria.
            Filter by location, specialization, or rating.
        </p>
    </div>
</div>

<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <livewire:realtor-search />
    </div>
</section>

@endsection
