@extends('layouts.dashboard')
@section('title','Approvals')
@section('role_label','Admin')
@section('page_title','Pending Approvals')
@section('page_subtitle','Approve or reject realtor accounts and property listings')
@section('sidebar_nav') @include('dashboard.admin._sidebar') @endsection
@section('content')
    @livewire('admin.approvals')
@endsection
