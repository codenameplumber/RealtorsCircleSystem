@extends('layouts.dashboard')
@section('title','Agents')
@section('role_label','Admin')
@section('page_title','Agents')
@section('page_subtitle','Manage field agents and track performance')
@section('sidebar_nav') @include('dashboard.admin._sidebar') @endsection
@section('content')
    @livewire('admin.agents')
@endsection
