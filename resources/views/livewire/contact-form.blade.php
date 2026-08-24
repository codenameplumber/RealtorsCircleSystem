<div>
    @if($sent)
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Message Sent!</h3>
            <p class="text-gray-500 mb-6">Thank you for reaching out. We'll get back to you within 24 hours.</p>
            <button wire:click="resetForm" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 hover:text-primary-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Send another message
            </button>
        </div>
    @else
        <form wire:submit="send" class="space-y-5">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                    <input wire:model="name" type="text" placeholder="John Doe"
                           class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition @error('name') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-gray-400 text-xs">(optional)</span></label>
                    <input wire:model="phone" type="tel" placeholder="+234 800 000 0000"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input wire:model="email" type="email" placeholder="you@example.com"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input wire:model="subject" type="text" placeholder="How can we help you?"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition @error('subject') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                @error('subject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea wire:model="body" rows="5" placeholder="Write your message here (min 20 characters)..."
                          class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition resize-none @error('body') border-red-400 bg-red-50 @else border-gray-300 @enderror"></textarea>
                <div class="flex items-center justify-between mt-1">
                    @error('body')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @else
                        <span></span>
                    @enderror
                    <span class="text-xs text-gray-400">{{ strlen($body) }}/2000</span>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
                    wire:loading.class="opacity-75 cursor-not-allowed" wire:loading.attr="disabled">
                <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span wire:loading.remove>Send Message</span>
                <span wire:loading>Sending...</span>
            </button>
        </form>
    @endif
</div>
