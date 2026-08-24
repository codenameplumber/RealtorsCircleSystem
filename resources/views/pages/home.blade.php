@extends('layouts.public')

@section('title', 'Realtors Circle — Nigeria\'s #1 P2P Real Estate Platform')

@push('styles')
<style>
    .slide-fade-enter { animation: slideFadeIn 0.8s ease forwards; }
    @keyframes slideFadeIn { from { opacity: 0; transform: scale(1.03); } to { opacity: 1; transform: scale(1); } }

    .testimonial-track { display: flex; transition: transform 0.6s cubic-bezier(.4,0,.2,1); }

    /* Stat counter animation */
    .stat-card { backdrop-filter: blur(12px); }
</style>
@endpush

@section('content')

{{-- ===================== HERO CAROUSEL ===================== --}}
<section
    x-data="{
        active: 0,
        slides: [
            {
                img: '{{ asset('img/slider1.png') }}',
                label: 'Property Inspections',
                sub: 'Our in-house agents accompany every viewing — professional, safe, and hassle-free',
                type: ''
            },
            {
                img: '{{ asset('img/slider2.png') }}',
                label: 'Luxury Homes',
                sub: 'Exquisite 4–6 bedroom duplexes and mansions in Lekki, VI, Ikoyi and beyond',
                type: 'Duplex'
            },
            {
                img: '{{ asset('img/slider3.png') }}',
                label: 'Modern Apartments',
                sub: 'Stylish 1–3 bedroom apartments in gated estates across Lagos, Abuja and Port Harcourt',
                type: 'Apartment'
            }
        ],
        autoplay: null,
        startAutoplay() {
            this.autoplay = setInterval(() => {
                this.active = (this.active + 1) % this.slides.length
            }, 5000)
        },
        pause() { clearInterval(this.autoplay); },
        resume() { this.startAutoplay(); },
        goto(i) { this.active = i; }
    }"
    x-init="startAutoplay()"
    @mouseenter="pause()" @mouseleave="resume()"
    class="relative h-[680px] overflow-hidden">

    {{-- Slides --}}
    <template x-for="(slide, i) in slides" :key="i">
        <div
            class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000"
            :style="`background-image: url('${slide.img}')`"
            :class="active === i ? 'opacity-100 z-10' : 'opacity-0 z-0'">
        </div>
    </template>

    {{-- Dark overlay (always on top of images) --}}
    <div class="absolute inset-0 z-20" style="background: linear-gradient(135deg, rgba(0,0,0,0.78) 0%, rgba(0,0,0,0.42) 60%, rgba(4,120,87,0.25) 100%);"></div>

    {{-- Slide label badge (changes with slide) --}}
    <div class="absolute top-8 right-8 z-30 hidden md:block">
        <template x-for="(slide, i) in slides" :key="'badge-'+i">
            <div x-show="active === i" x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/20 text-white px-4 py-2 rounded-full">
                <span class="w-2 h-2 bg-primary-400 rounded-full animate-pulse"></span>
                <span class="text-sm font-medium" x-text="slide.label"></span>
            </div>
        </template>
    </div>

    {{-- FIXED CONTENT (search bar never moves) --}}
    <div class="relative z-30 h-full flex flex-col justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">

            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary-600/25 text-primary-300 text-xs font-semibold rounded-full mb-4 border border-primary-500/30">
                    <span class="w-1.5 h-1.5 bg-primary-400 rounded-full animate-pulse"></span>
                    Nigeria's #1 P2P Real Estate Platform
                </span>

                <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white leading-tight mb-3">
                    Buy, Sell &amp; Connect.<br>
                    <span class="text-primary-400">Faster. Smarter.</span>
                </h1>

                {{-- Dynamic sub-headline from slide --}}
                <div class="h-8 mb-8 overflow-hidden">
                    <template x-for="(slide, i) in slides" :key="'sub-'+i">
                        <p x-show="active === i"
                           x-transition:enter="transition duration-500" x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
                           x-transition:leave="transition duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                           class="text-base sm:text-lg text-white/80"
                           x-text="slide.sub"></p>
                    </template>
                </div>

                {{-- SEARCH BAR --}}
                <div x-data="heroSearch()" class="bg-white rounded-2xl shadow-2xl max-w-2xl overflow-hidden">
                    {{-- Tabs --}}
                    <div class="flex border-b border-gray-100">
                        @foreach(['All' => '', 'Buy' => 'For Sale', 'Rent' => 'For Rent', 'Short Let' => 'Short Let'] as $label => $val)
                            <button type="button"
                                    @click="listingType = '{{ $val }}'"
                                    :class="listingType === '{{ $val }}' ? 'text-primary-600 border-b-2 border-primary-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                                    class="flex-1 py-3 text-xs font-medium transition-colors">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    <form :action="'{{ route('properties') }}'" method="GET" class="p-3">
                        <input type="hidden" name="listingType" :value="listingType">
                        <input type="hidden" name="type"        :value="propType">
                        <div class="flex flex-col gap-2">
                            {{-- Row 1: keyword search + state --}}
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" name="search"
                                           placeholder="LGA, street, or keyword..."
                                           class="w-full pl-9 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                <select name="state" x-model="selectedState" @change="selectedLga=''"
                                        class="w-36 px-3 py-2.5 text-sm text-gray-700 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                                    <option value="">All States</option>
                                    @foreach(\App\Data\NigeriaLocations::states() as $s)
                                        <option value="{{ $s }}">{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Row 2: LGA + type + search button --}}
                            <div class="flex gap-2">
                                <select name="lga" x-model="selectedLga"
                                        :disabled="!selectedState"
                                        class="flex-1 px-3 py-2.5 text-sm text-gray-700 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white disabled:opacity-50">
                                    <option value="">
                                        <span x-text="selectedState ? 'All LGAs' : 'Select state first'"></span>
                                    </option>
                                    <template x-for="l in lgas" :key="l">
                                        <option :value="l" x-text="l"></option>
                                    </template>
                                </select>
                                <select x-model="propType"
                                        class="w-32 px-3 py-2.5 text-sm text-gray-700 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                                    <option value="">Any Type</option>
                                    @foreach(['Apartment','Duplex','Bungalow','Land','Commercial','Penthouse','Studio'] as $pt)
                                        <option value="{{ $pt }}">{{ $pt }}</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                        class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors whitespace-nowrap flex items-center gap-1.5 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                @push('scripts')
                <script>
                const nigeriaLgas = @json(\App\Data\NigeriaLocations::statesWithLgas());
                function heroSearch() {
                    return {
                        listingType: '',
                        propType: '',
                        selectedState: '',
                        selectedLga: '',
                        get lgas() {
                            return this.selectedState ? (nigeriaLgas[this.selectedState] || []) : [];
                        }
                    }
                }
                </script>
                @endpush

                <div class="flex flex-wrap gap-3 mt-6">
                    <a href="{{ route('properties') }}" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        Browse Properties
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold rounded-xl border border-white/30 transition-colors backdrop-blur-sm">
                        Join as a Realtor →
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Slide indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex items-center gap-2">
        <template x-for="(slide, i) in slides" :key="'dot-'+i">
            <button @click="goto(i); pause(); resume()"
                    :class="active === i ? 'w-8 bg-primary-400' : 'w-2 bg-white/40 hover:bg-white/70'"
                    class="h-2 rounded-full transition-all duration-300"></button>
        </template>
    </div>

    {{-- Arrow nav --}}
    <button @click="active = (active - 1 + slides.length) % slides.length"
            class="absolute left-4 top-1/2 -translate-y-1/2 z-30 w-10 h-10 bg-white/15 hover:bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button @click="active = (active + 1) % slides.length"
            class="absolute right-4 top-1/2 -translate-y-1/2 z-30 w-10 h-10 bg-white/15 hover:bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
