@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('content')
    <div class="guest-card-title">Crear cuenta</div>
    <div class="guest-card-sub">Empieza a automatizar tu proxy de Cloudflare.</div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Nombre</label>
            <input id="name" name="name" type="text"
                class="form-control {{ $errors->get('name') ? 'is-invalid' : '' }}"
                value="{{ old('name') }}"
                required autofocus autocomplete="name" />
            @foreach($errors->get('name') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input id="email" name="email" type="email"
                class="form-control {{ $errors->get('email') ? 'is-invalid' : '' }}"
                value="{{ old('email') }}"
                required autocomplete="username" />
            @foreach($errors->get('email') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Contraseña</label>
            <input id="password" name="password" type="password"
                class="form-control {{ $errors->get('password') ? 'is-invalid' : '' }}"
                required autocomplete="new-password" />
            @foreach($errors->get('password') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                class="form-control {{ $errors->get('password_confirmation') ? 'is-invalid' : '' }}"
                required autocomplete="new-password" />
            @foreach($errors->get('password_confirmation') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="divider"></div>

        <button type="submit" class="btn btn-primary btn-full">
            Crear cuenta →
        </button>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="guest-link">
                ¿Ya tienes cuenta? Inicia sesión
            </a>
        </div>
    </form>
@endsection