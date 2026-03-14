@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    <div class="guest-card-title">Bienvenido de nuevo</div>
    <div class="guest-card-sub">Accede a tu panel de CF Proxy Manager</div>

    @if (session('status'))
        <div class="alert-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input id="email" name="email" type="email"
                class="form-control {{ $errors->get('email') ? 'is-invalid' : '' }}"
                value="{{ old('email') }}" required autofocus autocomplete="username" />
            @foreach($errors->get('email') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Contraseña</label>
            <input id="password" name="password" type="password"
                class="form-control {{ $errors->get('password') ? 'is-invalid' : '' }}"
                required autocomplete="current-password" />
            @foreach($errors->get('password') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="form-check">
            <input id="remember_me" type="checkbox" name="remember" />
            <label class="form-check-label" for="remember_me">Recordarme</label>
        </div>

        <div class="divider"></div>

        <button type="submit" class="btn btn-primary btn-full">
            Entrar →
        </button>

        @if (Route::has('password.request'))
            <div class="text-center mt-4">
                <a href="{{ route('password.request') }}" class="guest-link">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        @endif
    </form>
@endsection