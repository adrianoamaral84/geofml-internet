@extends('layouts.reset')

@section('content')
<p class="text-center">{{ __('Redefinir Senha') }}</p>
<p class="text-muted text-center"><small>Informe uma nova senha.</small></p>
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label class="control-label">{{ __('Email') }}</label>
       
            <input id="email" type="email" class="form-control underlined @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
       
        @error('cpf')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="alert alert-info" role="alert" style="font-size: 13px;">
        <strong>Requisitos da senha:</strong><br>
        Mínimo de 8 caracteres, contendo pelo menos 1 letra maiúscula, 1 letra minúscula, 1 número e 1 símbolo.
    </div>

    <div class="form-group">
        <label for="password">{{ __('Nova Senha') }}</label>
            <input id="password" type="password" class="form-control underlined @error('password') is-invalid @enderror" name="password" required minlength="8" maxlength="64" autocomplete="new-password" aria-describedby="passwordHelp">
            <small id="passwordHelp" class="form-text text-muted">Exemplo de formato válido: uma combinação com letras maiúsculas e minúsculas, número e símbolo.</small>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
    </div>

    <div class="form-group ">
        <label for="password-confirm">{{ __('Confirme a Nova Senha') }}</label>

        
            <input id="password-confirm" type="password" class="form-control underlined" name="password_confirmation" required minlength="8" maxlength="64" autocomplete="new-password">
       
    </div>



    <div class="form-group text-center">

       
            <button type="submit" class="btn btn-primary">
                {{ __('Redefinir Senha') }}
            </button>
        

    </div>
</form>
@endsection