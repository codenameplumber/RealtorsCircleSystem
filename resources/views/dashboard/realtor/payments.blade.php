@extends('layouts.dashboard')
@section('title','Payments')
@section('role_label','Realtor')
@section('page_title','Payments')
@section('page_subtitle','Track the payout status of your commission earnings')
@section('sidebar_nav') @include('dashboard.realtor._sidebar') @endsection

@section('content')
@livewire('realtor.payments')
@endsection
