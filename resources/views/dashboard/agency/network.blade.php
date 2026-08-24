@extends('layouts.dashboard')
@section('title', 'My Network')
@section('role_label', 'Agency Partner')
@section('page_title', 'My Network')
@section('page_subtitle', 'Realtors onboarded through your referral link')
@section('sidebar_nav') @include('dashboard.agency._sidebar') @endsection
@section('content')
    @livewire('agency.network')
@endsection
