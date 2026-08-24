@extends('layouts.dashboard')
@section('title', 'Network Performance')
@section('role_label', 'Agency Partner')
@section('page_title', 'Network Performance')
@section('page_subtitle', 'Aggregate stats for your realtor network')
@section('sidebar_nav') @include('dashboard.agency._sidebar') @endsection
@section('content')
    @livewire('agency.performance')
@endsection
