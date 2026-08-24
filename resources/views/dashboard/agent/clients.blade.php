@extends('layouts.dashboard')
@section('title', 'Clients')
@section('role_label', 'In-House Agent')
@section('page_title', 'Clients')
@section('page_subtitle', 'Clients who have made inquiries on properties in your area')
@section('sidebar_nav') @include('dashboard.agent._sidebar') @endsection

@section('content')
    @livewire('agent.clients')
@endsection
