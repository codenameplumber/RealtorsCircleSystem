@extends('layouts.dashboard')
@section('title', 'Overview')
@section('role_label', 'In-House Agent')
@section('page_title', 'Overview')
@section('page_subtitle', 'Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . (session('agent_name') ?? 'Agent') . '!')
@section('sidebar_nav') @include('dashboard.agent._sidebar') @endsection

@section('content')
    @livewire('agent.overview')
@endsection
