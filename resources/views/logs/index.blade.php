@extends('layouts.app')

@section('page-title', 'Logs')
@section('page-sub', 'Gestiona los logs de cada dominio Cloudflare')

@section('topbar-actions')

@section('content')

{{-- Filtros --}}
<div class="card mb-4">
    <form method="GET" action="{{ route('logs.index') }}" class="flex gap-2 items-center" style="flex-wrap:wrap">
        <select name="site_id" class="form-control" style="width:auto">
            <option value="">Todos los sitios</option>
            @foreach($sites as $site)
                <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                    {{ $site->domain }}
                </option>
            @endforeach
        </select>

        <select name="reason" class="form-control" style="width:auto">
            <option value="">Todos los motivos</option>
            <option value="laliga"      {{ request('reason') === 'laliga' ? 'selected' : '' }}>⚽ LaLiga</option>
            <option value="ssl_renewal" {{ request('reason') === 'ssl_renewal' ? 'selected' : '' }}>🔒 SSL</option>
            <option value="manual"      {{ request('reason') === 'manual'      ? 'selected' : '' }}>✋ Manual</option>
        </select>

        <select name="status" class="form-control" style="width:auto">
            <option value="">Todos los estados</option>
            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>✓ success</option>
            <option value="error"   {{ request('status') === 'error'   ? 'selected' : '' }}>✗ error</option>
        </select>

        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
        <a href="{{ route('logs.index') }}" class="btn btn-ghost btn-sm">Limpiar</a>
    </form>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Sitio</th>
                <th>Acción</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Mensaje</th>
                <th>Cuándo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td class="text-cyan">{{ $log->site->domain ?? '—' }}</td>
                <td style="color:{{ $log->action === 'proxy_enabled' ? 'var(--cyan)' : 'var(--orange)' }}">
                    @if ($log->action === 'proxy_enabled')
                        ↑ Proxy activado
                    @else
                        ↓ Proxy desactivado
                    @endif
                </td>
                </td>
                <td>{{ $log->reason_label }}</td>
                <td>
                    <span class="badge {{ $log->status === 'success' ? 'badge-ok' : 'badge-failed' }}">
                        {{ $log->status }}
                    </span>
                </td>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    {{ $log->message ?? '—' }}
                </td>
                <td title="{{ $log->created_at->format('d/m/Y H:i:s') }}">
                    {{ $log->created_at->diffForHumans() }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;color:var(--muted);padding:32px">
                    Sin registros.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">
        {{ $logs->links() }}
    </div>
</div>

@endsection