@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-sub', 'Estado en tiempo real de todos tus dominios')

@section('topbar-actions')

        <button type="submit" class="btn btn-warning btn-sm">⚽ Desactivar todos los sitios</button>
   
        <button type="submit" class="btn btn-ghost btn-sm">↑ Reactivar todos los sitios</button>

@endsection

@section('content')

{{-- STATS --}}
<div class="grid-4 mb-4">
    <div class="card">
        <div class="stat-val text-green">{{ $countEnabled }}/{{ $sites->count() }}</div>
        <div class="stat-lbl">Con proxy activo</div>
    </div>
    <div class="card">
        <div class="stat-val text-orange">{{ $countLaLiga }}/{{ $sites->count() }}</div>
        <div class="stat-lbl">Afectadas por LaLiga</div>
    </div>
    <div class="card">
        <div class="stat-val text-cyan">{{ $countSsl }}/{{ $sites->count() }}</div>
        <div class="stat-lbl">SSL auto-renovación</div>
    </div>
    <div class="card">
        <div class="stat-val text-yellow">0</div>
        <div class="stat-lbl">Schedules pendientes</div>
    </div>
</div>

{{-- SITIOS + SCHEDULES --}}
<div class="grid-2 mt-6">

    {{-- Sitios --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <div class="card-title">Sitios</div>
            <a href="{{ route('sites.index') }}" class="btn btn-ghost btn-sm">Ver todos</a>
        </div>

        @foreach($sites as $site)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid rgba(26,42,58,0.5)">
            <div style="flex:1">
                <div style="font-size:13px;color:var(--white)">{{ $site->name }}</div>
                <div style="font-size:11px;color:var(--cyan)">{{ $site->domain }}</div>
            </div>
            @if($site->affected_by_laliga)
                <span class="badge badge-laliga">⚽</span>
            @endif
            @if($site->ssl_auto_renewal)
                <span class="badge badge-ssl">🔒 SSL</span>
            @endif
            <form action="{{ route('dashboard.activateOrDesactivateProxy', $site) }}" method="POST">
                @csrf
                @method('PATCH')
                <button
                    type="submit"
                    class="toggle {{ $site->proxy_enabled ? 'toggle-on' : 'toggle-off' }}"
                    title="{{ $site->proxy_enabled ? 'Proxy ON — clic para desactivar' : 'Proxy OFF — clic para activar' }}"
                >
                    <div class="toggle-knob"></div>
                </button>
            </form>
        </div>
        @endforeach
    </div>

</div>

@endsection