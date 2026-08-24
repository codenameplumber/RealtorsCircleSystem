<div class="space-y-5">
    {{-- Totals --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="stat-card text-center">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Override</p>
            <p class="text-xl font-black text-gray-900">₦{{ number_format($totals['total'], 2) }}</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Paid Out</p>
            <p class="text-xl font-black text-emerald-600">₦{{ number_format($totals['paid'], 2) }}</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Pending</p>
            <p class="text-xl font-black text-amber-600">₦{{ number_format($totals['pending'], 2) }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'paid' => 'Paid'] as $key => $label)
            <button wire:click="$set('statusFilter', '{{ $key }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $statusFilter === $key ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Ledger --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Deal Ref</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Property</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Realtor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Deal Total</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Your Override</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($commissions as $c)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-4 font-mono text-xs text-gray-600">{{ $c->deal_reference }}</td>
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $c->property?->title ?? '—' }}</td>
                            <td class="px-5 py-4 text-gray-700">{{ $c->listingRealtor?->First_name }} {{ $c->listingRealtor?->Last_name }}</td>
                            <td class="px-5 py-4 text-gray-700">₦{{ number_format($c->total_amount, 2) }}</td>
                            <td class="px-5 py-4 font-bold text-primary-600">₦{{ number_format($c->agency_override_amount, 2) }}</td>
                            <td class="px-5 py-4">
                                @php $badge = match($c->status) { 'paid' => 'bg-emerald-50 text-emerald-700', 'processing' => 'bg-blue-50 text-blue-700', default => 'bg-amber-50 text-amber-700' }; @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ ucfirst($c->status) }}</span>
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-xs">{{ $c->deal_date?->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No override earnings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($commissions->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $commissions->links() }}</div>
        @endif
    </div>
</div>
