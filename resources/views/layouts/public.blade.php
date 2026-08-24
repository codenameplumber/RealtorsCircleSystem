<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Realtors Circle') — Find Your Perfect Property</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Connect with verified realtors and discover your dream property with Realtors Circle.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Realtors Circle">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Realtors Circle')))">
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Connect with verified realtors and discover your dream property with Realtors Circle.')))">
    <meta property="og:image" content="@yield('og_image', asset('img/logo.png'))">
    <meta property="og:url" content="@yield('canonical', url()->current())">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Realtors Circle')))">
    <meta name="twitter:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Connect with verified realtors and discover your dream property with Realtors Circle.')))">
    <meta name="twitter:image" content="@yield('og_image', asset('img/logo.png'))">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:  '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        .hero-gradient { background: linear-gradient(135deg, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.45) 100%); }

        .property-card:hover .property-image { transform: scale(1.05); }

        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #059669;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after,
        .nav-link.active::after { width: 100%; }

        /* Smooth fade-in for Livewire page transitions */
        .livewire-fade { animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        /* Custom range slider */
        input[type=range] { accent-color: #059669; }

        /* Toast notification */
        .toast-enter { animation: slideInRight 0.4s ease; }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>

    @stack('styles')
</head>

<body class="font-sans bg-white text-gray-900 antialiased">

    <!-- NAVBAR -->
    <header x-data="{ open: false, scrolled: false }"
            x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
            :class="scrolled ? 'shadow-md bg-white' : 'bg-white border-b border-gray-100'"
            class="sticky top-0 z-50 w-full transition-shadow duration-300">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-primary-600 tracking-tight">Realtors Circle</span>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ url('/') }}" class="nav-link text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors {{ request()->is('/') ? 'text-primary-600 active' : '' }}">Home</a>
                    <a href="{{ url('/properties') }}" class="nav-link text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors {{ request()->is('properties*') ? 'text-primary-600 active' : '' }}">Properties</a>
                    <a href="{{ url('/realtors') }}" class="nav-link text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors {{ request()->is('realtors*') ? 'text-primary-600 active' : '' }}">Realtors</a>
                    <a href="{{ url('/about') }}" class="nav-link text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors {{ request()->is('about') ? 'text-primary-600 active' : '' }}">About</a>
                    <a href="{{ url('/contact') }}" class="nav-link text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors {{ request()->is('contact') ? 'text-primary-600 active' : '' }}">Contact</a>
                </nav>

                <!-- Desktop Auth Buttons -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ url('realtor/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Logout</button>
                        </form>
                    @else
                        <a href="{{ url('/login') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors px-4 py-2 rounded-lg hover:bg-gray-100">Log in</a>
                        <a href="{{ url('/register') }}" class="text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 px-4 py-2 rounded-lg transition-colors shadow-sm">Sign up</a>
                    @endauth
                </div>

                <!-- Mobile Hamburger -->
                <button @click="open = !open" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-4 space-y-1">
                <a href="{{ url('/') }}" @click="open=false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">Home</a>
                <a href="{{ url('/properties') }}" @click="open=false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">Properties</a>
                <a href="{{ url('/realtors') }}" @click="open=false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">Realtors</a>
                <a href="{{ url('/about') }}" @click="open=false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">About</a>
                <a href="{{ url('/contact') }}" @click="open=false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">Contact</a>
                <div class="pt-3 border-t border-gray-100 flex gap-3">
                    <a href="{{ url('/login') }}" class="flex-1 text-center px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Log in</a>
                    <a href="{{ url('/register') }}" class="flex-1 text-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Sign up</a>
                </div>
            </div>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

                <!-- Brand -->
                <div class="md:col-span-1 space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-primary-400">Realtors Circle</span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">A trusted P2P marketplace connecting real estate professionals to deliver seamless property transactions across Nigeria.</p>
                    <div class="flex gap-3">
                        <a href="https://web.facebook.com/profile.php?id=61562754255807" target="_blank" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary-600 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://www.instagram.com/realtors_circle/" target="_blank" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary-600 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-white">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-primary-400 transition-colors">Home</a></li>
                        <li><a href="{{ url('/properties') }}" class="text-gray-400 hover:text-primary-400 transition-colors">Properties</a></li>
                        <li><a href="{{ url('/realtors') }}" class="text-gray-400 hover:text-primary-400 transition-colors">Find Realtors</a></li>
                        <li><a href="{{ url('/about') }}" class="text-gray-400 hover:text-primary-400 transition-colors">About Us</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-white">Resources</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/contact') }}" class="text-gray-400 hover:text-primary-400 transition-colors">Contact Us</a></li>
                        <li><a href="{{ url('/terms') }}" class="text-gray-400 hover:text-primary-400 transition-colors">Terms & Conditions</a></li>
                        <li><a href="{{ url('/privacy') }}" class="text-gray-400 hover:text-primary-400 transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- For Realtors -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-white">For Realtors</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/register') }}" class="text-gray-400 hover:text-primary-400 transition-colors">Join as a Realtor</a></li>
                        <li><a href="{{ url('/login') }}" class="text-gray-400 hover:text-primary-400 transition-colors">Login to Dashboard</a></li>
                    </ul>
                    <div class="pt-2">
                        <p class="text-xs text-gray-500">Contact us</p>
                        <p class="text-sm text-gray-400 mt-1">info@realtorscircle.com</p>
                        <p class="text-sm text-gray-400">+234 800 123 4567</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-8 border-t border-gray-800 flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-sm text-gray-500">© {{ date('Y') }} Realtors Circle. All rights reserved.</p>
                <p class="text-sm text-gray-500">
                    Built with <span class="text-red-400">❤️</span> by
                    <span class="text-primary-400 font-semibold">Charsac Technologies</span>
                </p>
            </div>
        </div>
    </footer>

    <!-- Livewire Scripts -->
    @livewireScripts

    @stack('scripts')
</body>
</html>
