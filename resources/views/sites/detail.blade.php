@extends('layouts.app')

@section('page-title', $proxySite->name)
@section('page-sub', $proxySite->domain)

@section('topbar-actions')
    <a href="{{ route('sites.edit', $proxySite) }}" class="btn btn-ghost btn-sm">Editar</a>
    <a href="{{ route('sites.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
@endsection

@section('content')

    <form action="{{ route('sites.activateOrDeactivateProxy', $proxySite) }}" method="POST">
        @csrf
        @method('PATCH')
        <button type="submit" class="toggle {{ $proxySite->proxy_enabled ? 'toggle-on' : 'toggle-off' }}"
                title="{{ $proxySite->proxy_enabled ? 'Proxy ON — clic para desactivar' : 'Proxy OFF — clic para activar' }}">
            <div class="toggle-knob"></div>
        </button>
    </form>

    {{-- STATS --}}
    <div class="grid-4 mb-4 mt-4">
        <div class="card">
            <div class="stat-val {{ $proxySite->proxy_enabled ? 'text-green' : 'text-orange' }}">
                {{ $proxySite->proxy_enabled ? 'ON' : 'OFF' }}
            </div>
            <div class="stat-lbl">Estado proxy</div>
        </div>
        <div class="card">
            <div class="stat-val text-cyan">{{ $logs->count() }}</div>
            <div class="stat-lbl">Acciones registradas</div>
        </div>
        <div class="card">
            <div class="stat-val text-yellow">{{ $schedules->total() }}</div>
            <div class="stat-lbl">Schedules asociados</div>
        </div>
        <div class="card">
            <div class="stat-val text-{{ $proxySite->ssl_auto_renewal ? 'cyan' : 'muted' }}">
                {{ $proxySite->ssl_auto_renewal ? ($proxySite->ssl_next_renewal ? $proxySite->ssl_next_renewal->format('d/m/Y') : '—') : '—' }}
            </div>
            <div class="stat-lbl">Próx. renovación SSL</div>
        </div>
    </div>

    {{-- INFO + TIMELINE --}}
    <div class="grid-2 mt-6">

        {{-- Info del sitio --}}
        <div class="card">
            <div class="card-title">Información del sitio</div>

            <table style="width:100%;font-size:12px;border-collapse:collapse;">
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px 0;color:var(--muted);width:140px;">Nombre</td>
                    <td style="padding:10px 0;color:var(--text)">{{ $proxySite->name }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px 0;color:var(--muted)">Dominio</td>
                    <td style="padding:10px 0;color:var(--cyan)">{{ $proxySite->domain }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px 0;color:var(--muted)">Estado proxy</td>
                    <td style="padding:10px 0">
                        @if($proxySite->proxy_enabled)
                            <span class="badge badge-ok">Proxy ON</span>
                        @else
                            <span class="badge badge-off">Proxy OFF</span>
                        @endif
                    </td>
                </tr>
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px 0;color:var(--muted)">LaLiga</td>
                    <td style="padding:10px 0">
                        @if($proxySite->affected_by_laliga)
                            <span class="badge badge-laliga">⚽ Afectado</span>
                        @else
                            <span style="color:var(--muted);font-size:11px">No afectado</span>
                        @endif
                    </td>
                </tr>
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px 0;color:var(--muted)">SSL auto</td>
                    <td style="padding:10px 0">
                        @if($proxySite->ssl_auto_renewal)
                            <span class="badge badge-ssl">🔒 Activo</span>
                        @else
                            <span style="color:var(--muted);font-size:11px">No configurado</span>
                        @endif
                    </td>
                </tr>
                @if($proxySite->ssl_next_renewal)
                    <tr>
                        <td style="padding:10px 0;color:var(--muted)">Próx. SSL</td>
                        <td style="padding:10px 0;color:{{ $proxySite->ssl_days_until_renewal <= 5 ? 'var(--yellow)' : 'var(--muted2)' }}">
                            {{ $proxySite->ssl_next_renewal->format('d/m/Y') }}
                            @if($proxySite->ssl_days_until_renewal !== null)
                                <small>({{ $proxySite->ssl_days_until_renewal }}d)</small>
                            @endif
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        {{-- Timeline visual --}}
        <div class="card">
            <div class="card-title">Timeline de actividad</div>
            <canvas id="timelineChart" height="200"></canvas>
        </div>

    </div>

    {{-- SCHEDULES --}}
    <div class="card mt-6">
        <div class="card-title">Schedules asociados</div>
        @if($schedules->isEmpty())
            <p class="text-muted" style="font-size:12px">No hay schedules asociados a este sitio.</p>
        @else
            <table class="table">
                <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Desactivar</th>
                    <th>Reactivar</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                @foreach($schedules as $schedule)
                    <tr>
                        <td>
                            @if($schedule->type === 'ssl_renewal')
                                <span class="badge badge-ssl">🔒 SSL</span>
                            @elseif($schedule->type === 'laliga_match')
                                <span class="badge badge-laliga">⚽ LaLiga</span>
                            @else
                                <span class="badge badge-manual">✋ Manual</span>
                            @endif
                        </td>
                        <td style="color:var(--muted2);font-size:12px">{{ $schedule->description ?? '—' }}</td>
                        <td style="color:var(--orange);font-size:12px">{{ $schedule->disable_at->format('d/m/Y H:i') }}</td>
                        <td style="color:var(--green);font-size:12px">{{ $schedule->enable_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge badge-{{ $schedule->status }}">{{ $schedule->status }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="pagination">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

    {{-- LOGS --}}
    <div class="card mt-6">
        <div class="flex items-center justify-between mb-4">
            <div class="card-title" style="margin-bottom:0">Historial de acciones</div>
            <span style="font-size:11px;color:var(--muted)">{{ $logs->count() }} registros</span>
        </div>
        @if($logs->isEmpty())
            <p class="text-muted" style="font-size:12px">No hay logs para este sitio.</p>
        @else
            <table class="table">
                <thead>
                <tr>
                    <th>Acción</th>
                    <th>Razón</th>
                    <th>Estado</th>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>
                            @if($log->action === 'proxy_enabled')
                                <span style="color:var(--green);font-size:12px">↑ Activado</span>
                            @else
                                <span style="color:var(--orange);font-size:12px">↓ Desactivado</span>
                            @endif
                        </td>
                        <td>{{ $log->reasonLabel }}</td>
                        <td>
                            @if($log->status === 'success')
                                <span class="badge badge-ok">OK</span>
                            @else
                                <span class="badge badge-failed">Error</span>
                            @endif
                        </td>
                        <td style="color:var(--muted2);font-size:11px">{{ $log->message ?? '—' }}</td>
                        <td style="color:var(--muted);font-size:11px">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const timelineData = @json($timelineData);
        const parsed = JSON.parse(timelineData);

        const labels = parsed.map(d => d.date);
        const data = parsed.map(d => d.action === 'proxy_enabled' ? 1 : 0);
        const colors = data.map(v => v === 1 ? 'rgba(0,255,136,0.8)' : 'rgba(255,107,53,0.8)');

        const ctx = document.getElementById('timelineChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Estado proxy',
                    data,
                    backgroundColor: colors,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.raw === 1 ? '↑ Proxy activado' : '↓ Proxy desactivado'
                        }
                    }
                },
                scales: {
                    y: {
                        min: 0, max: 1,
                        ticks: {
                            stepSize: 1,
                            callback: v => v === 1 ? 'ON' : 'OFF',
                            color: '#4a6285'
                        },
                        grid: { color: 'rgba(26,42,58,0.5)' }
                    },
                    x: {
                        ticks: { color: '#4a6285', maxRotation: 45, font: { size: 10 } },
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
@endpush
