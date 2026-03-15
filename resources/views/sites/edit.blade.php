@extends('layouts.app')

@section('page-title', 'Actualizar sitio')
@section('page-sub', 'Actualiza un dominio gestionado con Cloudflare')

@section('content')

<div class="flex mb-4">
    <a href="{{ route('sites.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
</div>

<div style="max-width: 560px;">
    <div class="card">
        <div class="card-title">Información del sitio</div>

        <form method="POST" action="{{ route('sites.update', $proxySite) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="name">Nombre</label>
                <input id="name" name="name" type="text"
                    class="form-control {{ $errors->get('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name', $proxySite->name) }}"
                    placeholder="Mi tienda online"
                    required autofocus />
                @foreach($errors->get('name') as $err)
                    <div class="invalid-feedback">{{ $err }}</div>
                @endforeach
            </div>

            <div class="form-group">
                <label class="form-label" for="domain">Dominio</label>
                <input id="domain" type="text"
                    class="form-control"
                    value="{{ $proxySite->domain }}"
                    style="cursor:not-allowed;opacity:0.5;"
                    disabled />
            </div>

            <div class="form-group">
                <label class="form-label" for="cloudflare_zone_id">Cloudflare Zone ID</label>
                <input id="cloudflare_zone_id" type="text"
                    class="form-control"
                    value="{{ $proxySite->cloudflare_zone_id }}"
                    style="cursor:not-allowed;opacity:0.5;"
                    disabled />
                <div style="font-size:10px;color:var(--muted);margin-top:6px;">
                    El dominio y Zone ID no se pueden modificar tras la creación.
                </div>
            </div>

            {{-- DIVIDER --}}
            <div style="height:1px;background:var(--border);margin:24px 0;"></div>

            {{-- SSL AUTO RENEWAL --}}
            <div class="form-group">
                <label class="form-check" for="ssl_auto_renewal" style="margin-bottom:0">
                    <input id="ssl_auto_renewal" name="ssl_auto_renewal" type="checkbox"
                        value="1" {{ old('ssl_auto_renewal', $proxySite->ssl_auto_renewal) ? 'checked' : '' }}
                        onchange="document.getElementById('ssl_renewal_date_wrap').style.display = this.checked ? 'block' : 'none'" />
                    <span class="form-check-label">
                        🔒 Renovación SSL automática
                    </span>
                </label>
                <div style="font-size:10px;color:var(--muted);margin-top:4px;padding-left:22px;">
                    Desactiva el proxy automáticamente durante la ventana de renovación del certificado.
                </div>
            </div>

            <div id="ssl_renewal_date_wrap"
                style="display:{{ old('ssl_auto_renewal', $proxySite->ssl_auto_renewal) ? 'block' : 'none' }};margin-top:12px;">
                <div class="form-group">
                    <label class="form-label" for="ssl_next_renewal">Próxima renovación SSL</label>
                    <input id="ssl_next_renewal" name="ssl_next_renewal" type="date"
                        class="form-control {{ $errors->get('ssl_next_renewal') ? 'is-invalid' : '' }}"
                        value="{{ old('ssl_next_renewal', $proxySite->ssl_next_renewal?->format('Y-m-d')) }}" />
                    @foreach($errors->get('ssl_next_renewal') as $err)
                        <div class="invalid-feedback">{{ $err }}</div>
                    @endforeach
                    <div style="font-size:10px;color:var(--muted);margin-top:6px;">
                        El proxy se desactivará 5 minutos antes y se reactivará 35 minutos después.
                    </div>
                </div>
            </div>

            {{-- AFFECTED BY LALIGA --}}
            <div class="form-group">
                <label class="form-check" for="affected_by_laliga" style="margin-bottom:0">
                    <input id="affected_by_laliga" name="affected_by_laliga" type="checkbox"
                        value="1" {{ old('affected_by_laliga', $proxySite->affected_by_laliga) ? 'checked' : '' }} />
                    <span class="form-check-label">
                        ⚽ Afectado por bloqueos de LaLiga
                    </span>
                </label>
                <div style="font-size:10px;color:var(--muted);margin-top:4px;padding-left:22px;">
                    El proxy se desactivará automáticamente durante los partidos de LaLiga.
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    Actualizar sitio
                </button>
                <a href="{{ route('sites.index') }}" class="btn btn-ghost">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@endsection