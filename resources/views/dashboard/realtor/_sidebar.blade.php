@php
    $path           = request()->path();
    $isProperties   = $path === 'realtor/properties';
    $isPropertyAdd  = str_contains($path, 'realtor/properties/add') || str_contains($path, 'realtor/properties/edit');
    $isDashboard    = $path === 'realtor/dashboard';
    $isLeads        = $path === 'realtor/leads';
    $isAppointments = $path === 'realtor/appointments';
    $isCommission   = $path === 'realtor/commission';
    $isPayments     = $path === 'realtor/payments';
    $isSettings     = str_contains($path, 'realtor/settings');

    // Always compute live counts for sidebar badges
    $userId = auth()->id();
    $sidebarNewLeads = \App\Models\PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', $userId))
        ->where('status', 'pending')->count();
    $sidebarUpcomingViewings = \App\Models\PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', $userId))
        ->where('preferred_date', '>=', now()->toDateString())
        ->where('status', 'confirmed')->count();
@endphp

{{-- ===== MAIN ===== --}}
<p class="px-3 mb-1.5 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Main</p>

<a href="{{ route('realtor.dashboard') }}"
   class="nav-link {{ $isDashboard ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
              d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10-2a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>
    </svg>
    <span class="flex-1">Overview</span>
</a>

{{-- ===== LISTINGS ===== --}}
<div class="mt-4 mb-1.5">
    <div class="flex items-center gap-2 px-3">
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Listings</p>
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>
</div>

<a href="{{ route('realtor.properties') }}"
   class="nav-link {{ $isProperties ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
    </svg>
    <span class="flex-1">My Properties</span>
</a>

<a href="{{ route('realtor.properties.create') }}"
   class="nav-link {{ $isPropertyAdd ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
              d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <span class="flex-1">Add Property</span>
    @if(!$isPropertyAdd)
        <span class="text-[10px] font-semibold bg-primary-100 text-primary-600 px-1.5 py-0.5 rounded-md">New</span>
    @endif
</a>

{{-- ===== BUSINESS ===== --}}
<div class="mt-4 mb-1.5">
    <div class="flex items-center gap-2 px-3">
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Business</p>
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>
</div>

<a href="{{ route('realtor.leads') }}"
   class="nav-link {{ $isLeads ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    <span class="flex-1">Leads</span>
    @if($sidebarNewLeads > 0)
        <span class="text-[10px] font-bold bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full min-w-[18px] text-center badge-pulse">
            {{ $sidebarNewLeads > 99 ? '99+' : $sidebarNewLeads }}
        </span>
    @endif
</a>

<a href="{{ route('realtor.appointments') }}"
   class="nav-link {{ $isAppointments ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <span class="flex-1">Appointments</span>
    @if($sidebarUpcomingViewings > 0)
        <span class="text-[10px] font-bold bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
            {{ $sidebarUpcomingViewings > 99 ? '99+' : $sidebarUpcomingViewings }}
        </span>
    @endif
</a>

{{-- ===== FINANCE ===== --}}
<div class="mt-4 mb-1.5">
    <div class="flex items-center gap-2 px-3">
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Finance</p>
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>
</div>

<a href="{{ route('realtor.commission') }}"
   class="nav-link {{ $isCommission ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span class="flex-1">Commission</span>
</a>

<a href="{{ route('realtor.payments') }}"
   class="nav-link {{ $isPayments ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
              d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
    </svg>
    <span class="flex-1">Payments</span>
</a>

{{-- ===== SETTINGS ===== --}}
<div class="mt-4 mb-1.5">
    <div class="flex items-center gap-2 px-3">
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>
</div>

<a href="{{ route('realtor.settings') }}"
   class="nav-link {{ $isSettings ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    <span class="flex-1">Settings</span>
</a>
