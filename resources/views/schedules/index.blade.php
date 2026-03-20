@extends('layouts.app')

@section('page-title', 'Schedules')
@section('page-sub', 'Gestiona los schedules de cada dominio Cloudflare')

@section('topbar-actions')

@section('content')

<div class="flex mb-4">
    <a href="{{ route('schedules.create') }}" class="btn btn-primary">+ Añadir Schedule</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Desactivar proxy</th>
                <th>Activar proxy</th>
                <th>Sitios afectados</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @if($schedules->isEmpty())
                <tr>
                    <td colspan="7" class="text-center text-muted">No hay schedules creados.</td>
                </tr>
            @else
                @foreach($schedules as $schedule)
                <tr>
                    <td>
                        @if($schedule->type === 'laliga_match')
                            <span class="badge badge-laliga">⚽ LaLiga</span>
                        @elseif($schedule->type === 'ssl_renewal')
                            <span class="badge badge-ssl">🔒 SSL</span>
                        @else
                            <span class="badge badge-manual">✋ Manual</span>
                        @endif
                    </td>
                    <td style="color:var(--muted2);font-size:12px;">
                        {{ $schedule->description ?? '—' }}
                    </td>
                    <td style="font-size:12px;color:var(--orange);">
                        {{ $schedule->disable_at->format('d/m/Y H:i') }}
                    </td>
                    <td style="font-size:12px;color:var(--green);">
                        {{ $schedule->enable_at->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        <div class="flex gap-2" style="flex-wrap:wrap;">
                            @forelse($schedule->sites as $site)
                                <span style="font-size:10px;padding:2px 8px;border-radius:4px;background:rgba(0,212,255,0.08);color:var(--cyan);letter-spacing:0.05em;">
                                    {{ $site->domain }}
                                </span>
                            @empty
                                <span class="text-muted" style="font-size:12px;">—</span>
                            @endforelse
                        </div>
                    </td>
                    <td>
                        @php
                            $statusMap = [
                                'pending'   => ['class' => 'badge-pending',   'label' => 'Pendiente'],
                                'active'    => ['class' => 'badge-active',    'label' => 'Activo'],
                                'completed' => ['class' => 'badge-completed', 'label' => 'Completado'],
                                'failed'    => ['class' => 'badge-failed',    'label' => 'Fallido'],
                            ];
                            $s = $statusMap[$schedule->status] ?? ['class' => 'badge-manual', 'label' => $schedule->status];
                        @endphp
                        <span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-ghost btn-sm">Editar</a>
                            <form action="{{ route('schedules.destroy', $schedule) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar este schedule?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="pagination">
        {{ $schedules->links() }}
    </div>
</div>

@endsection