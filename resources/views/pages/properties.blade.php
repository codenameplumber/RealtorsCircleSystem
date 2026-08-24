@extends('layouts.public')

@section('title', 'Properties — Realtors Circle')
@section('meta_description', 'Browse thousands of verified properties across Nigeria. Filter by type, price, location and more.')

@section('content')

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white mb-2">Find Your Perfect Property</h1>
            <p class="text-gray-300">Thousands of verified listings across Nigeria — filtered to your needs.</p>
        </div>
    </div>

    <section class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:property-search />
        </div>
    </section>

@endsection
