@extends('layouts.public')

@section('title', 'Contact Us — Realtors Circle')

@section('content')

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-3">Contact Us</h1>
            <p class="text-gray-300 max-w-xl mx-auto">Have questions? We're here to help. Reach out through any channel below.</p>
        </div>
    </div>

    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Contact Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-14">
                @foreach([
                    ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Our Location', 'lines' => ['123 Real Estate Avenue', 'Victoria Island, Lagos', 'Nigeria']],
                    ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'title' => 'Contact Information', 'lines' => ['+234 800 123 4567', 'info@realtorscircle.com', 'Mon–Fri, 9 AM – 6 PM']],
                    ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Business Hours', 'lines' => ['Monday – Friday: 9:00 AM – 6:00 PM', 'Saturday: 10:00 AM – 4:00 PM', 'Sunday: Closed']],
                ] as $card)
                    <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100">
                        <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $card['icon'] }}"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-3">{{ $card['title'] }}</h3>
                        @foreach($card['lines'] as $line)
                            <p class="text-sm text-gray-500">{{ $line }}</p>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <!-- Map + Form -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                <!-- Map Placeholder -->
                <div class="bg-gray-200 rounded-2xl h-[450px] flex flex-col items-center justify-center text-gray-400 overflow-hidden relative">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="font-medium text-gray-500">Victoria Island, Lagos</p>
                    <p class="text-sm mt-1 text-gray-400">Map coming soon</p>

                    <!-- Decorative pins -->
                    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2">
                        <div class="w-5 h-5 bg-primary-600 rounded-full shadow-lg shadow-primary-600/40 animate-pulse"></div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Send Us a Message</h3>
                    <livewire:contact-form />
                </div>
            </div>
        </div>
    </section>

@endsection
