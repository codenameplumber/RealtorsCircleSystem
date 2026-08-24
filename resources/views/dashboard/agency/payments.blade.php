@extends('layouts.dashboard')
@section('title', 'Payment History')
@section('role_label', 'Agency Partner')
@section('page_title', 'Payment History')
@section('page_subtitle', 'Commission override payout records from your network')
@section('sidebar_nav') @include('dashboard.agency._sidebar') @endsection
@section('content')
    @livewire('agency.payments')
@endsection