</section>

{{-- ===================== STATS BAR ===================== --}}
<section class="py-8 bg-primary-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-6 text-center">
            <div>
                <div class="text-3xl font-extrabold text-white mb-0.5">{{ number_format($stats['properties']) }}+</div>
                <div class="text-xs text-primary-200 font-medium uppercase tracking-wider">Verified Properties</div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-white mb-0.5">{{ number_format($stats['realtors']) }}+</div>
                <div class="text-xs text-primary-200 font-medium uppercase tracking-wider">Active Realtors</div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-white mb-0.5">{{ $stats['cities'] }}+</div>
                <div class="text-xs text-primary-200 font-medium uppercase tracking-wider">Cities Covered</div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== WHAT IS REALTORS CIRCLE ===================== --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">The Platform</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-2 mb-5 leading-tight">
                    Real Estate is a Team Sport.<br>
                    <span class="text-primary-600">We Built the Team.</span>
                </h2>
                <p class="text-gray-600 leading-relaxed mb-4 text-lg">
                    Realtors Circle is Nigeria's only <strong>Peer-to-Peer (P2P) real estate network</strong> where realtors collaborate — not compete — to close deals faster and earn more.
                </p>
                <p class="text-gray-500 leading-relaxed mb-6">
                    When you have a client looking for a property you don't have, simply connect them with another realtor on the network who does. You refer, they list, and when the deal closes — <strong>you both earn commission</strong>. Our in-house agents verify every property and mediate on-site to keep every transaction clean, fast, and legitimate.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors">
                        Join the Network
                    </a>
                    <a href="{{ route('about') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-200 text-gray-700 hover:border-primary-400 hover:text-primary-600 font-semibold rounded-xl transition-colors">
                        Learn More →
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['emoji' => '🤝', 'title' => 'P2P Commission Split', 'desc' => 'Both the referring and listing realtors earn — automatically split at closing.'],
                    ['emoji' => '🔍', 'title' => 'In-House Verification', 'desc' => 'Our agents physically inspect every property before it goes live.'],
                    ['emoji' => '⚡', 'title' => 'Faster Closings', 'desc' => 'Network referrals match buyers to the right property in days, not months.'],
                    ['emoji' => '🛡️', 'title' => 'Fraud-Free Guarantee', 'desc' => 'On-site mediation by Realtors Circle agents ensures zero fraudulent deals.'],
                ] as $f)
                    <div class="bg-gray-50 rounded-2xl p-5 hover:shadow-md transition-shadow">
                        <div class="text-3xl mb-3">{{ $f['emoji'] }}</div>
                        <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $f['title'] }}</h3>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ===================== WHY BETTER ===================== --}}
