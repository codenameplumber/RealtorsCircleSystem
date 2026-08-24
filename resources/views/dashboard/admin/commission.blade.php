@extends('layouts.dashboard')
@section('title','Commission')
@section('role_label','Admin')
@section('page_title','Commission Management')
@section('page_subtitle','All platform deal commissions and fee tracking')
@section('sidebar_nav') @include('dashboard.admin._sidebar') @endsection
@section('content')
    @livewire('admin.commission')
@endsection
