@extends('layouts.app')

@section('page-title', 'Nuevo sitio')
@section('page-sub', 'Añade un dominio gestionado con Cloudflare')

@section('topbar-actions')

@section('content')

<div class="flex mb-4">
    <a href="{{ route('sites.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
</div>

<div style="max-width: 560px;">
    <div class="card">
        <div class="card-title">Información del sitio</div>

        <form method="POST" action="{{ route('sites.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Nombre</label>
                <input id="name" name="name" type="text"
                    class="form-control {{ $errors->get('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name') }}"
                    placeholder="Mi tienda online"
                    required autofocus />
                @foreach($errors->get('name') as $err)
                    <div class="invalid-feedback">{{ $err }}</div>
                @endforeach
            </div>

            <div class="form-group">
                <label class="form-label" for="domain">Dominio</label>
                <input id="domain" name="domain" type="text"
                    class="form-control {{ $errors->get('domain') ? 'is-invalid' : '' }}"
                    value="{{ old('domain') }}"
                    placeholder="tiendaonline.es"
                    required />
                @foreach($errors->get('domain') as $err)
                    <div class="invalid-feedback">{{ $err }}</div>
                @endforeach
            </div>

            <div class="form-group">
                <label class="form-label" for="cloudflare_zone_id">Cloudflare Zone ID</label>
                <input id="cloudflare_zone_id" name="cloudflare_zone_id" type="text"
                    class="form-control {{ $errors->get('cloudflare_zone_id') ? 'is-invalid' : '' }}"
                    value="{{ old('cloudflare_zone_id') }}"
                    placeholder="a1b2c3d4e5f6..."
                    required />
                @foreach($errors->get('cloudflare_zone_id') as $err)
                    <div class="invalid-feedback">{{ $err }}</div>
                @endforeach
                <div style="font-size:10px;color:var(--muted);margin-top:6px;">
                    Lo encuentras en el Overview de tu dominio en Cloudflare → zona derecha inferior.
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    Guardar sitio
                </button>
                <a href="{{ route('sites.index') }}" class="btn btn-ghost">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@endsection