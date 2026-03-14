@extends('layouts.guest')

@section('title', 'Restablecer contraseña')

@section('content')
    <div class="guest-card-title">Nueva contraseña</div>
    <div class="guest-card-sub">Elige una contraseña segura para tu cuenta.</div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input id="email" name="email" type="email"
                class="form-control {{ $errors->get('email') ? 'is-invalid' : '' }}"
                value="{{ old('email', $request->email) }}"
                required autofocus autocomplete="username" />
            @foreach($errors->get('email') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Nueva contraseña</label>
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
            Restablecer contraseña →
        </button>
    </form>
@endsection