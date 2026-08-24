<div class="space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Approvals</h1>
        <p class="text-sm text-gray-500 mt-0.5">Review and approve pending realtors and properties</p>
    </div>

    {{-- ============================== SECTION 0: Pending Agency Partners ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
            <h2 class="text-base font-semibold text-gray-900">Pending Agency Partners</h2>
            @if(count($pendingAgencies) > 0)
            <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                {{ count($pendingAgencies) }}
            </span>
            @endif
        </div>

        @if(count($pendingAgencies) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Agency</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Contact</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Applied</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pendingAgencies as $agency)
                    <tr wire:key="ag-{{ $agency->id }}" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-emerald-700">{{ strtoupper(substr($agency->company_name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $agency->company_name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[160px]">{{ $agency->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600 text-xs">
                            {{ $agency->first_name }} {{ $agency->last_name }}
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $agency->phone ?? '—' }}</td>
                        <td class="px-5 py-4 text-xs text-gray-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($agency->created_at)->diffForHumans() }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <button wire:click="approveAgency({{ $agency->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="approveAgency({{ $agency->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 px-3.5 py-1.5 rounded-lg transition-colors">
                                    <span wire:loading.remove wire:target="approveAgency({{ $agency->id }})">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <span wire:loading wire:target="approveAgency({{ $agency->id }})">
                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </span>
                                    Approve
                                </button>
                                <button wire:click="openReject({{ $agency->id }}, 'agency')"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 disabled:opacity-60 px-3.5 py-1.5 rounded-lg transition-colors border border-red-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16 gap-3">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-400 font-medium text-sm">No pending agency applications</p>
            <p class="text-gray-300 text-xs">All agency partners have been reviewed</p>
        </div>
        @endif
    </div>

    {{-- ============================== SECTION 1: Pending Realtors ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
            <h2 class="text-base font-semibold text-gray-900">Pending Realtors</h2>
            @if(count($pendingRealtors) > 0)
            <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                {{ count($pendingRealtors) }}
            </span>
            @endif
        </div>

        @if(count($pendingRealtors) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Realtor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Specialization</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Experience</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Applied</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pendingRealtors as $realtor)
                    <tr wire:key="pr-{{ $realtor->id }}" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-amber-700">{{ strtoupper(substr($realtor->First_name, 0, 1)) }}{{ strtoupper(substr($realtor->Last_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $realtor->First_name }} {{ $realtor->Last_name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[160px]">{{ $realtor->Email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $realtor->Phone ?? '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $realtor->Specialization ?? '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">
                            {{ $realtor->Years_of_experience ?? '—' }}
                            @if($realtor->Years_of_experience)
                                <span class="text-xs text-gray-400">yrs</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($realtor->created_at)->diffForHumans() }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                <button wire:click="viewRealtor({{ $realtor->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </button>
                                <button wire:click="approveRealtor({{ $realtor->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="approveRealtor({{ $realtor->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 px-3.5 py-1.5 rounded-lg transition-colors">
                                    <span wire:loading.remove wire:target="approveRealtor({{ $realtor->id }})">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <span wire:loading wire:target="approveRealtor({{ $realtor->id }})">
                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </span>
                                    Approve
                                </button>
                                <button wire:click="openReject({{ $realtor->id }}, 'realtor')"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 disabled:opacity-60 px-3.5 py-1.5 rounded-lg transition-colors border border-red-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16 gap-3">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-400 font-medium text-sm">No pending realtor applications</p>
            <p class="text-gray-300 text-xs">All realtors have been reviewed</p>
        </div>
        @endif
    </div>

    {{-- ============================== SECTION 2: Pending Properties ============================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
            <h2 class="text-base font-semibold text-gray-900">Pending Properties</h2>
            @if(count($pendingProperties) > 0)
            <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                {{ count($pendingProperties) }}
            </span>
            @endif
        </div>

        @if(count($pendingProperties) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Property</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Location</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Realtor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Price</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Submitted</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pendingProperties as $property)
                    <tr wire:key="pp-{{ $property->id }}" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4.5 h-4.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                                <p class="font-semibold text-gray-800 line-clamp-1 max-w-[160px]">{{ $property->Title ?? $property->Property_name ?? 'Untitled' }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-gray-700 text-xs font-medium">{{ $property->City ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $property->State ?? '' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-violet-50 text-violet-700">
                                {{ $property->Property_type ?? $property->type ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($property->realtor ?? $property->listingRealtor ?? null)
                                @php $r = $property->realtor ?? $property->listingRealtor; @endphp
                                <p class="font-medium text-gray-700 text-xs">{{ $r->First_name ?? '' }} {{ $r->Last_name ?? '' }}</p>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-semibold text-gray-800 whitespace-nowrap">
                            ₦{{ number_format($property->Price ?? $property->price ?? 0, 2) }}
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($property->created_at)->diffForHumans() }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="/properties/preview/{{ \App\Helpers\IdHasher::encode($property->id) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Preview
                                </a>
                                <button wire:click="approveProperty({{ $property->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="approveProperty({{ $property->id }})"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 px-3 py-1.5 rounded-lg transition-colors">
                                    <span wire:loading.remove wire:target="approveProperty({{ $property->id }})">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <svg wire:loading wire:target="approveProperty({{ $property->id }})" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Approve
                                </button>
                                <button wire:click="openReject({{ $property->id }}, 'property')"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 disabled:opacity-60 px-3 py-1.5 rounded-lg transition-colors border border-red-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16 gap-3">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <p class="text-gray-400 font-medium text-sm">No pending property listings</p>
            <p class="text-gray-300 text-xs">All properties have been reviewed</p>
        </div>
        @endif
    </div>

    {{-- ============================== Reject Reason Modal ============================== --}}
    @if($rejectOpen)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4" wire:click.self="closeReject">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-1">
                Reject {{ ucfirst($rejectType) }}
            </h3>
            <p class="text-sm text-gray-500 mb-4">Please provide a reason. This will be emailed to the applicant.</p>

            <textarea wire:model="rejectReason"
                      rows="4"
                      placeholder="Explain why this is being rejected..."
                      class="w-full text-sm border border-gray-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
            @error('rejectReason')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3 mt-5">
                <button wire:click="closeReject" type="button"
                        class="text-sm font-medium text-gray-600 hover:text-gray-800 px-4 py-2.5 rounded-xl transition-colors">
                    Cancel
                </button>
                <button wire:click="submitReject"
                        wire:loading.attr="disabled"
                        wire:target="submitReject"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 disabled:opacity-60 px-4 py-2.5 rounded-xl transition-colors">
                    <svg wire:loading wire:target="submitReject" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Confirm Rejection
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================== Realtor Detail Modal (KYC review) ============================== --}}
    @if($detailOpen && $viewingRealtor)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="closeDetail">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Realtor Application</h2>
                <button wire:click="closeDetail" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-700 text-2xl font-bold flex-shrink-0">
                        {{ strtoupper(substr($viewingRealtor->First_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ $viewingRealtor->First_name }} {{ $viewingRealtor->Last_name }}</p>
                        <p class="text-sm text-gray-500">{{ $viewingRealtor->Specialization ?? 'Real Estate Professional' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['label' => 'Email', 'value' => $viewingRealtor->Email],
                        ['label' => 'Phone', 'value' => $viewingRealtor->Phone],
                        ['label' => 'LGA', 'value' => $viewingRealtor->Lga],
                        ['label' => 'State', 'value' => $viewingRealtor->State],
                        ['label' => 'Address', 'value' => $viewingRealtor->Address],
                        ['label' => 'ID Type', 'value' => $viewingRealtor->kyc_id_type],
                    ] as $field)
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">{{ $field['label'] }}</p>
                            <p class="text-sm text-gray-900 font-medium mt-0.5">{{ $field['value'] ?? '—' }}</p>
                        </div>
                    @endforeach
                </div>

                @if($viewingRealtor->Bio)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Bio</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $viewingRealtor->Bio }}</p>
                    </div>
                @endif

                {{-- KYC Images --}}
                @if($viewingRealtor->kyc_front_url || $viewingRealtor->kyc_back_url)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">KYC Documents</p>
                        <div class="grid grid-cols-2 gap-3">
                            @if($viewingRealtor->kyc_front_url)
                                <a href="{{ $viewingRealtor->kyc_front_link }}" target="_blank">
                                    <p class="text-xs text-gray-400 mb-1">Front of ID</p>
                                    <img src="{{ $viewingRealtor->kyc_front_link }}" alt="ID Front" class="w-full h-40 object-cover rounded-xl border border-gray-100 hover:opacity-90 transition-opacity">
                                </a>
                            @endif
                            @if($viewingRealtor->kyc_back_url)
                                <a href="{{ $viewingRealtor->kyc_back_link }}" target="_blank">
                                    <p class="text-xs text-gray-400 mb-1">Back of ID</p>
                                    <img src="{{ $viewingRealtor->kyc_back_link }}" alt="ID Back" class="w-full h-40 object-cover rounded-xl border border-gray-100 hover:opacity-90 transition-opacity">
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 text-sm text-amber-700">
                        No KYC documents have been uploaded yet.
                    </div>
                @endif

                {{-- Bank Info --}}
                @if($viewingRealtor->bankInformation)
                    <div class="bg-blue-50 rounded-xl p-4">
                        <p class="text-[10px] font-semibold text-blue-400 uppercase tracking-wide mb-2">Bank Information</p>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div>
                                <p class="text-[10px] text-gray-400">Bank</p>
                                <p class="font-semibold text-gray-900">{{ $viewingRealtor->bankInformation->Bank_Name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400">Account Name</p>
                                <p class="font-semibold text-gray-900">{{ $viewingRealtor->bankInformation->Account_Name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400">Account Number</p>
                                <p class="font-semibold text-gray-900">{{ $viewingRealtor->bankInformation->Bank_Account ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button wire:click="openReject({{ $viewingRealtor->id }}, 'realtor')"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2.5 rounded-xl transition-colors border border-red-100">
                        Reject
                    </button>
                    <button wire:click="approveRealtor({{ $viewingRealtor->id }})"
                            wire:loading.attr="disabled"
                            wire:target="approveRealtor({{ $viewingRealtor->id }})"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 px-4 py-2.5 rounded-xl transition-colors">
                        Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
