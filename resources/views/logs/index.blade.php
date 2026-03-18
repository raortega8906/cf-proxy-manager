@extends('layouts.app')

@section('page-title', 'Logs')
@section('page-sub', 'Gestiona los logs de cada dominio Cloudflare')

@section('topbar-actions')

@section('content')

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

    {{-- <div class="pagination">
        {{ $logs->links() }}
    </div> --}}
</div>

@endsection