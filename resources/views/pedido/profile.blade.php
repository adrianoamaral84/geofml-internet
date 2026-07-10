@extends('layouts.app')

@section('content')
<div class="title-block">
    <h3 class="title"> Profile </h3>
    <p class="title-description"> Meus dados pessoais e de acesso </p>
</div>
<section class="section">
    <div class="row sameheight-container">
        <div class="col-md-12">
            <div class="card card-block sameheight-item">
                <form id="profile-form" action="{{ route('editProfile') }}" method="POST">
                    @csrf
                    
                    <div class="row form-group has-error">
                        <div class="col-8">
                            <label class="control-label">{{ __('Nome *') }}</label>
                            <input type="text" class="form-control boxed @error('nome') is-invalid @enderror" value="{{ $usuario->name }}" name="nome" id="nome" autofocus required maxlength="100" onpaste="return false;">
                            @error('name')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    
 <input type="hidden" class="form-control boxed @error('id') is-invalid @enderror" value="{{ $id }}" name="id" id="id" autofocus required onpaste="return false;">
                 
                        <div class="col-4">
                            <label class="control-label">{{ __('Nome Guerra*') }}</label>
                            <input type="text" class="form-control boxed @error('nomeguerra') is-invalid @enderror" value="{{ $usuario->nomeguerra }}" name="nomeguerra" id="nomeguerra" autofocus required maxlength="50" onpaste="return false;">
                            @error('nomeguerra')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                        
                    
        
                   <div class="row form-group has-error">
                         <div class="col-8">
                            <label class="control-label">{{ __('Email *') }}</label>
                            <input type="email" class="form-control boxed @error('email') is-invalid @enderror" value="{{ $usuario->email }}" name="email" id="email" required maxlength="100" onpaste="return false;">
                            @error('email')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-4">
                            <label class="control-label">{{ __('Perfil *') }}</label>
                            <select name="perfil_id" id="perfil_id" required class="form-control boxed @error('perfil_id') is-invalid @enderror">
                                <option value="">Selecione Perfil</option>
                                @foreach($perfis as $perfil)

                                <option value="{{$perfil->id}}" 
                                @if($usuario->perfil->id == $perfil->id) selected 
                                @endif >{{$perfil->display_name}}</option>

                                
                                

                                @endforeach
                            </select>
                            @error('perfil_id')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        

                    </div>
                    <div class="row form-group has-error">
                       
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
                            <input type="text" class="form-control boxed @error('idtMil') is-invalid @enderror" value="{{ $usuario->idtMil }}" name="idtMil" id="idtMil" maxlength="15" data-mask="000000000-0" autocomplete="off" onpaste="return false;">
                            @error('idtMil')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row form-group has-error">

                        <div class="col-6">
                            <label class="control-label">{{ __('OM') }}</label>
                            <select name="om" id="om" required class="form-control boxed @error('om') is-invalid @enderror">
                            <option value="">Selecione OM</option>
                                @foreach($oms as $om)

                                 <option value="{{$om->id}}" 
                                @if($usuario->om->id == $om->id) selected 
                                @endif >{{$om->sigla}}</option>

                               
                                @endforeach
                            </select>

                            @error('om')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                         <div class="col-6">
                            <label class="control-label">{{ __('Posto / Graduação') }}</label>
                            <select name="posto" id="posto" required class="form-control boxed @error('posto') is-invalid @enderror">
                                <option value="">Selecione</option>
                                @foreach($postos as $posto)

                                  <option value="{{$posto->id}}" 
                                @if($usuario->posto->id == $posto->id) selected 
                                @endif >{{$posto->sigla}}</option>


                               
                                @endforeach
                            </select>
                            @error('posto')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

              

                    </div>

                     <div class="row form-group has-error">
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
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle fa-sm"></i>  Salvar Alterações </button>
                            <a href="{{ route('usuario.reset', ['id' => Crypt::encrypt($usuario->id)]) }}" class="btn btn-dark"><i class="fas fa-check-circle fa-sm"></i>  Resetar Senha! </a>
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