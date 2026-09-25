@extends('layouts.app')
@section('title', 'Alterar Senha')

@section('content')
<div class="title-block">
    <h3 class="title"> Alterar Minha Senha </h3>
</div>
<section class="section">
    <div class="row sameheight-container">
        <div class="col-md-12">
            <div class="card card-block sameheight-item">
                <form id="password-form" action="{{ route('editarSenha') }}" method="POST">
                    @csrf
                    <div class="row form-group has-error">
                        <div class="col-4">
                            <label class="control-label">{{ __('Senha Atual') }}</label>
                            <input type="password" class="form-control boxed @error('senhaAtual') is-invalid @enderror" name="senhaAtual" id="senhaAtual" autofocus required maxlength="15" minlength="6">
                            @error('senhaAtual')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="alert alert-info" role="alert" style="font-size: 13px;">
                                <strong>Requisitos da nova senha:</strong><br>
                                Mínimo de 8 caracteres, contendo pelo menos 1 letra maiúscula, 1 letra minúscula, 1 número e 1 símbolo.
                            </div>
                        </div>
                    </div>

                    <div class="row form-group has-error">
                        <div class="col-4">
                            <label class="control-label">{{ __('Nova Senha') }}</label>
                            <input type="password" class="form-control boxed @error('novaSenha') is-invalid @enderror" name="novaSenha" id="novaSenha" required maxlength="15" minlength="8" aria-describedby="novaSenhaHelp">
                            <small id="novaSenhaHelp" class="form-text text-muted">Use letras maiúsculas e minúsculas, número e símbolo.</small>
                            @error('novaSenha')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row form-group has-error">
                        <div class="col-4">
                            <label class="control-label">{{ __('Confirmação da Nova Senha') }}</label>
                            <input type="password" class="form-control boxed @error('novaSenha_confirmation') is-invalid @enderror" name="novaSenha_confirmation" id="novaSenha_confirmation" required maxlength="15" minlength="8">
                            @error('novaSenha_confirmation')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle fa-sm"></i>  Salvar Alterações </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection