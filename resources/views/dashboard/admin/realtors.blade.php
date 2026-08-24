@extends('layouts.dashboard')
@section('title','Realtors')
@section('role_label','Admin')
@section('page_title','Realtors')
@section('page_subtitle','All registered realtors — search, filter and view profiles')
@section('sidebar_nav') @include('dashboard.admin._sidebar') @endsection
@section('content')
    @livewire('admin.realtors')
@endsection
