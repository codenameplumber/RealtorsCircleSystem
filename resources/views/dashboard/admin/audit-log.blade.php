@extends('layouts.dashboard')
@section('title','Audit Log')
@section('role_label','Admin')
@section('page_title','Audit Log')
@section('page_subtitle','A record of every approval, suspension, and setting change made by admins')
@section('sidebar_nav') @include('dashboard.admin._sidebar') @endsection
@section('content')
    @livewire('admin.audit-log')
@endsection
