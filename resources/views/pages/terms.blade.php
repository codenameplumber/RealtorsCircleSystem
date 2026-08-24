@extends('layouts.public')
@section('title', 'Terms & Conditions — Realtors Circle')
@section('content')
<div class="bg-gradient-to-r from-gray-900 to-gray-800 py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-white mb-2">Terms & Conditions</h1>
        <p class="text-gray-300">Last updated: {{ date('F Y') }}</p>
    </div>
</div>
<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 prose prose-gray">
        <p class="text-gray-600 text-lg">By using Realtors Circle, you agree to the following terms and conditions. Please read them carefully.</p>
        <h2>1. Acceptance of Terms</h2>
        <p>By accessing or using our platform, you agree to be bound by these Terms. If you disagree, please do not use our services.</p>
        <h2>2. Use of Platform</h2>
        <p>Realtors Circle is a P2P real estate marketplace. Users must provide accurate information and comply with all applicable Nigerian real estate laws and regulations.</p>
        <h2>3. Commission Sharing</h2>
        <p>Commission splits are governed by individual agreements between realtors and facilitated by Realtors Circle. The platform reserves the right to adjust split policies with notice.</p>
        <h2>4. Account Responsibility</h2>
        <p>You are responsible for maintaining the confidentiality of your account credentials. Report any unauthorized use to us immediately.</p>
        <h2>5. Contact</h2>
        <p>For questions, contact us at <a href="mailto:info@realtorscircle.com" class="text-primary-600">info@realtorscircle.com</a>.</p>
    </div>
</section>
@endsection
