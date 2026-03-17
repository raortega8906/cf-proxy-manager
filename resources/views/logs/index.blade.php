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