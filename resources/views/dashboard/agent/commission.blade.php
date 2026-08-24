@extends('layouts.dashboard')
@section('title', 'Commission')
@section('role_label', 'In-House Agent')
@section('page_title', 'Commission')
@section('page_subtitle', 'Your earnings from facilitated deals')
@section('sidebar_nav') @include('dashboard.agent._sidebar') @endsection

@section('content')
    @livewire('agent.commission')
@endsection
