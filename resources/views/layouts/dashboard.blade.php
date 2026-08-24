<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Realtors Circle</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:  '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0',
                            300: '#6ee7b7', 400: '#34d399', 500: '#10b981',
                            600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b'
                        }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    boxShadow: {
                        'sidebar': '4px 0 24px -4px rgba(0,0,0,0.06)',
                        'card':    '0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06)',
                        'nav':     '0 4px 6px -1px rgba(5,150,105,0.15)',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        /* Sidebar nav link base */
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 12px;
            font-size: 0.8125rem; font-weight: 500;
            transition: all 0.15s ease;
            color: #6b7280; position: relative;
        }
        .nav-link:hover {
            background-color: #ecfdf5;
            color: #059669;
        }
        .nav-link:hover .nav-icon { color: #059669; }
        .nav-link.active {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px -2px rgba(5,150,105,0.35);
        }
        .nav-link.active .nav-icon { color: #ffffff; }
        .nav-icon {
            width: 16px; height: 16px; flex-shrink: 0;
            color: #9ca3af; transition: color 0.15s;
        }

        /* Stat card */
        .stat-card {
            background: white; border-radius: 16px;
            border: 1px solid #f3f4f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 20px;
        }

        /* Scrollbar thin */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

        /* Badge pulse */
        @keyframes badge-pulse { 0%,100%{opacity:1} 50%{opacity:.7} }
        .badge-pulse { animation: badge-pulse 2s ease-in-out infinite; }
    </style>

    @stack('styles')
</head>

<body class="font-sans bg-gray-50 text-gray-900 antialiased"
      x-data="{ sidebarOpen: false, notifOpen: false, userOpen: false }">

<div class="flex h-screen overflow-hidden">

    {{-- ============================================================ SIDEBAR --}}

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white flex flex-col transition-transform duration-300 lg:static lg:translate-x-0 lg:z-auto shadow-sidebar">

        {{-- ---- Logo ---- --}}
        <div class="h-16 flex items-center px-5 border-b border-gray-100/80 flex-shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-md shadow-primary-200/50">
                    <svg class="w-4.5 h-4.5 text-white w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-900 text-sm leading-none">Realtors Circle</p>
                    <p class="text-[10px] text-primary-600 font-medium tracking-wide mt-0.5">@yield('role_label', 'Dashboard')</p>
                </div>
            </a>
        </div>

        {{-- ---- User Card ---- --}}
        @if(session('agent_id'))
        @php $sidebarAgent = \App\Models\AgentsTable::find(session('agent_id')); @endphp
        <div class="px-4 py-3 border-b border-gray-100/80 flex-shrink-0">
            <div class="flex items-center gap-3 bg-gradient-to-br from-primary-50 to-emerald-50/60 rounded-xl p-3">
                <div class="relative flex-shrink-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ strtoupper(substr($sidebarAgent?->First_name ?? 'A', 0, 1)) }}{{ strtoupper(substr($sidebarAgent?->Last_name ?? '', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 border-2 border-white rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900 truncate">{{ $sidebarAgent?->First_name }} {{ $sidebarAgent?->Last_name }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ $sidebarAgent?->Assigned_area }}, {{ $sidebarAgent?->State }}</p>
                </div>
            </div>
        </div>
        @elseif(session('admin_id'))
        <div class="px-4 py-3 border-b border-gray-100/80 flex-shrink-0">
            <div class="flex items-center gap-3 bg-gradient-to-br from-primary-50 to-emerald-50/60 rounded-xl p-3">
                <div class="relative flex-shrink-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ strtoupper(substr(session('admin_name', 'A'), 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 border-2 border-white rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900 truncate">{{ session('admin_name') }}</p>
                    <p class="text-[10px] text-primary-600 font-medium truncate">{{ ucfirst(str_replace('_', ' ', session('admin_role', 'admin'))) }}</p>
                </div>
            </div>
        </div>
        @elseif(session('agency_id'))
        @php $sidebarAgency = \App\Models\AgencyPartner::find(session('agency_id')); @endphp
        <div class="px-4 py-3 border-b border-gray-100/80 flex-shrink-0">
            <div class="flex items-center gap-3 bg-gradient-to-br from-primary-50 to-emerald-50/60 rounded-xl p-3">
                <div class="relative flex-shrink-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-sm">
                        @php
                            $agWords = explode(' ', $sidebarAgency?->company_name ?? 'A');
                            echo strtoupper(substr($agWords[0] ?? 'A', 0, 1)) . strtoupper(substr($agWords[1] ?? '', 0, 1));
                        @endphp
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 border-2 border-white rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900 truncate">{{ $sidebarAgency?->company_name }}</p>
                    <p class="text-[10px] text-primary-600 font-medium truncate">Agency Partner</p>
                </div>
            </div>
        </div>
        @elseauth
        <div class="px-4 py-3 border-b border-gray-100/80 flex-shrink-0">
            <div class="flex items-center gap-3 bg-gradient-to-br from-primary-50 to-emerald-50/60 rounded-xl p-3">
                <div class="relative flex-shrink-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ strtoupper(substr(auth()->user()->First_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->Last_name ?? '', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 border-2 border-white rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900 truncate">{{ auth()->user()->First_name }} {{ auth()->user()->Last_name }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ auth()->user()->Email }}</p>
                </div>
            </div>
        </div>
        @endauth

        {{-- ---- Navigation ---- --}}
        <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4 space-y-0.5">
            @yield('sidebar_nav')
        </nav>

        {{-- ---- Footer ---- --}}
        @auth
        <div class="px-3 py-3 border-t border-gray-100/80 flex-shrink-0 space-y-0.5">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
        @endauth
    </aside>

    {{-- ============================================================ MAIN --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- ---- Top Header ---- --}}
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 z-30">

            {{-- Left: hamburger + page title --}}
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarOpen=!sidebarOpen"
                        class="lg:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-sm font-bold text-gray-900 truncate">@yield('page_title', 'Dashboard')</h1>
                    <p class="text-xs text-gray-400 hidden sm:block truncate">@yield('page_subtitle', date('l, F j Y'))</p>
                </div>
            </div>

            {{-- Right: actions --}}
            <div class="flex items-center gap-2 flex-shrink-0">

                {{-- Search (desktop) — hidden for agent/admin/agency portals --}}
                @if(!session('agent_id') && !session('admin_id') && !session('agency_id'))
                <div class="hidden md:flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2 w-44 lg:w-56">
                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" placeholder="Search..." class="bg-transparent text-xs text-gray-600 placeholder-gray-400 outline-none w-full">
                </div>

                {{-- Add Property quick CTA (realtor only) --}}
                <a href="{{ route('realtor.properties.create') }}"
                   class="hidden sm:flex items-center gap-1.5 px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-sm shadow-primary-200/50">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Property
                </a>
                @endif

                {{-- Notifications — context-aware (admin vs agent vs realtor) --}}
                @if(session('admin_id'))
                @php
                    $adminPendingR = \App\Models\UsersTable::where('Status','pending')->count();
                    $adminPendingP = \App\Models\PropertiesTable::where('approval_status','pending')->count();
                    $adminTotal    = $adminPendingR + $adminPendingP;
                @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open=false">
                    <button @click="open=!open" class="relative p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($adminTotal > 0)
                        <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 bg-red-500 rounded-full badge-pulse ring-2 ring-white flex items-center justify-center">
                            <span class="text-[9px] font-bold text-white leading-none">{{ $adminTotal > 9 ? '9+' : $adminTotal }}</span>
                        </span>
                        @endif
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-72 bg-white rounded-2xl border border-gray-100 shadow-xl z-50">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900">Pending Actions</p>
                            @if($adminTotal > 0)<span class="text-xs bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded-full">{{ $adminTotal }} pending</span>
                            @else<span class="text-xs bg-green-100 text-green-600 font-semibold px-2 py-0.5 rounded-full">All clear</span>@endif
                        </div>
                        <div class="py-1.5">
                            <a href="{{ route('admin.approvals') }}" @click="open=false" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 {{ $adminPendingR > 0 ? 'bg-amber-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 {{ $adminPendingR > 0 ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="flex-1"><p class="text-xs font-semibold text-gray-900">Realtor Approvals</p><p class="text-xs text-gray-400">{{ $adminPendingR }} awaiting approval</p></div>
                                @if($adminPendingR > 0)<span class="text-xs bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full">{{ $adminPendingR }}</span>@endif
                            </a>
                            <a href="{{ route('admin.approvals') }}" @click="open=false" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 {{ $adminPendingP > 0 ? 'bg-blue-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 {{ $adminPendingP > 0 ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                </div>
                                <div class="flex-1"><p class="text-xs font-semibold text-gray-900">Property Approvals</p><p class="text-xs text-gray-400">{{ $adminPendingP }} pending review</p></div>
                                @if($adminPendingP > 0)<span class="text-xs bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full">{{ $adminPendingP }}</span>@endif
                            </a>
                        </div>
                        <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                            <a href="{{ route('admin.dashboard') }}" @click="open=false" class="text-xs text-primary-600 font-semibold hover:underline">Go to Overview →</a>
                        </div>
                    </div>
                </div>
                {{-- Admin user dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open=false">
                    <button @click="open=!open" class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(session('admin_name','A'),0,1)) }}
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-52 bg-white rounded-2xl border border-gray-100 shadow-xl z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-xs font-semibold text-gray-900">{{ session('admin_name') }}</p>
                            <p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_',' ',session('admin_role','admin'))) }}</p>
                        </div>
                        <div class="py-1.5">
                            <a href="{{ route('admin.settings') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Settings
                            </a>
                        </div>
                        <div class="border-t border-gray-100 py-1.5">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @elseif(session('agent_id'))
                {{-- ── Agent Notifications ─────────────────────────────── --}}
                @php
                    $notifAgent       = \App\Models\AgentsTable::find(session('agent_id'));
                    $notifLga         = $notifAgent?->Assigned_area;
                    $notifPendingRealtors    = $notifLga ? \App\Models\UsersTable::where('Lga', $notifLga)->where('kyc_status', 'pending')->count() : 0;
                    $notifPendingProps       = $notifLga ? \App\Models\PropertiesTable::whereHas('user', fn($q) => $q->where('Lga', $notifLga))->where('approval_status', 'pending')->count() : 0;
                    $notifPendingApts        = $notifLga ? \App\Models\PropertyInquiry::whereHas('property.user', fn($q) => $q->where('Lga', $notifLga))->where('status', 'pending')->count() : 0;
                    $notifAgentTotal  = $notifPendingRealtors + $notifPendingProps + $notifPendingApts;
                @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open=false">
                    <button @click="open=!open"
                            class="relative p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($notifAgentTotal > 0)
                        <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 bg-red-500 rounded-full badge-pulse ring-2 ring-white flex items-center justify-center">
                            <span class="text-[9px] font-bold text-white leading-none">{{ $notifAgentTotal > 9 ? '9+' : $notifAgentTotal }}</span>
                        </span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak @click.outside="open=false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl border border-gray-100 shadow-xl z-50">

                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Action Items</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $notifAgent?->Assigned_area }}, {{ $notifAgent?->State }}</p>
                            </div>
                            @if($notifAgentTotal > 0)
                                <span class="text-xs bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded-full">{{ $notifAgentTotal }} pending</span>
                            @else
                                <span class="text-xs bg-green-100 text-green-600 font-semibold px-2 py-0.5 rounded-full">All clear</span>
                            @endif
                        </div>

                        <div class="py-1.5 max-h-72 overflow-y-auto">

                            {{-- Pending Realtors --}}
                            <a href="{{ route('agent.realtors') }}" @click="open=false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 {{ $notifPendingRealtors > 0 ? 'bg-amber-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 {{ $notifPendingRealtors > 0 ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900">Realtor Approvals</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $notifPendingRealtors > 0 ? $notifPendingRealtors . ' realtor' . ($notifPendingRealtors > 1 ? 's' : '') . ' awaiting KYC review' : 'No pending realtors' }}
                                    </p>
                                </div>
                                @if($notifPendingRealtors > 0)
                                    <span class="text-xs bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full flex-shrink-0">{{ $notifPendingRealtors }}</span>
                                @endif
                            </a>

                            {{-- Pending Properties --}}
                            <a href="{{ route('agent.properties') }}" @click="open=false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 {{ $notifPendingProps > 0 ? 'bg-blue-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 {{ $notifPendingProps > 0 ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900">Property Approvals</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $notifPendingProps > 0 ? $notifPendingProps . ' propert' . ($notifPendingProps > 1 ? 'ies' : 'y') . ' pending review' : 'No pending properties' }}
                                    </p>
                                </div>
                                @if($notifPendingProps > 0)
                                    <span class="text-xs bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full flex-shrink-0">{{ $notifPendingProps }}</span>
                                @endif
                            </a>

                            {{-- Pending Appointments --}}
                            <a href="{{ route('agent.appointments') }}" @click="open=false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 {{ $notifPendingApts > 0 ? 'bg-primary-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 {{ $notifPendingApts > 0 ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900">Appointments</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $notifPendingApts > 0 ? $notifPendingApts . ' appointment' . ($notifPendingApts > 1 ? 's' : '') . ' need confirmation' : 'No pending appointments' }}
                                    </p>
                                </div>
                                @if($notifPendingApts > 0)
                                    <span class="text-xs bg-primary-100 text-primary-700 font-bold px-2 py-0.5 rounded-full flex-shrink-0">{{ $notifPendingApts }}</span>
                                @endif
                            </a>
                        </div>

                        <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                            <a href="{{ route('agent.dashboard') }}" @click="open=false"
                               class="text-xs text-primary-600 font-semibold hover:underline">Go to Overview →</a>
                        </div>
                    </div>
                </div>

                @elseif(session('agency_id'))
                {{-- ── Agency Notification Bell ───────────────────────── --}}
                @php
                    $agencyNetworkCount = \App\Models\UsersTable::where('agency_id', session('agency_id'))->count();
                    $agencyPendingDeals = \App\Models\CommissionTable::where('agency_id', session('agency_id'))->whereIn('status', ['pending','processing'])->count();
                @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open=false">
                    <button @click="open=!open" class="relative p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($agencyPendingDeals > 0)
                        <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 bg-amber-500 rounded-full badge-pulse ring-2 ring-white flex items-center justify-center">
                            <span class="text-[9px] font-bold text-white leading-none">{{ $agencyPendingDeals > 9 ? '9+' : $agencyPendingDeals }}</span>
                        </span>
                        @endif
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-72 bg-white rounded-2xl border border-gray-100 shadow-xl z-50">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900">Agency Overview</p>
                            <span class="text-xs bg-primary-100 text-primary-700 font-semibold px-2 py-0.5 rounded-full">{{ $agencyNetworkCount }} realtors</span>
                        </div>
                        <div class="py-1.5">
                            <a href="{{ route('agency.network') }}" @click="open=false" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
                                </div>
                                <div class="flex-1"><p class="text-xs font-semibold text-gray-900">My Network</p><p class="text-xs text-gray-400">{{ $agencyNetworkCount }} registered realtor{{ $agencyNetworkCount !== 1 ? 's' : '' }}</p></div>
                            </a>
                            <a href="{{ route('agency.earnings') }}" @click="open=false" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 {{ $agencyPendingDeals > 0 ? 'bg-amber-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 {{ $agencyPendingDeals > 0 ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1"><p class="text-xs font-semibold text-gray-900">Earnings</p><p class="text-xs text-gray-400">{{ $agencyPendingDeals }} deal{{ $agencyPendingDeals !== 1 ? 's' : '' }} pending settlement</p></div>
                                @if($agencyPendingDeals > 0)<span class="text-xs bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full">{{ $agencyPendingDeals }}</span>@endif
                            </a>
                        </div>
                        <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                            <a href="{{ route('agency.dashboard') }}" @click="open=false" class="text-xs text-primary-600 font-semibold hover:underline">Go to Overview →</a>
                        </div>
                    </div>
                </div>

                @elseauth
                {{-- ── Realtor Notifications ─────────────────────────── --}}
                @php
                    $notifRealtorApts     = auth()->check() ? \App\Models\PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', auth()->id()))->where('status', 'pending')->count() : 0;
                    $notifRealtorProps    = auth()->check() ? \App\Models\PropertiesTable::where('user_id', auth()->id())->where('approval_status', 'pending')->count() : 0;
                    $notifRealtorTotal    = $notifRealtorApts + $notifRealtorProps;
                @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open=false">
                    <button @click="open=!open"
                            class="relative p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($notifRealtorTotal > 0)
                        <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 bg-red-500 rounded-full badge-pulse ring-2 ring-white flex items-center justify-center">
                            <span class="text-[9px] font-bold text-white leading-none">{{ $notifRealtorTotal > 9 ? '9+' : $notifRealtorTotal }}</span>
                        </span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak @click.outside="open=false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl border border-gray-100 shadow-xl z-50">

                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900">Notifications</p>
                            @if($notifRealtorTotal > 0)
                                <span class="text-xs bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded-full">{{ $notifRealtorTotal }} new</span>
                            @else
                                <span class="text-xs bg-green-100 text-green-600 font-semibold px-2 py-0.5 rounded-full">All clear</span>
                            @endif
                        </div>

                        <div class="py-1.5 max-h-72 overflow-y-auto">

                            {{-- Pending Appointments --}}
                            <a href="{{ route('realtor.appointments') }}" @click="open=false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 {{ $notifRealtorApts > 0 ? 'bg-amber-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 {{ $notifRealtorApts > 0 ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900">Viewing Requests</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $notifRealtorApts > 0 ? $notifRealtorApts . ' new viewing request' . ($notifRealtorApts > 1 ? 's' : '') : 'No new viewing requests' }}
                                    </p>
                                </div>
                                @if($notifRealtorApts > 0)
                                    <span class="text-xs bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full flex-shrink-0">{{ $notifRealtorApts }}</span>
                                @endif
                            </a>

                            {{-- Properties Pending Approval --}}
                            <a href="{{ route('realtor.properties') }}" @click="open=false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 {{ $notifRealtorProps > 0 ? 'bg-blue-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 {{ $notifRealtorProps > 0 ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900">Listings Pending Approval</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $notifRealtorProps > 0 ? $notifRealtorProps . ' listing' . ($notifRealtorProps > 1 ? 's' : '') . ' under review' : 'All listings approved' }}
                                    </p>
                                </div>
                                @if($notifRealtorProps > 0)
                                    <span class="text-xs bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full flex-shrink-0">{{ $notifRealtorProps }}</span>
                                @endif
                            </a>

                            {{-- Welcome message when nothing pending --}}
                            @if($notifRealtorTotal === 0)
                            <div class="flex items-center gap-3 px-4 py-3">
                                <div class="w-9 h-9 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-gray-900">You're all caught up!</p>
                                    <p class="text-xs text-gray-400">No pending actions at the moment.</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                            <a href="{{ route('realtor.dashboard') }}" @click="open=false"
                               class="text-xs text-primary-600 font-semibold hover:underline">Go to Dashboard →</a>
                        </div>
                    </div>
                </div>
                @endauth

                {{-- User dropdown — agent-aware --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open=false">

                    @if(session('agent_id'))
                    {{-- Agent avatar button --}}
                    @php $headerAgent = \App\Models\AgentsTable::find(session('agent_id')); @endphp
                    <button @click="open=!open"
                            class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr($headerAgent?->First_name ?? 'A', 0, 1)) }}{{ strtoupper(substr($headerAgent?->Last_name ?? '', 0, 1)) }}
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Agent dropdown panel --}}
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-2xl border border-gray-100 shadow-xl z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-xs font-semibold text-gray-900 truncate">{{ $headerAgent?->First_name }} {{ $headerAgent?->Last_name }}</p>
                            <p class="text-[10px] text-gray-400 truncate">{{ $headerAgent?->Assigned_area }}, {{ $headerAgent?->State }}</p>
                        </div>
                        <div class="py-1.5">
                            <a href="{{ route('agent.settings') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                My Profile
                            </a>
                            <a href="{{ route('agent.settings') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Settings
                            </a>
                        </div>
                        <div class="border-t border-gray-100 py-1.5">
                            <form method="POST" action="{{ route('agent.logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>

                    @elseif(session('agency_id'))
                    {{-- Agency avatar button --}}
                    @php $headerAgency = \App\Models\AgencyPartner::find(session('agency_id')); @endphp
                    <button @click="open=!open"
                            class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white text-xs font-bold">
                            @php
                                $hAgWords = explode(' ', $headerAgency?->company_name ?? 'A');
                                echo strtoupper(substr($hAgWords[0] ?? 'A', 0, 1)) . strtoupper(substr($hAgWords[1] ?? '', 0, 1));
                            @endphp
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Agency dropdown panel --}}
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-2xl border border-gray-100 shadow-xl z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-xs font-semibold text-gray-900 truncate">{{ $headerAgency?->company_name }}</p>
                            <p class="text-[10px] text-primary-600 font-medium">Agency Partner</p>
                        </div>
                        <div class="py-1.5">
                            <a href="{{ route('agency.settings') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Settings
                            </a>
                        </div>
                        <div class="border-t border-gray-100 py-1.5">
                            <form method="POST" action="{{ route('agency.logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>

                    @elseauth
                    {{-- Realtor avatar button --}}
                    <button @click="open=!open"
                            class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->First_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->Last_name ?? '', 0, 1)) }}
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Realtor dropdown panel --}}
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-52 bg-white rounded-2xl border border-gray-100 shadow-xl z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-xs font-semibold text-gray-900 truncate">{{ auth()->user()->First_name }} {{ auth()->user()->Last_name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->Email }}</p>
                        </div>
                        <div class="py-1.5">
                            <a href="{{ route('realtor.settings') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                My Profile
                            </a>
                            <a href="{{ route('realtor.settings') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Settings
                            </a>
                        </div>
                        <div class="border-t border-gray-100 py-1.5">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth

                </div>

            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success') || session('error'))
            <div class="px-4 sm:px-6 pt-4">
                @if(session('success'))
                    <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
