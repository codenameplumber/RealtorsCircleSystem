@extends('layouts.dashboard')
@section('title', 'Payments')
@section('role_label', 'In-House Agent')
@section('page_title', 'Payments')
@section('page_subtitle', 'Payout status and payment links for your logged deals')
@section('sidebar_nav') @include('dashboard.agent._sidebar') @endsection

@section('content')
    @livewire('agent.payments')
@endsection
