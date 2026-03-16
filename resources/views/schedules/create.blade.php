@extends('layouts.app')

@section('page-title', 'Nuevo schedule')
@section('page-sub', 'Añade un schedule')

@section('content')

<div class="flex mb-4">
    <a href="{{ route('schedules.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
</div>

@endsection