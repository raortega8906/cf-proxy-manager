@extends('layouts.app')

@section('page-title', 'Editar Schedule')
@section('page-sub', 'Modifica una programación existente')

@section('content')

<div class="flex mb-4">
    <a href="{{ route('schedules.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
</div>

<div style="max-width:560px;">
    <div class="card">
        <div class="card-title">Información del schedule</div>

        <form method="POST" action="{{ route('schedules.update', $proxySchedule) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="type">Tipo</label>
                <select id="type" name="type"
                    class="form-control {{ $errors->get('type') ? 'is-invalid' : '' }}">
                    <option value="laliga_match" {{ old('type', $proxySchedule->type) === 'laliga_match' ? 'selected' : '' }}>
                        ⚽ Partido LaLiga
                    </option>
                    <option value="ssl_renewal" {{ old('type', $proxySchedule->type) === 'ssl_renewal' ? 'selected' : '' }}>
                        🔒 Renovación SSL
                    </option>
                </select>
                @foreach($errors->get('type') as $err)
                    <div class="invalid-feedback">{{ $err }}</div>
                @endforeach
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Descripción <span style="color:var(--muted)">(opcional)</span></label>
                <input id="description" name="description" type="text"
                    class="form-control {{ $errors->get('description') ? 'is-invalid' : '' }}"
                    value="{{ old('description', $proxySchedule->description) }}"
                    placeholder="Ej: Jornada 28 — Real Madrid vs Barcelona" />
                @foreach($errors->get('description') as $err)
                    <div class="invalid-feedback">{{ $err }}</div>
                @endforeach
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Estado</label>
                <select id="status" name="status"
                    class="form-control {{ $errors->get('status') ? 'is-invalid' : '' }}">
                    <option value="pending"   {{ old('status', $proxySchedule->status) === 'pending'   ? 'selected' : '' }}>Pendiente</option>
                    <option value="active"    {{ old('status', $proxySchedule->status) === 'active'    ? 'selected' : '' }}>Activo</option>
                    <option value="completed" {{ old('status', $proxySchedule->status) === 'completed' ? 'selected' : '' }}>Completado</option>
                    <option value="failed"    {{ old('status', $proxySchedule->status) === 'failed'    ? 'selected' : '' }}>Fallido</option>
                </select>
                @foreach($errors->get('status') as $err)
                    <div class="invalid-feedback">{{ $err }}</div>
                @endforeach
            </div>

            <div style="height:1px;background:var(--border);margin:24px 0;"></div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="disable_at">🔴 Desactivar proxy</label>
                    <input id="disable_at" name="disable_at" type="datetime-local"
                        class="form-control {{ $errors->get('disable_at') ? 'is-invalid' : '' }}"
                        value="{{ old('disable_at', $proxySchedule->disable_at->format('Y-m-d\TH:i')) }}"
                        required />
                    @foreach($errors->get('disable_at') as $err)
                        <div class="invalid-feedback">{{ $err }}</div>
                    @endforeach
                </div>

                <div class="form-group">
                    <label class="form-label" for="enable_at">🟢 Reactivar proxy</label>
                    <input id="enable_at" name="enable_at" type="datetime-local"
                        class="form-control {{ $errors->get('enable_at') ? 'is-invalid' : '' }}"
                        value="{{ old('enable_at', $proxySchedule->enable_at->format('Y-m-d\TH:i')) }}"
                        required />
                    @foreach($errors->get('enable_at') as $err)
                        <div class="invalid-feedback">{{ $err }}</div>
                    @endforeach
                </div>
            </div>

            {{-- Sitios afectados (solo lectura) --}}
            <div style="height:1px;background:var(--border);margin:24px 0;"></div>

            <div class="form-group">
                <label class="form-label">Sitios afectados</label>
                <div class="flex gap-2" style="flex-wrap:wrap;margin-top:8px;">
                    @forelse($proxySchedule->sites as $site)
                        <span style="font-size:11px;padding:4px 10px;border-radius:4px;background:rgba(0,212,255,0.08);color:var(--cyan);border:1px solid rgba(0,212,255,0.15);">
                            {{ $site->domain }}
                        </span>
                    @empty
                        <span style="font-size:12px;color:var(--muted);">Sin sitios asociados</span>
                    @endforelse
                </div>
                <div style="font-size:10px;color:var(--muted);margin-top:8px;">
                    Los sitios se asignan automáticamente según el tipo al crear el schedule.
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">Actualizar schedule</button>
                <a href="{{ route('schedules.index') }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@endsection