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

   

    <div class="form-group">
        <label for="password">{{ __('Senha') }}</label>
            <input id="password" type="password" class="form-control underlined @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
    </div>

    <div class="form-group ">
        <label for="password-confirm">{{ __('Confirme a Senha') }}</label>

        
            <input id="password-confirm" type="password" class="form-control underlined" name="password_confirmation" required autocomplete="new-password">
       
    </div>



    <div class="form-group text-center">

       
            <button type="submit" class="btn btn-primary">
                {{ __('Redefinir Senha') }}
            </button>
        

    </div>
</form>
@endsection