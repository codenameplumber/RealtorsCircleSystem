@extends('layouts.dashboard')
@section('title','Overview')
@section('role_label','Admin — ' . (session('admin_role') === 'super_admin' ? 'Super Admin' : ucfirst(session('admin_role','Manager'))))
@section('page_title','Admin Overview')
@section('page_subtitle','Platform-wide stats — filter by state')
@section('sidebar_nav') @include('dashboard.admin._sidebar') @endsection
@section('content')
    @livewire('admin.overview')
@endsection
