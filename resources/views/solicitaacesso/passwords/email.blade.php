@extends('layouts.auth')

@section('content')
<p class="text-center">{{ __('RECUPERAR SENHA') }}</p>
<p class="text-muted text-center"><small>Digite seu endereço de e-mail para recuperar sua senha.</small></p>
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="form-group">
        <label for="email1">Email</label>
        <input type="email" class="form-control underlined @error('email') is-invalid @enderror" name="email" id="email" placeholder="Seu e-mail" required autofocus>

        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-block btn-primary">{{ __('Enviar o link de redefinição de senha') }}</button>
    </div>
    <div class="form-group clearfix">
        <a class="pull-left" href="{{ route('login') }}"><small>Voltar para tela de Login</small></a>
    </div>
</form>
@endsection
