<section>
    <p class="text-muted" style="font-size:11px;margin-bottom:20px;">
        {{ __('Una vez eliminada, toda la información será borrada permanentemente.') }}
    </p>

    <button class="btn btn-danger" onclick="document.getElementById('delete-modal-overlay').style.display='flex'">
        🗑 {{ __('Eliminar cuenta') }}
    </button>
</section>

{{-- MODAL --}}
<div id="delete-modal-overlay"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:999;align-items:center;justify-content:center;">
    <div style="background:var(--bg3);border:1px solid var(--border);border-radius:12px;padding:32px;width:100%;max-width:440px;">

        <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:16px;color:var(--white);margin-bottom:10px;">
            ¿Eliminar cuenta?
        </div>
        <p style="font-size:11px;color:var(--muted2);margin-bottom:24px;line-height:1.6;">
            {{ __('Esta acción es irreversible. Introduce tu contraseña para confirmar.') }}
        </p>

        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="form-group">
                <label class="form-label" for="delete_password">{{ __('Contraseña') }}</label>
                <input id="delete_password" name="password" type="password"
                    class="form-control {{ $errors->userDeletion->get('password') ? 'is-invalid' : '' }}"
                    placeholder="{{ __('Tu contraseña actual') }}" />
                @foreach($errors->userDeletion->get('password') as $err)
                    <div class="invalid-feedback">{{ $err }}</div>
                @endforeach
            </div>

            <div class="flex gap-2 mt-4" style="justify-content:flex-end;">
                <button type="button" class="btn btn-ghost"
                    onclick="document.getElementById('delete-modal-overlay').style.display='none'">
                    {{ __('Cancelar') }}
                </button>
                <button type="submit" class="btn btn-danger">
                    🗑 {{ __('Eliminar definitivamente') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Reabrir modal si hay errores de validación --}}
@if($errors->userDeletion->isNotEmpty())
<script>
    document.getElementById('delete-modal-overlay').style.display = 'flex';
</script>
@endif