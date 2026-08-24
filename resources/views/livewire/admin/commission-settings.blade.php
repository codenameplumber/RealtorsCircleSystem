<div class="space-y-6">

    @if (session('commission_settings_success'))
        <div class="px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
            {{ session('commission_settings_success') }}
        </div>
    @endif

    <p class="text-sm text-gray-500">
        These percentages drive the automatic split calculation when an agent logs a closed deal.
        Each model's percentages must add up to exactly 100%.
    </p>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Model 1 --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-1">Model 1 — Standard Deal</h3>
            <p class="text-xs text-gray-400 mb-5">Listing realtor + referring realtor + agent + platform</p>

            <div class="space-y-4">
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Listing Realtor (%)</label>
                    <input type="number" wire:model.live="m1_listing_pct" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Referring Realtor (%)</label>
                    <input type="number" wire:model.live="m1_referring_pct" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Agent (%)</label>
                    <input type="number" wire:model.live="m1_agent_pct" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Platform (%)</label>
                    <input type="number" wire:model.live="m1_platform_pct" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <p class="text-sm font-semibold {{ $this->m1Total() === 100 ? 'text-green-600' : 'text-red-500' }}">
                    Total: {{ $this->m1Total() }}%
                    @if ($this->m1Total() !== 100)
                        — must equal 100%
                    @endif
                </p>
                @error('m1_listing_pct') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Model 2 --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-1">Model 2 — Direct Deal</h3>
            <p class="text-xs text-gray-400 mb-5">Listing realtor + agent + platform (no referring realtor)</p>

            <div class="space-y-4">
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Listing Realtor (%)</label>
                    <input type="number" wire:model.live="m2_listing_pct" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Agent (%)</label>
                    <input type="number" wire:model.live="m2_agent_pct" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Platform (%)</label>
                    <input type="number" wire:model.live="m2_platform_pct" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <p class="text-sm font-semibold {{ $this->m2Total() === 100 ? 'text-green-600' : 'text-red-500' }}">
                    Total: {{ $this->m2Total() }}%
                    @if ($this->m2Total() !== 100)
                        — must equal 100%
                    @endif
                </p>
                @error('m2_listing_pct') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Agency Override --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-1">Agency Partner Override</h3>
        <p class="text-xs text-gray-400 mb-5">Percentage of the platform fee routed to an agency when the listing realtor belongs to one. Comes out of the platform's share — does not affect realtor or agent splits.</p>

        <div class="max-w-xs space-y-4">
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Override (% of Platform Fee)</label>
                <input type="number" wire:model.live="agency_override_pct" min="0" max="100"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <p class="text-xs text-gray-400 mt-1.5">Default: 20% — meaning if platform fee is ₦10,000, agency gets ₦2,000 and platform retains ₦8,000.</p>
            </div>
            @error('agency_override_pct') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <button wire:click="save" type="button"
            class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors inline-flex items-center gap-2">
        <span wire:loading.remove wire:target="save">Save Commission Settings</span>
        <span wire:loading wire:target="save">Saving...</span>
    </button>
</div>
