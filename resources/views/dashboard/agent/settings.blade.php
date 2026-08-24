@extends('layouts.dashboard')
@section('title', 'Settings')
@section('role_label', 'In-House Agent')
@section('page_title', 'Settings')
@section('page_subtitle', 'Manage your profile, password, and bank information')
@section('sidebar_nav') @include('dashboard.agent._sidebar') @endsection

@section('content')
    @livewire('agent.settings')
@endsection
