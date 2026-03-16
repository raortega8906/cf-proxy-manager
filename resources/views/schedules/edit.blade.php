@extends('layouts.app')

@section('page-title', 'Actualizar schedule')
@section('page-sub', 'Actualizar un schedule para el dominio')

@section('content')

<div class="flex mb-4">
    <a href="{{ route('schedules.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
</div>

@endsection