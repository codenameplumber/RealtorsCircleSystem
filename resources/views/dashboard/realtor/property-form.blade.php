@extends('layouts.dashboard')
@section('title', isset($propertyId) ? 'Edit Property' : 'Add Property')
@section('role_label', 'Realtor')
@section('page_title', isset($propertyId) ? 'Edit Property' : 'Add New Property')
@section('page_subtitle', isset($propertyId) ? 'Update your listing details' : 'List a property on the Realtors Circle network')
@section('sidebar_nav') @include('dashboard.realtor._sidebar') @endsection

@section('content')
    <livewire:realtor.property-form :propertyId="$propertyId ?? null" />
@endsection
