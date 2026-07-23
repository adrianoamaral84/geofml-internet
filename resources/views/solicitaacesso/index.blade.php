@extends('layouts.solicita')
@section('title', 'Solicitar Acesso ao Sistema')
@section('content')
<p class="text-center">Solicitar Acesso ao Sistema</p>
<form id="solicita-form" action="{{ route('pedido.acesso') }}" method="POST">
    @csrf



    <div class="row">
    <div class="form-group col-sm-12 col-md-12 col-lg-12">
    
        <label for="username">{{ __('Nome Completo') }}</label>
        <input type="nome" class="form-control underlined @error('nome') is-invalid @enderror" value="{{ old('nome') }}" name="nome" id="nome" placeholder="Nome Completo" autofocus required>

        @error('nome')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    </div>

    <div class="row">
    <div class="form-group col-sm-6 col-md-6 col-lg-6">
        <label for="cpf">{{ __('CPF') }}</label>
        <input type="cpf" class="form-control underlined @error('cpf') is-invalid @enderror" name="cpf" id="cpf" placeholder="Seu CPF" required maxlength="11" data-mask="000.000.000-00" autocomplete="off" onpaste="return false;" value="{{ old('cpf') }}">

        @error('cpf')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group col-sm-6 col-md-6 col-lg-6">
        <label for="username">{{ __('Celular') }} <small>whatsapp</small></label>
        <input type="celular" class="form-control underlined @error('celular') is-invalid @enderror" value="{{ old('celular') }}" name="celular" id="celular" placeholder="00 00000-0000" data-mask="(00) 00000-0000" maxlength="11" autofocus required>

        @error('celular')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    </div>

   
    <div class="row">
    <div class="form-group col-sm-12 col-md-12 col-lg-12">
    
        <label for="username">{{ __('E-mail') }}</label>
        <input type="email" class="form-control underlined @error('email') is-invalid @enderror" value="{{ old('email') }}" name="email" id="email" placeholder="Seu e-mail" autofocus required>

        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    </div>


   
    <div class="form-group">
        <button type="submit" class="btn btn-block btn-primary">{{ __('Solicitar Acesso') }}</button>
         <a href="{{ route('login') }}" class="forgot-btn pull-left"><small>Voltar</small></a>
    </div>    
   

    
</form>



@push('javascript')
<script src="{{ asset('js/jquery.mask.min.js') }}"></script>
@endpush

@endsection