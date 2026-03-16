@extends('layouts.app')

@section('page-title', 'Nuevo sitio')
@section('page-sub', 'Añade un dominio gestionado con Cloudflare')

@section('content')

<div class="flex mb-4">
    <a href="{{ route('schedules.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
</div>

@endsection