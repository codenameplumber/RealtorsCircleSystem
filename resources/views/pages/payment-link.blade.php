<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment — Realtors Circle</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7',
                            400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857',
                            800: '#065f46', 900: '#064e3b',
                        }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        <div class="text-center mb-6">
            <p class="font-black text-xl text-gray-900">Realtors Circle</p>
            <p class="text-xs text-gray-400 mt-1">Secure commission payout link</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

            <div class="bg-gradient-to-br from-primary-600 to-primary-800 px-6 py-6 text-white">
                <p class="text-xs font-semibold text-primary-100 uppercase tracking-wider">Amount Due</p>
                <p class="text-3xl font-black mt-1">₦{{ number_format($commission->total_amount, 2) }}</p>
                <p class="text-xs text-primary-200 mt-2">Deal Reference: {{ $commission->deal_reference ?? '—' }}</p>
            </div>

            <div class="p-6 space-y-4">

                @if(session('payment_message'))
                    <div class="px-4 py-3 rounded-xl bg-blue-50 border border-blue-100 text-blue-700 text-sm">
                        {{ session('payment_message') }}
                    </div>
                @endif

                <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Property</span>
                        <span class="font-semibold text-gray-900 text-right max-w-[60%] truncate">{{ $commission->property?->title ?? 'Property #' . $commission->property_id }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Listing Realtor</span>
                        <span class="font-semibold text-gray-900">{{ $commission->listingRealtor?->First_name }} {{ $commission->listingRealtor?->Last_name }}</span>
                    </div>
                    @if($commission->referringRealtor)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Referring Realtor</span>
                            <span class="font-semibold text-gray-900">{{ $commission->referringRealtor->First_name }} {{ $commission->referringRealtor->Last_name }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Deal Date</span>
                        <span class="font-semibold text-gray-900">{{ $commission->deal_date?->format('M j, Y') ?? '—' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Status</span>
                    @php
                        $badge = match($commission->status) {
                            'paid'       => 'bg-emerald-50 text-emerald-700',
                            'processing' => 'bg-blue-50 text-blue-700',
                            'disputed'   => 'bg-red-50 text-red-700',
                            default      => 'bg-amber-50 text-amber-700',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">{{ ucfirst($commission->status) }}</span>
                </div>

                @if($commission->status === 'pending')
                    <form method="POST" action="{{ route('payment.process', $commission->payment_token) }}">
                        @csrf
                        <button type="submit" class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition-colors">
                            Pay Now
                        </button>
                    </form>
                @elseif($commission->status === 'processing')
                    <div class="text-center text-sm text-blue-600 font-semibold py-2">
                        Payment is being processed — you'll receive confirmation shortly.
                    </div>
                @elseif($commission->status === 'paid')
                    <div class="text-center text-sm text-emerald-600 font-semibold py-2">
                        This payout has been completed.
                    </div>
                @else
                    <div class="text-center text-sm text-gray-400 py-2">
                        This deal is currently marked as <span class="font-semibold capitalize">{{ $commission->status }}</span>.
                    </div>
                @endif

            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-5">
            ⚠ This is a preview / stub page for demonstration purposes — no real payment gateway is connected.
        </p>
    </div>

</body>
</html>
