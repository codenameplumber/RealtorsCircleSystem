@extends('layouts.dashboard')
@section('title', 'My Earnings')
@section('role_label', 'Agency Partner')
@section('page_title', 'My Earnings')
@section('page_subtitle', 'Override commission ledger from your network deals')
@section('sidebar_nav') @include('dashboard.agency._sidebar') @endsection
@section('content')
    @livewire('agency.earnings')
@endsection
