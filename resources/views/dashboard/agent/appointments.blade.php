@extends('layouts.dashboard')
@section('title', 'Appointments')
@section('role_label', 'In-House Agent')
@section('page_title', 'Appointments')
@section('page_subtitle', 'Manage inspection and viewing requests in your area')
@section('sidebar_nav') @include('dashboard.agent._sidebar') @endsection

@section('content')
    @livewire('agent.appointments')
@endsection
