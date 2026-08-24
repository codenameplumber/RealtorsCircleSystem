@extends('layouts.dashboard')
@section('title', 'Property Approvals')
@section('role_label', 'In-House Agent')
@section('page_title', 'Property Approvals')
@section('page_subtitle', 'Review and verify property listings in your area')
@section('sidebar_nav') @include('dashboard.agent._sidebar') @endsection

@section('content')
    @livewire('agent.property-approvals')
@endsection
