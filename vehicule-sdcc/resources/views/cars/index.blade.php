@extends('layouts.app')

@if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
    @section('title', 'SDCC - Gestion des Véhicules')
@else
    @section('title', 'SDCC - Véhicules')
@endif

@section('content')

@if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
    @include('cars.admin-index')
@else
    @include('cars.employee-index')
@endif

@endsection

