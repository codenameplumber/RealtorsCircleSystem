@extends('layouts.dashboard')
@section('title', 'Settings')
@section('role_label', 'Agency Partner')
@section('page_title', 'Settings')
@section('page_subtitle', 'Manage your agency profile and security')
@section('sidebar_nav') @include('dashboard.agency._sidebar') @endsection
@section('content')
    @livewire('agency.settings')
@endsection
