@extends('layouts.public')

@section('title', 'About Us — Realtors Circle')
@section('meta_description', 'Learn about Realtors Circle — Nigeria\'s trusted P2P real estate platform connecting realtors and clients.')

@section('content')

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-3">About Realtors Circle</h1>
            <p class="text-gray-300 max-w-xl mx-auto">A trusted P2P marketplace transforming real estate in Nigeria</p>
        </div>
    </div>

    <!-- Story Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
                <div>
                    <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Our Story</span>
                    <h2 class="text-3xl font-bold text-gray-900 mt-2 mb-5">Built for Nigerian Real Estate Professionals</h2>
                    <div class="space-y-4 text-gray-600 leading-relaxed">
                        <p>Realtors Circle is a premier real estate platform dedicated to connecting property buyers with verified, professional realtors across Nigeria. Founded with a mission to transform the real estate industry by creating a transparent, efficient, and trustworthy marketplace for all stakeholders.</p>
                        <p>We understand that finding the perfect property or the right realtor can be challenging. That's why we built a P2P platform that simplifies the process, ensures transparency, and provides the tools necessary for successful real estate transactions — including a commission-sharing model that rewards every party in the deal fairly.</p>
                        <p>Our in-house agents play a critical role: they verify properties on-site and act as neutral mediators to ensure smooth transactions between the referring realtor, the listing realtor, and the client.</p>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl h-80 flex items-center justify-center">
                        <svg class="w-32 h-32 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="absolute -bottom-4 -left-4 bg-white rounded-xl shadow-lg p-4 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">100% Verified</div>
                                <div class="text-xs text-gray-400">All listings inspected</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Our Mission & Vision</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                    <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Our Mission</h3>
                    <p class="text-gray-600 leading-relaxed">To create a trusted P2P marketplace that connects property seekers with verified realtors, ensuring transparency, efficiency, and fair commission distribution for all stakeholders.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                    <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Our Vision</h3>
                    <p class="text-gray-600 leading-relaxed">To become the leading real estate platform in Africa, known for our commitment to quality, integrity, and innovation in the property market — making every transaction seamless and profitable for all parties.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">What We Stand For</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">Our Core Values</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Integrity', 'desc' => 'We uphold the highest standards of honesty and ethical conduct in all our dealings.'],
                    ['icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'title' => 'Transparency', 'desc' => 'We believe in clear, open communication and providing complete information to all parties.'],
                    ['icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'title' => 'Excellence', 'desc' => 'We strive for excellence in every aspect of our service and continuously improve our platform.'],
                ] as $v)
                    <div class="bg-primary-50 rounded-2xl p-8 text-center">
                        <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $v['icon'] }}"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $v['title'] }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $v['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-primary-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-white mb-3">Join the Realtors Circle Community</h2>
            <p class="text-primary-100 mb-8 max-w-lg mx-auto">Whether you're a property seeker or a professional realtor, we invite you to experience the future of real estate.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-3 bg-white text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition-colors">Join as a Realtor</a>
                <a href="{{ route('contact') }}" class="px-8 py-3 border-2 border-white/40 text-white font-semibold rounded-xl hover:bg-white/10 transition-colors">Contact Us</a>
            </div>
        </div>
    </section>

@endsection
