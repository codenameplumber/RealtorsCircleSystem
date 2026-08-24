@extends('layouts.dashboard')
@section('title','Leads')
@section('role_label','Realtor')
@section('page_title','Leads & Inquiries')
@section('page_subtitle','Property viewing requests from potential clients')
@section('sidebar_nav') @include('dashboard.realtor._sidebar') @endsection

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    {{-- Summary strip --}}
    <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span class="w-2.5 h-2.5 bg-amber-400 rounded-full inline-block"></span>
            Pending: <strong class="text-gray-800 ml-1">{{ $leads->getCollection()->where('status','pending')->count() }}</strong>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span class="w-2.5 h-2.5 bg-green-500 rounded-full inline-block"></span>
            Confirmed: <strong class="text-gray-800 ml-1">{{ $leads->getCollection()->where('status','confirmed')->count() }}</strong>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span class="w-2.5 h-2.5 bg-gray-400 rounded-full inline-block"></span>
            Completed: <strong class="text-gray-800 ml-1">{{ $leads->getCollection()->where('status','completed')->count() }}</strong>
        </div>
        <div class="ml-auto text-xs text-gray-400">
            Showing {{ $leads->firstItem() }}–{{ $leads->lastItem() }} of {{ $leads->total() }} leads
        </div>
    </div>

    @if($leads->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Property</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date Requested</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($leads as $lead)
                    <tr class="hover:bg-gray-50 transition-colors">
                        {{-- Name only — contact details are protected --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-primary-700">
                                        {{ strtoupper(substr($lead->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $lead->name ?? 'Unknown' }}</p>
                                    @if($lead->booker_user_id)
                                        <p class="text-xs text-primary-600 font-medium">Realtor Network</p>
                                    @else
                                        <p class="text-xs text-gray-400">Public Inquiry</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-gray-800 font-medium text-xs max-w-[180px] truncate">{{ $lead->property?->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $lead->property?->city }}</p>
                        </td>
                        <td class="px-5 py-4 text-gray-600 text-xs whitespace-nowrap">
                            {{ $lead->preferred_date?->format('M j, Y') }}
                            <p class="text-gray-400 mt-0.5">{{ $lead->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-5 py-4 text-gray-600 text-xs whitespace-nowrap">{{ $lead->preferred_time }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-full capitalize">
                                {{ str_replace('_',' ',$lead->visit_type) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $colors = [
                                    'confirmed' => 'bg-green-100 text-green-700',
                                    'completed' => 'bg-gray-100 text-gray-600',
                                    'cancelled' => 'bg-red-100 text-red-600',
                                    'pending'   => 'bg-amber-100 text-amber-700',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($leads->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between gap-4 flex-wrap">
                <p class="text-xs text-gray-400">
                    Page {{ $leads->currentPage() }} of {{ $leads->lastPage() }}
                </p>
                <div class="flex items-center gap-1">
                    {{-- Previous --}}
                    @if($leads->onFirstPage())
                        <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">← Prev</span>
                    @else
                        <a href="{{ $leads->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-primary-400 hover:text-primary-600 transition-colors">← Prev</a>
                    @endif

                    {{-- Page numbers --}}
                    @foreach($leads->getUrlRange(max(1, $leads->currentPage()-2), min($leads->lastPage(), $leads->currentPage()+2)) as $page => $url)
                        @if($page == $leads->currentPage())
                            <span class="px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 rounded-lg">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-primary-400 hover:text-primary-600 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if($leads->hasMorePages())
                        <a href="{{ $leads->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:border-primary-400 hover:text-primary-600 transition-colors">Next →</a>
                    @else
                        <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">Next →</span>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-16 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-sm">No leads yet. When clients request viewings on your properties, they'll appear here.</p>
        </div>
    @endif
</div>
@endsection
