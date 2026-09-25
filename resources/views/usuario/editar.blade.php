@extends('layouts.app')

@section('content')
<div class="title-block">
    <h3 class="title"> Editar Usuário </h3>
</div>
<section class="section">
    <div class="row sameheight-container">
        <div class="col-md-12">
            <div class="card card-block sameheight-item">
                <p class="title-description"> * Campo obrigatório </p><br>
                <form id="profile-form" action="{{ route('editUsuario') }}" method="POST">
                    @csrf
                    <input type="hidden" name="usuario_id" id="usuario_id" value="{{$usuario->id}}">
                    <div class="row form-group has-error">
                        <div class="col-8">
                            <label class="control-label">{{ __('Nome *') }}</label>
                            <input type="text" class="form-control boxed @error('nome') is-invalid @enderror" value="{{ $usuario->name }}" name="nome" id="nome" autofocus required maxlength="100" onpaste="return false;">
                            @error('nome')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-4">
                            <label class="control-label">{{ __('Perfil *') }}</label>
                            <input type="text" name="perfil_id" id="perfil_id" readonly disabled class="form-control boxed @error('perfil_id') is-invalid @enderror" value="{{$usuario->perfil->descricao}}">
                            @error('perfil_id')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row form-group has-error">
                        <div class="col-12">
                            <label class="control-label">{{ __('Email *') }}</label>
                            <input type="email" class="form-control boxed @error('email') is-invalid @enderror" value="{{ $usuario->email }}" name="email" id="email" required maxlength="100" onpaste="return false;">
                            @error('email')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row form-group has-error">
                        <div class="col-6">
                            <label class="control-label">{{ __('CPF *') }}</label>
                            <input type="text" class="form-control boxed @error('cpf') is-invalid @enderror" value="{{ $usuario->cpf }}" name="cpf" id="cpf" required maxlength="11" data-mask="000.000.000-00" autocomplete="off" onpaste="return false;">
                            @error('cpf')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="control-label">{{ __('Identidade Militar *') }}</label>
                            <input type="text" class="form-control boxed @error('idtMil') is-invalid @enderror" value="{{ $usuario->idtMil }}" name="idtMil" id="idtMil" required maxlength="15" data-mask="000000000-0" autocomplete="off" onpaste="return false;">
                            @error('idtMil')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row form-group has-error">
                        <div class="col-4">
                            <label class="control-label">{{ __('OM') }}</label>
                            <input type="text" class="form-control boxed @error('om') is-invalid @enderror" value="{{ $usuario->om }}" name="om" id="om" maxlength="100" onpaste="return false;">
                            @error('om')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-4">
                            <label class="control-label">{{ __('Seção') }}</label>
                            <input type="text" class="form-control boxed @error('secao') is-invalid @enderror" value="{{ $usuario->secao }}" name="secao" id="secao" maxlength="45" onpaste="return false;">
                            @error('secao')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-4">
                            <label class="control-label">{{ __('Telefone Contato') }}</label>
                            <input type="text" class="form-control boxed @error('telefone') is-invalid @enderror" value="{{ $usuario->telefone }}" name="telefone" id="telefone" maxlength="45" onpaste="return false;" data-mask="(00) 00000-0000">
                            @error('telefone')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row form-group has-error">
                        <div class="col-md-8">
                            <label class="control-label">{{ __('Processos Associados') }}</label>
                            <p class="title-description"> Mantenha a tecla Ctrl pressionada para selecionar mais de um processo </a>
                            <select class="form-control boxed @error('processo_id') is-invalid @enderror" multiple="multiple" required name="processo_id[]" id="processo_id">
                                @foreach($processos as $processo)
                                    @if ($processosUsuario->where('id', $processo->id)->count() > 0)
                                    <option value="{{ $processo->id }}" selected>{{$processo->categoria->sigla}} | {{$processo->descricao}}</option>
                                    @else
                                    <option value="{{ $processo->id }}">{{$processo->categoria->sigla}} | {{$processo->descricao}}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('processo_id')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-10 col-sm-offset-2">
                            <p class="title-description"> * A senha inicial do usuário será o CPF do mesmo, somente os números </p><br>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle fa-sm"></i>  Salvar Alterações </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@push('javascript')
    <script src="{{asset('lib/jquery-mask-plugin/dist/jquery.mask.min.js')}}"></script>
@endpush
@endsection