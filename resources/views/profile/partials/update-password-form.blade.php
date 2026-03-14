<section>
    <p class="text-muted" style="font-size:11px;margin-bottom:20px;">
        {{ __('Usa una contraseña larga y aleatoria para mantener tu cuenta segura.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="form-group">
            <label class="form-label" for="update_password_current_password">
                {{ __('Contraseña actual') }}
            </label>
            <input id="update_password_current_password" name="current_password" type="password"
                class="form-control {{ $errors->updatePassword->get('current_password') ? 'is-invalid' : '' }}"
                autocomplete="current-password" />
            @foreach($errors->updatePassword->get('current_password') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="form-group">
            <label class="form-label" for="update_password_password">
                {{ __('Nueva contraseña') }}
            </label>
            <input id="update_password_password" name="password" type="password"
                class="form-control {{ $errors->updatePassword->get('password') ? 'is-invalid' : '' }}"
                autocomplete="new-password" />
            @foreach($errors->updatePassword->get('password') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="form-group">
            <label class="form-label" for="update_password_password_confirmation">
                {{ __('Confirmar contraseña') }}
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="form-control {{ $errors->updatePassword->get('password_confirmation') ? 'is-invalid' : '' }}"
                autocomplete="new-password" />
            @foreach($errors->updatePassword->get('password_confirmation') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
            @if (session('status') === 'password-updated')
                <span style="font-size:11px;color:var(--green);">✓ {{ __('Contraseña actualizada.') }}</span>
            @endif
        </div>
    </form>
</section>