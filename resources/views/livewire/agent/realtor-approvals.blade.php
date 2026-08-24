<div>
    {{-- Toast --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:agent-notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 4000)"
         x-show="show" x-cloak x-transition
         :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed top-5 right-5 z-50 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg max-w-sm">
        <span x-text="message"></span>
    </div>

    {{-- Header: search + tabs --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-gray-50">
            <div class="flex gap-1 bg-gray-100 rounded-xl p-1">
                @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
                    <button wire:click="$set('tab', '{{ $key }}')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $tab === $key ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2 w-full sm:w-56">
                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search realtors..." class="bg-transparent text-xs text-gray-600 placeholder-gray-400 outline-none w-full">
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Realtor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Contact</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Specialization</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">KYC Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($realtors as $realtor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 text-sm font-bold flex-shrink-0">
                                        {{ strtoupper(substr($realtor->First_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $realtor->First_name }} {{ $realtor->Last_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $realtor->Email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-600 hidden md:table-cell">
                                <p class="text-sm">{{ $realtor->Phone }}</p>
                                <p class="text-xs text-gray-400">{{ $realtor->Lga }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-600 text-sm hidden lg:table-cell">{{ $realtor->Specialization }}</td>
                            <td class="px-5 py-4">
                                @php
                                    $ks = $realtor->kyc_status;
                                    $kc = match($ks) { 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', default => 'bg-amber-100 text-amber-700' };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $kc }}">{{ ucfirst($ks ?? 'pending') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <button wire:click="viewRealtor({{ $realtor->id }})"
                                        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                                    View Profile
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-16 text-gray-400 text-sm">No realtors found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($realtors->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $realtors->links() }}</div>
        @endif
    </div>

    {{-- ── Realtor Detail Modal ─────────────────────────────────────────────── --}}
    @if($detailOpen && $viewingRealtor)
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                {{-- Modal header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-900">Realtor Profile</h2>
                    <button wire:click="closeDetail" class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Identity --}}
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-700 text-2xl font-bold flex-shrink-0">
                            {{ strtoupper(substr($viewingRealtor->First_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $viewingRealtor->First_name }} {{ $viewingRealtor->Last_name }}</p>
                            <p class="text-sm text-gray-500">{{ $viewingRealtor->Specialization }} · {{ $viewingRealtor->Yrs_experience }} yrs exp.</p>
                            @php $ks = $viewingRealtor->kyc_status; $kc = match($ks){ 'approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700',default=>'bg-amber-100 text-amber-700' }; @endphp
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold {{ $kc }}">{{ ucfirst($ks ?? 'pending') }}</span>
                        </div>
                    </div>

                    {{-- Contact Info --}}
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

                    {{-- Bio --}}
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
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1">Front of ID</p>
                                        <img src="{{ $viewingRealtor->kyc_front_link }}" alt="ID Front" class="w-full h-32 object-cover rounded-xl border border-gray-100">
                                    </div>
                                @endif
                                @if($viewingRealtor->kyc_back_url)
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1">Back of ID</p>
                                        <img src="{{ $viewingRealtor->kyc_back_link }}" alt="ID Back" class="w-full h-32 object-cover rounded-xl border border-gray-100">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Bank Info --}}
                    @if($viewingRealtor->bankInformation)
                        <div class="bg-blue-50 rounded-xl p-4">
                            <p class="text-[10px] font-semibold text-blue-400 uppercase tracking-wide mb-2">Bank Information</p>
                            <div class="grid grid-cols-3 gap-2 text-sm">
                                <div>
                                    <p class="text-[10px] text-gray-400">Bank</p>
                                    <p class="font-semibold text-gray-900">{{ $viewingRealtor->bankInformation->Bank_Name }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400">Account No.</p>
                                    <p class="font-semibold text-gray-900">{{ $viewingRealtor->bankInformation->Bank_Account }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400">Account Name</p>
                                    <p class="font-semibold text-gray-900">{{ $viewingRealtor->bankInformation->Account_Name }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    @if($viewingRealtor->kyc_status !== 'approved')
                        <div class="flex gap-3 pt-2">
                            <button wire:click="approve({{ $viewingRealtor->id }})" wire:loading.attr="disabled"
                                    class="flex-1 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-colors text-sm">
                                <span wire:loading.remove wire:target="approve({{ $viewingRealtor->id }})">Approve Realtor</span>
                                <span wire:loading wire:target="approve({{ $viewingRealtor->id }})">Approving...</span>
                            </button>
                            <button wire:click="openReject({{ $viewingRealtor->id }})"
                                    class="flex-1 py-3 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-xl transition-colors text-sm">
                                Reject
                            </button>
                        </div>
                    @else
                        <div class="flex gap-3 pt-2">
                            <button wire:click="openReject({{ $viewingRealtor->id }})"
                                    class="flex-1 py-3 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-xl transition-colors text-sm">
                                Revoke Approval
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── Reject Reason Modal ──────────────────────────────────────────────── --}}
    @if($rejectOpen)
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Reason for Rejection</h3>
                    <p class="text-xs text-gray-400 mt-1">This message will be emailed to the realtor.</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <textarea wire:model="rejectReason" rows="4" placeholder="e.g. KYC documents are unclear or missing. Please resubmit clear photos of your valid ID..."
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
                        @error('rejectReason') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-3">
                        <button wire:click="$set('rejectOpen', false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
                        <button wire:click="submitReject" wire:loading.attr="disabled"
                                class="flex-1 py-2.5 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">
                            <span wire:loading.remove wire:target="submitReject">Send Rejection</span>
                            <span wire:loading wire:target="submitReject">Sending...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
