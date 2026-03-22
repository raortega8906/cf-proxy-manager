@extends('layouts.app')

@section('page-title', 'Perfil')
@section('page-sub', 'Gestiona la información de tu cuenta')

@section('topbar-actions')

@section('content')

<div class="space-y-6">

    <div class="card">
        <h2 class="logo-text text-lg mb-4">Información de perfil</h2>

        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="card mt-4">
        <h2 class="logo-text text-lg mb-4">Actualizar contraseña</h2>

        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="card mt-4" style="display:none;">
        <h2 class="logo-text text-lg mb-4 text-red-500">Eliminar cuenta</h2>

        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>

@endsection