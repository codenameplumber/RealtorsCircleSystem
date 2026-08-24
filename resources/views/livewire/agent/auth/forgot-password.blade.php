<div>
    {{-- Success state --}}
    @if($sent)
        <div class="text-center py-4">
            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/></svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">Check Your Email</h3>
            <p class="text-sm text-gray-500 mb-5">If an account with that email exists, a reset link has been sent. Check your inbox and spam folder.</p>
            <a href="{{ route('agent.login') }}" class="text-sm text-primary-600 font-semibold hover:underline">← Back to sign in</a>
        </div>
    @else
        {{-- Error alert --}}
        @if($errors->any())
            <div class="mb-5 flex items-start gap-2 p-3.5 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form wire:submit="send" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Agent Email Address</label>
                <input wire:model="email"
                       type="email"
                       autocomplete="email"
                       autofocus
                       placeholder="agent@realtorscircle.test"
                       class="w-full px-4 py-2.5 border @error('email') border-red-300 bg-red-50 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    wire:loading.attr="disabled"
                    class="w-full flex items-center justify-center gap-2 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-70 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-colors">
                <svg wire:loading wire:target="send" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span wire:loading.remove wire:target="send">Send Reset Link</span>
                <span wire:loading wire:target="send">Sending...</span>
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="{{ route('agent.login') }}" class="text-xs text-primary-600 font-medium hover:underline">← Back to sign in</a>
        </div>
    @endif
</div>
