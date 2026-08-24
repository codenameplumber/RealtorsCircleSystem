@extends('layouts.dashboard')
@section('title','Reports')
@section('role_label','Admin')
@section('page_title','Reports & Analytics')
@section('page_subtitle','Deep platform stats — properties, deals, cities, asset types')
@section('sidebar_nav') @include('dashboard.admin._sidebar') @endsection
@section('content')
    @livewire('admin.reports')
@endsection