<section class="py-20 bg-gray-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 left-10 w-64 h-64 bg-primary-400 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-64 h-64 bg-primary-600 rounded-full blur-3xl"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-14">
            <span class="text-primary-400 font-semibold text-sm uppercase tracking-wider">The Difference</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2 mb-4">
                Why Realtors Circle Beats Every<br>Other Platform
            </h2>
            <p class="text-gray-400 max-w-2xl mx-auto text-lg">Traditional platforms list properties. We move them — through the power of a connected realtor network.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            @foreach([
                ['icon' => '💸', 'title' => 'Everyone Earns More', 'desc' => 'On PropertyPro or Jiji, only the listing agent earns. On Realtors Circle, the agent who referred the client also gets their cut — automatically. No arguments, no delays.', 'stat' => '2× More Income'],
                ['icon' => '🏃', 'title' => 'Deals Close in Days', 'desc' => 'Traditional real estate takes 3–6 months due to fragmented networks and lack of trust. Our P2P matching connects the right buyer to the right property through trusted realtor referrals — closing in weeks.', 'stat' => '60% Faster Closings'],
                ['icon' => '🔐', 'title' => 'Zero-Fraud Transactions', 'desc' => 'Other platforms are just listing directories — no verification, no oversight. We send an in-house agent to every site, verify ownership documents, and mediate the closing. Your client is protected.', 'stat' => '100% Verified Deals'],
            ] as $w)
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-colors">
                    <div class="text-4xl mb-4">{{ $w['icon'] }}</div>
                    <div class="inline-block px-3 py-1 bg-primary-600/30 text-primary-400 text-xs font-bold rounded-full mb-3">{{ $w['stat'] }}</div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ $w['title'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $w['desc'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Comparison table --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
            <div class="grid grid-cols-3 text-sm">
                <div class="p-4 border-b border-white/10 font-semibold text-gray-400">Feature</div>
                <div class="p-4 border-b border-white/10 font-semibold text-gray-400 text-center">Other Platforms</div>
                <div class="p-4 border-b border-white/10 font-semibold text-primary-400 text-center">Realtors Circle</div>

                @foreach([
                    ['label' => 'Commission for referring agent', 'other' => '❌ None', 'us' => '✅ Automatic split'],
                    ['label' => 'Property verification (physical)', 'other' => '❌ None', 'us' => '✅ In-house agents'],
                    ['label' => 'On-site deal mediation', 'other' => '❌ Not offered', 'us' => '✅ Always'],
                    ['label' => 'P2P realtor network', 'other' => '❌ Siloed', 'us' => '✅ Fully connected'],
                    ['label' => 'Fraud protection guarantee', 'other' => '⚠️ Disclaimer only', 'us' => '✅ Active oversight'],
                ] as $row)
                    <div class="p-4 border-b border-white/5 text-gray-300">{{ $row['label'] }}</div>
                    <div class="p-4 border-b border-white/5 text-center text-gray-500 text-sm">{{ $row['other'] }}</div>
                    <div class="p-4 border-b border-white/5 text-center text-sm font-medium text-primary-300">{{ $row['us'] }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ===================== HOW IT WORKS ===================== --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">The Process</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mt-2">How Deals Get Done on Realtors Circle</h2>
            <p class="text-gray-500 mt-3 max-w-xl mx-auto">A smarter, faster flow that eliminates the bottlenecks of traditional real estate.</p>
        </div>

        <div class="relative">
            {{-- Connecting line (desktop) --}}
            <div class="hidden md:block absolute top-12 left-1/6 right-1/6 h-0.5 bg-gradient-to-r from-primary-100 via-primary-400 to-primary-100"></div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                @foreach([
                    ['num' => '01', 'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 'title' => 'Realtor Registers', 'desc' => 'A verified Nigerian realtor joins the network with credentials. Admin approves within 24 hours.'],
                    ['num' => '02', 'icon' => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z', 'title' => 'Client Has a Need', 'desc' => 'A realtor has a buyer but not the right property. They browse the network and find a match from a fellow realtor.'],
                    ['num' => '03', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0', 'title' => 'P2P Referral Made', 'desc' => 'Referring realtor links the client to the listing realtor. Both are now tied to this deal — and both earn on close.'],
                    ['num' => '04', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Deal Closed & Split', 'desc' => 'Our in-house agent verifies on-site, mediates the closing, and commissions are distributed automatically.'],
                ] as $step)
                    <div class="text-center relative">
                        <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-600/30">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-4xl font-black text-gray-100 absolute -top-2 -right-2 select-none">{{ $step['num'] }}</span>
                        <h3 class="font-bold text-gray-900 mb-2">{{ $step['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ===================== BENEFITS ===================== --}}
<section class="py-20 bg-primary-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Who Benefits</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mt-2">Built for Everyone in the Deal</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-primary-100">
                <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">For Realtors</h3>
                <ul class="space-y-2.5">
                    @foreach(['Earn referral commissions on every deal you send', 'Access a network of thousands of properties you don\'t have to list', 'Close more deals by collaborating, not competing', 'Automated commission tracking — no disputes', 'Verified leads from trusted realtor referrals'] as $b)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-primary-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $b }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-primary-600 rounded-2xl p-8 shadow-lg">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">For Buyers & Clients</h3>
                <ul class="space-y-2.5">
                    @foreach(['Access thousands of verified, fraud-free properties', 'On-site inspection by our in-house agents', 'Multiple realtor perspectives on one platform', 'No pressure — transparent pricing, no hidden fees', 'Close with confidence and legal documentation support'] as $b)
                        <li class="flex items-start gap-2 text-sm text-primary-100">
                            <svg class="w-4 h-4 text-primary-200 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $b }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-primary-100">
                <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">For the Industry</h3>
                <ul class="space-y-2.5">
                    @foreach(['A transparent, auditable commission system', 'Reduced fraud through in-house oversight', 'Faster market liquidity — properties move quicker', 'More professional realtor standards enforced', 'A digital-first real estate economy in Nigeria'] as $b)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-primary-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $b }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ===================== FEATURED PROPERTIES ===================== --}}
@if($featuredProperties->count())
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Fresh Listings</span>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-1">Featured Properties</h2>
            </div>
            <a href="{{ route('properties') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredProperties as $property)
                @php $photos = is_array($property->photos) ? $property->photos : []; @endphp
                <div class="property-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                    <div class="relative h-52 overflow-hidden bg-gray-100">
                        @if(!empty($photos))
                            <img src="{{ $photos[0] }}" alt="{{ $property->title }}" class="property-image w-full h-full object-cover transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
                                <svg class="w-12 h-12 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                        @endif
                        <div class="absolute top-3 left-3 flex gap-1.5">
                            <span class="px-2 py-0.5 text-xs font-semibold bg-primary-600 text-white rounded-full">Verified</span>
                            <span class="px-2 py-0.5 text-xs font-semibold bg-white text-gray-700 rounded-full">{{ $property->type }}</span>
                        </div>
                        <div class="absolute bottom-3 right-3">
                            <span class="px-3 py-1 text-sm font-bold bg-white/95 text-gray-900 rounded-xl shadow-sm">₦{{ $property->price_display }}</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center gap-1 text-xs text-gray-500 mb-1.5">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $property->neigborhood }}, {{ $property->city }}
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-3 line-clamp-1 group-hover:text-primary-600 transition-colors">{{ $property->title }}</h3>
                        <a href="{{ route('property.detail', \App\Helpers\IdHasher::encode($property->id)) }}" class="block text-center py-2 text-sm font-semibold text-primary-600 border border-primary-600 rounded-lg hover:bg-primary-600 hover:text-white transition-colors">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== TESTIMONIALS SLIDER ===================== --}}
<section class="py-20 bg-gray-50 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Success Stories</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mt-2">What Our Community Says</h2>
        </div>

        <div
            x-data="{
                active: 0,
                testimonials: [
                    { name: 'Adebayo Okonkwo', role: 'Property Investor, Lagos', initials: 'AO', review: 'I referred a client to a colleague on Realtors Circle and we closed in 3 weeks. I earned ₦450,000 in referral commission without listing a single property. This platform changed my income model completely.' },
                    { name: 'Fatima Al-Hassan', role: 'First-time Buyer, Abuja', initials: 'FA', review: 'The in-house verification agent gave me absolute peace of mind. On other platforms I was scared of fraud. Here, an agent came to the site, confirmed everything, and we signed in one day. Incredible experience.' },
                    { name: 'Chukwuemeka Dike', role: 'Senior Realtor, Port Harcourt', initials: 'CD', review: 'I\'ve been in real estate for 12 years. Realtors Circle is the first platform that actually rewards collaboration. My deal volume doubled in 6 months because I stopped competing and started connecting.' },
                    { name: 'Sarah Johnson', role: 'Property Developer, Lagos', initials: 'SJ', review: 'Listed 8 properties on Realtors Circle and had all of them receive inquiry bookings within the first week. The quality of leads from the realtor network is so much better than any listing site I\'ve used.' },
                    { name: 'Michael Obi', role: 'Commercial Real Estate, Lekki', initials: 'MO', review: 'The commission split system is transparent and automated. No more chasing other agents for referral fees. The money lands in my account after every closed deal. Professional, reliable, and fair.' }
                ],
                autoplay: null,
                startAutoplay() { this.autoplay = setInterval(() => this.next(), 5000) },
                pause() { clearInterval(this.autoplay) },
                resume() { this.startAutoplay() },
                next() { this.active = (this.active + 1) % this.testimonials.length },
                prev() { this.active = (this.active - 1 + this.testimonials.length) % this.testimonials.length }
            }"
            x-init="startAutoplay()"
            @mouseenter="pause()" @mouseleave="resume()">

            {{-- Slider viewport --}}
            <div class="relative overflow-hidden">
                <div class="flex transition-transform duration-700 ease-in-out"
                     :style="`transform: translateX(-${active * 100}%)`">
                    <template x-for="(t, i) in testimonials" :key="i">
                        <div class="min-w-full px-4">
                            <div class="max-w-3xl mx-auto bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                                <div class="flex gap-1 mb-5">
                                    <template x-for="s in 5" :key="s">
                                        <svg class="w-5 h-5 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    </template>
                                </div>
                                <p class="text-gray-700 text-lg leading-relaxed mb-6 italic" x-text="t.review"></p>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0" x-text="t.initials"></div>
                                    <div>
                                        <p class="font-bold text-gray-900" x-text="t.name"></p>
                                        <p class="text-sm text-gray-400" x-text="t.role"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Controls --}}
            <div class="flex items-center justify-center gap-4 mt-8">
                <button @click="prev()" class="w-10 h-10 bg-white border border-gray-200 hover:border-primary-400 text-gray-600 hover:text-primary-600 rounded-full flex items-center justify-center transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <div class="flex gap-2">
                    <template x-for="(t, i) in testimonials" :key="'dot-'+i">
                        <button @click="active = i"
                                :class="active === i ? 'w-8 bg-primary-600' : 'w-2 bg-gray-300'"
                                class="h-2 rounded-full transition-all duration-300"></button>
                    </template>
                </div>

                <button @click="next()" class="w-10 h-10 bg-white border border-gray-200 hover:border-primary-400 text-gray-600 hover:text-primary-600 rounded-full flex items-center justify-center transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- ===================== CTA ===================== --}}
<section class="py-20 bg-primary-600 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-white rounded-full"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white rounded-full"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
            Stop Losing Deals.<br>Start Closing Them Together.
        </h2>
        <p class="text-primary-100 text-lg mb-8 max-w-xl mx-auto">Join Realtors Circle today — earn more, close faster, and build a real estate career that actually scales.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-primary-700 font-bold rounded-xl hover:bg-primary-50 transition-colors shadow-sm text-lg">
                Join the Network — Free
            </a>
            <a href="{{ route('properties') }}" class="px-8 py-4 border-2 border-white/40 text-white font-bold rounded-xl hover:bg-white/10 transition-colors text-lg">
                Browse Properties
            </a>
        </div>
        <p class="text-primary-200 text-sm mt-6">No monthly fees. Earn only when deals close.</p>
    </div>
</section>

@endsection
