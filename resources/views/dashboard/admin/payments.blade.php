@extends('layouts.dashboard')
@section('title','Payments')
@section('role_label','Admin')
@section('page_title','Payments')
@section('page_subtitle','Payout ledger and payment-link generation across the platform')
@section('sidebar_nav') @include('dashboard.admin._sidebar') @endsection
@section('content')
    @livewire('admin.payments')
@endsection
