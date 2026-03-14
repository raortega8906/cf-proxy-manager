@extends('layouts.guest')

@section('title', 'Confirmar contraseña')

@section('content')
    <div class="guest-card-title">Zona segura</div>
    <div class="guest-card-sub">Confirma tu contraseña antes de continuar.</div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="password">Contraseña</label>
            <input id="password" name="password" type="password"
                class="form-control {{ $errors->get('password') ? 'is-invalid' : '' }}"
                required autocomplete="current-password" />
            @foreach($errors->get('password') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="divider"></div>

        <button type="submit" class="btn btn-primary btn-full">
            Confirmar →
        </button>
    </form>
@endsection