@extends('layouts.public')
@section('title', 'Privacy Policy — Realtors Circle')
@section('content')
<div class="bg-gradient-to-r from-gray-900 to-gray-800 py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-white mb-2">Privacy Policy</h1>
        <p class="text-gray-300">Last updated: {{ date('F Y') }}</p>
    </div>
</div>
<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 prose prose-gray">
        <p class="text-gray-600 text-lg">Your privacy matters to us. This policy explains how Realtors Circle collects, uses, and protects your information.</p>
        <h2>1. Data We Collect</h2>
        <p>We collect personal information (name, email, phone, NIN), professional details (license, specialization), and bank information solely for the purpose of facilitating real estate transactions and commission payouts.</p>
        <h2>2. How We Use Your Data</h2>
        <p>Your data is used to verify your identity, process property listings, coordinate viewings, and disburse commissions. We do not sell your data to third parties.</p>
        <h2>3. Data Security</h2>
        <p>We employ industry-standard encryption and security measures. However, no system is 100% secure. Report any suspected breaches immediately.</p>
        <h2>4. Your Rights</h2>
        <p>You have the right to access, correct, or delete your personal data at any time. Contact us at <a href="mailto:info@realtorscircle.com" class="text-primary-600">info@realtorscircle.com</a> to make a request.</p>
        <h2>5. Cookies</h2>
        <p>We use cookies to maintain your session and improve your experience. You can disable cookies in your browser settings, though this may affect platform functionality.</p>
    </div>
</section>
@endsection
