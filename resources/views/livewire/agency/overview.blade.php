<div class="space-y-6">
    {{-- Toast --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:agency-notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 4000)"
         x-show="show" x-cloak x-transition
         :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed top-5 right-5 z-50 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg max-w-sm">
        <span x-text="message"></span>
    </div>

    {{-- Referral Link Card --}}
    <div x-data="{ copied: false }" class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-5 text-white shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-white/70 uppercase tracking-wider mb-1">Your Agency Referral Link</p>
                <p class="text-sm font-mono text-white/90 break-all">{{ url('/register?ref=' . $agency->referral_code) }}</p>
                <p class="text-xs text-white/60 mt-1">Share this link — realtors who register via it are automatically linked to {{ $agency->company_name }}.</p>
            </div>
            <button @click="navigator.clipboard.writeText('{{ url('/register?ref=' . $agency->referral_code) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="flex-shrink-0 px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold rounded-xl transition-colors">
                <span x-show="!copied">Copy Link</span>
                <span x-show="copied" x-cloak>Copied!</span>
            </button>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="stat-card flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Network</p>
                <p class="text-2xl font-black text-gray-900 mt-0.5">{{ number_format($stats['network_size']) }}</p>
                <p class="text-xs text-gray-400">{{ $stats['active_realtors'] }} active</p>
            </div>
        </div>
        <div class="stat-card flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Deals Closed</p>
                <p class="text-2xl font-black text-gray-900 mt-0.5">{{ number_format($stats['deals_closed']) }}</p>
                <p class="text-xs text-gray-400">by network</p>
            </div>
        </div>
        <div class="stat-card flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Earned</p>
                <p class="text-2xl font-black text-gray-900 mt-0.5">₦{{ number_format($stats['total_earned'], 2) }}</p>
                <p class="text-xs text-gray-400">all time</p>
            </div>
        </div>
        <div class="stat-card flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
                <p class="text-2xl font-black text-gray-900 mt-0.5">₦{{ number_format($stats['pending_earned'], 2) }}</p>
                <p class="text-xs text-gray-400">awaiting payout</p>
            </div>
        </div>
    </div>

    {{-- Recent Deals --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-base font-semibold text-gray-900">Recent Network Deals</h2>
            <p class="text-sm text-gray-400">Latest closed deals from your realtors that generated an override for you</p>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentDeals as $deal)
                <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $deal->property?->title ?? 'Property' }}</p>
                        <p class="text-xs text-gray-400">by {{ $deal->listingRealtor?->First_name }} {{ $deal->listingRealtor?->Last_name }} · {{ $deal->deal_reference }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-primary-600">₦{{ number_format($deal->agency_override_amount, 2) }}</p>
                        <p class="text-xs text-gray-400">{{ $deal->deal_date?->format('M j, Y') }}</p>
                    </div>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-gray-400 text-sm">No deals yet. Share your referral link to grow your network.</div>
            @endforelse
        </div>
    </div>
</div>
