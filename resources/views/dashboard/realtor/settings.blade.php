@extends('layouts.dashboard')
@section('title','Settings')
@section('role_label','Realtor')
@section('page_title','Account Settings')
@section('page_subtitle','Manage your profile and preferences')
@section('sidebar_nav') @include('dashboard.realtor._sidebar') @endsection

@section('content')
<livewire:realtor.profile-settings :user="$user" />
@endsection
