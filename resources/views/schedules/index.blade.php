@extends('layouts.app')

@section('page-title', 'Schedules')
@section('page-sub', 'Gestiona los schedules de cada dominio Cloudflare')

@section('topbar-actions')

@section('content')

<div class="flex mb-4">
    <a href="{{ route('schedules.create') }}" class="btn btn-primary">+ Añadir Schedule</a>
</div>

@endsection