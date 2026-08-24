@extends('layouts.public')

@section('title', 'Create Account — Realtors Circle')

@section('content')
<div class="min-h-screen bg-gray-50 py-14 px-4">
    <div class="max-w-2xl mx-auto">

        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-primary-600">Realtors Circle</span>
            </a>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Create a Realtor Account</h1>
                <p class="text-sm text-gray-500 mt-1">Join thousands of realtors on Nigeria's #1 P2P property platform</p>
            </div>

            <!-- Info banner -->
            <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-100 rounded-xl mb-6">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-blue-700">Your account will be reviewed by our team. You'll be notified once approved — usually within 24 hours.</p>
            </div>

            <livewire:auth.register-form />
        </div>

        <p class="text-center text-sm text-gray-500 mt-5">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:text-primary-700">Sign in</a>
        </p>
    </div>
</div>
@endsection
