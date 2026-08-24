<div>
    {{-- Search --}}
    <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-xl px-4 py-2.5 shadow-sm mb-4 max-w-xs">
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search clients..." class="bg-transparent text-xs text-gray-600 placeholder-gray-400 outline-none w-full">
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Client</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Email</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Phone</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Inquiries</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Last Inquiry</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 text-sm font-bold flex-shrink-0">
                                        {{ strtoupper(substr($client->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $client->name ?: '—' }}</p>
                                        <p class="text-xs text-gray-400 sm:hidden">{{ $client->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-600 hidden sm:table-cell text-xs">{{ $client->email ?: '—' }}</td>
                            <td class="px-5 py-4 text-gray-600 hidden md:table-cell text-sm">{{ $client->phone ?: '—' }}</td>
                            <td class="px-5 py-4 hidden lg:table-cell">
                                <span class="bg-primary-100 text-primary-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $client->total_inquiries }}</span>
                            </td>
                            <td class="px-5 py-4 text-gray-400 text-xs hidden lg:table-cell">
                                {{ $client->last_inquiry ? \Carbon\Carbon::parse($client->last_inquiry)->diffForHumans() : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-16 text-gray-400 text-sm">No clients found in your area.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $clients->links() }}</div>
        @endif
    </div>
</div>
