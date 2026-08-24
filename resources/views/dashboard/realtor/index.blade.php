@extends('layouts.dashboard')

@section('title', 'My Dashboard')
@section('role_label', 'Realtor')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Welcome back, ' . auth()->user()->First_name . '!')

@section('sidebar_nav')
    @include('dashboard.realtor._sidebar')
@endsection

@section('content')
    @php $user = auth()->user(); @endphp

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label'=>'Total Commission',  'value'=>'₦'.number_format($totalCommission), 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color'=>'emerald'],
            ['label'=>'Active Listings',   'value'=>$activeListings,                      'icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color'=>'blue'],
            ['label'=>'New Leads',          'value'=>$newLeads,                            'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color'=>'purple'],
            ['label'=>'Upcoming Viewings', 'value'=>$upcomingViewings,                    'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color'=>'amber'],
        ] as $stat)
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-gray-500">{{ $stat['label'] }}</p>
                <div class="w-9 h-9 bg-{{ $stat['color'] }}-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Revenue Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-gray-900">Commission Overview</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Monthly earnings this year</p>
                </div>
                <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-lg">2026</span>
            </div>
            <canvas id="revenueChart" height="120"></canvas>
        </div>

        {{-- Recent Leads --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-900">Recent Leads</h3>
                <a href="{{ route('realtor.leads') }}" class="text-xs text-primary-600 font-medium hover:underline">View all</a>
            </div>
            @forelse($recentLeads as $lead)
                <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($lead->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $lead->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $lead->preferred_date?->format('M j') }} · {{ $lead->visit_type === 'virtual' ? 'Virtual' : 'In-person' }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $lead->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ ucfirst($lead->status) }}
                    </span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-sm">No leads yet</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- My Properties quick view --}}
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-900">My Active Listings</h3>
            <a href="{{ route('realtor.properties') }}" class="text-xs text-primary-600 font-medium hover:underline">Manage all</a>
        </div>
        @forelse($myProperties as $property)
            <div class="flex items-center gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                <div class="w-14 h-12 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $property->title }}</p>
                    <p class="text-xs text-gray-400">{{ $property->city }} · {{ $property->type }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-900">₦{{ $property->price_display }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $property->approval_status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ ucfirst($property->approval_status) }}
                    </span>
                </div>
                <a href="{{ route('realtor.properties.show', \App\Helpers\IdHasher::encode($property->id)) }}" class="ml-2 p-1.5 text-gray-400 hover:text-primary-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @empty
            <div class="text-center py-8 text-gray-400">
                <p class="text-sm mb-3">You haven't listed any properties yet.</p>
                <a href="{{ route('realtor.properties') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-600 text-white text-xs font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add First Property
                </a>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('revenueChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Commission (₦)',
                data: {!! json_encode($monthlyCommission) !!},
                backgroundColor: 'rgba(5,150,105,0.2)',
                borderColor: '#059669',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { color: '#9ca3af', font: { size: 11 } } },
                x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 } } }
            }
        }
    });
}
</script>
@endpush
