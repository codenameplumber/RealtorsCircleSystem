<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your KYC — Realtors Circle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:{500:'#10b981',600:'#059669',700:'#047857'}}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-primary-200/50">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Complete Your KYC</h1>
            <p class="text-gray-500 text-sm mt-1">Hi {{ $user->First_name }} — one last step to activate your account.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            @if ($errors->any())
                <div class="mb-4 p-3.5 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ID Type</label>
                    <select name="kyc_id_type" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Select ID type</option>
                        <option value="National ID (NIN)">National ID (NIN)</option>
                        <option value="Driver's License">Driver's License</option>
                        <option value="International Passport">International Passport</option>
                        <option value="Voter's Card">Voter's Card</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Front of ID</label>
                    <input name="kyc_front" type="file" accept="image/*,.pdf" required
                           class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Back of ID <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input name="kyc_back" type="file" accept="image/*,.pdf"
                           class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700">
                </div>
                <button type="submit" class="w-full py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors">
                    Submit KYC Documents
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-5">This link is unique to you and expires after use — don't share it.</p>
    </div>
</body>
</html>
