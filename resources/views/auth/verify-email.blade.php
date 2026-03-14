@extends('layouts.guest')

@section('title', 'Verificar email')

@section('content')
    <div class="guest-card-title">Verifica tu email</div>
    <div class="guest-card-sub">
        Te hemos enviado un enlace de verificación. Revisa tu bandeja de entrada y haz clic en el enlace para activar tu cuenta.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert-status">✓ Enlace de verificación reenviado a tu email.</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary btn-full">
            Reenviar email de verificación
        </button>
    </form>

    <div class="divider"></div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-ghost btn-full">
            🚪 Cerrar sesión
        </button>
    </form>
@endsection