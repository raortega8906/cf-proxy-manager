<section>
    <p class="text-muted" style="font-size:11px;margin-bottom:20px;">
        {{ __("Actualiza el nombre y email de tu cuenta.") }}
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="max-w-[50%]">
        @csrf
        @method('patch')

        <div class="form-group">
            <label class="form-label" for="name">{{ __('Nombre') }}</label>
            <input id="name" name="name" type="text"
                class="logo-sub form-control {{ $errors->get('name') ? 'is-invalid' : '' }}"
                value="{{ old('name', $user->name) }}"
                required autofocus autocomplete="name" />
            @foreach($errors->get('name') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach
        </div>

        <div class="form-group">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email"
                class="logo-sub form-control {{ $errors->get('email') ? 'is-invalid' : '' }}"
                value="{{ old('email', $user->email) }}"
                required autocomplete="username" />
            @foreach($errors->get('email') as $err)
                <div class="invalid-feedback">{{ $err }}</div>
            @endforeach

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top:10px;">
                    <p style="font-size:11px;color:var(--yellow);">
                        {{ __('Tu email no está verificado.') }}
                        <button form="send-verification"
                            style="background:none;border:none;cursor:pointer;color:var(--cyan);font-size:11px;font-family:inherit;text-decoration:underline;">
                            {{ __('Reenviar verificación.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p style="margin-top:6px;font-size:11px;color:var(--green);">
                            {{ __('Enlace de verificación enviado.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
            @if (session('status') === 'profile-updated')
                <span style="font-size:11px;color:var(--green);">✓ {{ __('Guardado.') }}</span>
            @endif
        </div>
    </form>
</section>