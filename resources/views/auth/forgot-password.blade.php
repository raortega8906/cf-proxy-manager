@extends('layouts.guest')

@section('title', 'Recuperar contraseña')

@section('content')
    <div class="guest-card-title">Recuperar contraseña</div>
    <div class="guest-card-sub">Introduce tu email y te enviaremos un enlace para restablecer tu contraseña.</div>

    @if (session('status'))
        <div class="alert-status">✓ {{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input id="email" name="email" type="email"
                class="form-control {{ $errors->get('email') ? 'is-invalid' : '' }}"
                value="{{ old('email') }}" required autofocus />
            @foreach($errors->get('email') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="divider"></div>

        <button type="submit" class="btn btn-primary btn-full">
            Enviar enlace →
        </button>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="guest-link">← Volver al login</a>
        </div>
    </form>
@endsection