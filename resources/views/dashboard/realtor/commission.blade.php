@extends('layouts.dashboard')
@section('title','Commission')
@section('role_label','Realtor')
@section('page_title','Commission Earnings')
@section('page_subtitle','Track your deal earnings and payout status')
@section('sidebar_nav') @include('dashboard.realtor._sidebar') @endsection

@section('content')
@livewire('realtor.commission')
@endsection
