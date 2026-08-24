@extends('layouts.dashboard')

@section('title', 'Agency Partners — Admin')
@section('role_label', 'Admin')

@section('sidebar_nav')
    @include('dashboard.admin._sidebar')
@endsection

@section('content')
    @livewire('admin.agencies')
@endsection
