@extends('layouts.dashboard')
@section('title','Settings')
@section('role_label','Admin')
@section('page_title','Settings')
@section('page_subtitle','Manage your profile and platform configuration')
@section('sidebar_nav') @include('dashboard.admin._sidebar') @endsection

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Tab navigation --}}
<div class="flex gap-1 mb-6 bg-gray-100 p-1 rounded-xl w-fit">
    <a href="#profile" onclick="showTab('profile')" id="tab-profile"
       class="tab-btn px-5 py-2 rounded-lg text-sm font-medium transition-colors bg-white text-gray-900 shadow-sm">
        Profile
    </a>
    <a href="#password" onclick="showTab('password')" id="tab-password"
       class="tab-btn px-5 py-2 rounded-lg text-sm font-medium transition-colors text-gray-500 hover:text-gray-700">
        Change Password
    </a>
    <a href="#platform" onclick="showTab('platform')" id="tab-platform"
       class="tab-btn px-5 py-2 rounded-lg text-sm font-medium transition-colors text-gray-500 hover:text-gray-700">
        Platform
    </a>
</div>

{{-- Profile Tab --}}
<div id="panel-profile">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-xl">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold">
                {{ strtoupper(substr($admin->First_name, 0, 1)) }}
            </div>
            <div>
                <p class="font-bold text-gray-900">{{ $admin->First_name }} {{ $admin->Last_name }}</p>
                <p class="text-sm text-gray-400">{{ ucfirst(str_replace('_', ' ', $admin->Role)) }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.profile') }}" class="space-y-4" onsubmit="return submitWithLoading(this, 'Saving…')">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $admin->First_name) }}" required
                           class="w-full px-4 py-2.5 border @error('first_name') border-red-400 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('first_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $admin->Last_name) }}" required
                           class="w-full px-4 py-2.5 border @error('last_name') border-red-400 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('last_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $admin->Email) }}" required
                       class="w-full px-4 py-2.5 border @error('email') border-red-400 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $admin->Phone) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <input type="text" value="{{ ucfirst(str_replace('_', ' ', $admin->Role)) }}" disabled
                       class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-xl text-sm text-gray-400 cursor-not-allowed">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors disabled:opacity-70 disabled:cursor-not-allowed inline-flex items-center gap-2">
                Save Profile
            </button>
        </form>
    </div>
</div>

{{-- Change Password Tab --}}
<div id="panel-password" class="hidden">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-xl">
        <h3 class="font-bold text-gray-900 mb-5">Change Password</h3>

        <form method="POST" action="{{ route('admin.settings.password') }}" class="space-y-4" onsubmit="return submitWithLoading(this, 'Updating…')">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <div class="relative">
                    <input type="password" name="current_password" id="pw_current" required
                           class="w-full px-4 py-2.5 pr-10 border @error('current_password') border-red-400 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <button type="button" onclick="togglePw('pw_current',this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="eye-show w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-hide w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                @error('current_password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <div class="relative">
                    <input type="password" name="password" id="pw_new" required minlength="8"
                           class="w-full px-4 py-2.5 pr-10 border @error('password') border-red-400 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <button type="button" onclick="togglePw('pw_new',this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="eye-show w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-hide w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="pw_confirm" required minlength="8"
                           class="w-full px-4 py-2.5 pr-10 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <button type="button" onclick="togglePw('pw_confirm',this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="eye-show w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-hide w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors disabled:opacity-70 disabled:cursor-not-allowed inline-flex items-center gap-2">
                Update Password
            </button>
        </form>
    </div>
</div>

{{-- Platform Settings Tab --}}
<div id="panel-platform" class="hidden">
    <h3 class="font-bold text-gray-900 mb-5">Commission Split Configuration</h3>
    @livewire('admin.commission-settings')
</div>

@endsection

@push('scripts')
<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.querySelector('.eye-show').classList.toggle('hidden');
    btn.querySelector('.eye-hide').classList.toggle('hidden');
}

function submitWithLoading(form, loadingText) {
    const btn = form.querySelector('button[type="submit"]');
    if (!btn || btn.dataset.loading) return false;
    btn.dataset.loading = '1';
    btn.dataset.originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg><span>${loadingText}</span>`;
    return true;
}

function showTab(name) {
    ['profile','password','platform'].forEach(t => {
        document.getElementById('panel-' + t).classList.add('hidden');
        document.getElementById('tab-' + t).classList.remove('bg-white','text-gray-900','shadow-sm');
        document.getElementById('tab-' + t).classList.add('text-gray-500');
    });
    document.getElementById('panel-' + name).classList.remove('hidden');
    document.getElementById('tab-' + name).classList.add('bg-white','text-gray-900','shadow-sm');
    document.getElementById('tab-' + name).classList.remove('text-gray-500');
}

// Auto-open the password tab if there are password errors
@if($errors->has('current_password') || $errors->has('password'))
    showTab('password');
@else
    showTab('profile');
@endif
</script>
@endpush
