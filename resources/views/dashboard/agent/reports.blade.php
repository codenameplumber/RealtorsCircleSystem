@extends('layouts.dashboard')
@section('title', 'Reports')
@section('role_label', 'In-House Agent')
@section('page_title', 'Performance Reports')
@section('page_subtitle', 'Statistics for your location and your activity')
@section('sidebar_nav') @include('dashboard.agent._sidebar') @endsection

@section('content')
    @livewire('agent.reports')
@endsection
