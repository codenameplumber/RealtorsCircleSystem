@extends('layouts.dashboard')
@section('title','Appointments')
@section('role_label','Realtor')
@section('page_title','Appointments')
@section('page_subtitle','Manage and respond to property viewing requests')
@section('sidebar_nav') @include('dashboard.realtor._sidebar') @endsection

@section('content')
    <livewire:realtor.appointments />
@endsection
