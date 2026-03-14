@extends('layouts.app')

@section('page-title', 'Sitios')
@section('page-sub', 'Gestiona el proxy de cada dominio Cloudflare')

@section('topbar-actions')

@section('content')

<div class="flex mb-4">
    <a href="{{ route('sites.create') }}" class="btn btn-primary">+ Añadir Sitio</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre / Dominio</th>
                <th>Características</th>
                <th>SSL próximo</th>
                <th>Estado</th>
                <th>Proxy</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @if ($sites->isEmpty())
                <tr>
                    <td colspan="6" class="text-center text-muted">No hay sitios añadidos. ¡Empieza añadiendo uno!</td>
                </tr>
            @else
                @foreach($sites as $site)
                <tr>
                    <td>
                        <div style="color:var(--white);font-size:13px">{{ $site->name }}</div>
                        <div style="color:var(--cyan);font-size:11px">{{ $site->domain }}</div>
                    </td>
                    <td>
                        @if($site->affected_by_liga)
                            <span class="badge badge-laliga">⚽ LaLiga</span>
                        @endif
                        @if($site->ssl_auto_renewal)
                            <span class="badge badge-ssl">🔒 SSL auto</span>
                        @endif
                    </td>
                    <td>
                        @if($site->ssl_next_renewal)
                            <span style="color:{{ $site->ssl_days_until_renewal <= 5 ? 'var(--yellow)' : 'var(--muted2)' }}">
                                {{ $site->ssl_next_renewal->format('d/m/Y') }}
                                @if($site->ssl_days_until_renewal !== null)
                                    <small>({{ $site->ssl_days_until_renewal }}d)</small>
                                @endif
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($site->proxy_enabled)
                            <span class="badge badge-success">Proxy ON</span>
                        @else
                            <span class="badge badge-danger">Proxy OFF</span>
                        @endif
                    </td>
                    <td>
                        {{-- <button class="toggle {{ $site->proxy_enabled ? 'toggle-on' : 'toggle-off' }}" data-site-id="{{ $site->id }}" data-url="{{ route('sites.toggleProxy', $site) }}">
                            <span class="toggle-thumb"></span>
                        </button> --}}
                    </td>
                    <td>
                        {{-- <div class="flex gap-2">
                            <a href="{{ route('sites.edit', $site) }}" class="btn btn-ghost btn-sm">Editar</a>
                            <form action="{{ route('sites.destroy', $site) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar {{ $site->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </div> --}}
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
// document.querySelectorAll('.toggle[data-site-id]').forEach(btn => {
//     btn.addEventListener('click', async () => {
//         btn.style.opacity = '0.5'; btn.disabled = true;
//         try {
//             const res  = await fetch(btn.dataset.url, {
//                 method: 'POST',
//                 headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
//             });
//             const data = await res.json();
//             if (data.success) {
//                 btn.classList.toggle('toggle-on', data.proxy_enabled);
//                 btn.classList.toggle('toggle-off', !data.proxy_enabled);
//             }
//         } finally { btn.style.opacity = '1'; btn.disabled = false; }
//     });
// });
</script>
@endpush
