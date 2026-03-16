@extends('layouts.app')

@section('page-title', 'Nuevo Schedule')
@section('page-sub', 'Programa una activación/desactivación automática del proxy')

@section('content')

<div class="flex mb-4">
    <a href="{{ route('schedules.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
</div>

<div style="max-width:560px;">
    <div class="card">
        <div class="card-title">Información del schedule</div>

        <form method="POST" action="{{ route('schedules.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="type">Tipo</label>
                <select id="type" name="type"
                    class="form-control {{ $errors->get('type') ? 'is-invalid' : '' }}">
                    <option value="">— Selecciona un tipo —</option>
                    <option value="laliga_match" {{ old('type') === 'laliga_match' ? 'selected' : '' }}>
                        ⚽ Partido LaLiga
                    </option>
                    <option value="ssl_renewal" {{ old('type') === 'ssl_renewal' ? 'selected' : '' }}>
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
                    value="{{ old('description') }}"
                    placeholder="Ej: Jornada 28 — Real Madrid vs Barcelona" />
                @foreach($errors->get('description') as $err)
                    <div class="invalid-feedback">{{ $err }}</div>
                @endforeach
            </div>

            <div style="height:1px;background:var(--border);margin:24px 0;"></div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="disable_at">🔴 Desactivar proxy</label>
                    <input id="disable_at" name="disable_at" type="datetime-local"
                        class="form-control {{ $errors->get('disable_at') ? 'is-invalid' : '' }}"
                        value="{{ old('disable_at') }}" required />
                    @foreach($errors->get('disable_at') as $err)
                        <div class="invalid-feedback">{{ $err }}</div>
                    @endforeach
                    <div style="font-size:10px;color:var(--muted);margin-top:6px;">
                        El proxy se apagará en esta fecha y hora.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="enable_at">🟢 Reactivar proxy</label>
                    <input id="enable_at" name="enable_at" type="datetime-local"
                        class="form-control {{ $errors->get('enable_at') ? 'is-invalid' : '' }}"
                        value="{{ old('enable_at') }}" required />
                    @foreach($errors->get('enable_at') as $err)
                        <div class="invalid-feedback">{{ $err }}</div>
                    @endforeach
                    <div style="font-size:10px;color:var(--muted);margin-top:6px;">
                        El proxy volverá a activarse en esta hora.
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">Crear schedule</button>
                <a href="{{ route('schedules.index') }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@endsection