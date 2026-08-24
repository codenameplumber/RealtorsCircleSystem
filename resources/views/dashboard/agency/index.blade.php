@extends('layouts.dashboard')
@section('title', 'Overview')
@section('role_label', 'Agency Partner')
@section('page_title', 'Overview')
@section('page_subtitle', 'Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . (session('agency_name') ?? 'Agency') . '!')
@section('sidebar_nav') @include('dashboard.agency._sidebar') @endsection
@section('content')
    @livewire('agency.overview')
@endsection
