@extends('layouts.dashboard')
@section('title','Properties')
@section('role_label','Admin')
@section('page_title','All Properties')
@section('page_subtitle','Review and manage every listing on the platform')
@section('sidebar_nav') @include('dashboard.admin._sidebar') @endsection
@section('content')
    @livewire('admin.properties')
@endsection
