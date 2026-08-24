@extends('layouts.dashboard')
@section('title', 'Realtor Approvals')
@section('role_label', 'In-House Agent')
@section('page_title', 'Realtor Approvals')
@section('page_subtitle', 'Review and approve realtors in your assigned area')
@section('sidebar_nav') @include('dashboard.agent._sidebar') @endsection

@section('content')
    @livewire('agent.realtor-approvals')
@endsection
