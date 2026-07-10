@extends('layouts.novoacesso')

@section('content')
<div class="title-block">
    <h3 class="title"> Novo Cadastro </h3>
    <p class="title-description"> Realize o seu cadastro para acesso ao sistema! </p>
</div>
<section class="section">
    <div class="row sameheight-container">
        <div class="col-md-12">

            <div class="card card-block sameheight-item">
                <p class="title-description"> * Campo obrigatório </p><br>
                <form id="profile-form" action="{{ route('usuario.create.new') }}" method="POST">
                    @csrf
                    <div class="row form-group has-error">
                        <div class="col-12">
                            <label class="control-label">{{ __('Nome *') }}</label>
                            <input type="text" class="form-control boxed @error('nome') is-invalid @enderror" value="{{ $user->name }}" name="nome" id="nome" autofocus required maxlength="100" onpaste="return false;">

                            
                            @error('nome')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
              
                    </div>


                    <div class="row form-group has-error">
                         <div class="col-12">
                            <label class="control-label">{{ __('Email *') }}</label>
                            <input type="email" class="form-control boxed @error('email') is-invalid @enderror" value="{{ $user->email }}" name="email" id="email" required maxlength="100" onpaste="return false;">
                            @error('email')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        

                    </div>
                   
                    <div class="row form-group has-error">
                        <div class="col-4">
                            <label class="control-label">{{ __('CPF *') }}</label>
                            <input type="text" class="form-control boxed @error('cpf') is-invalid @enderror" value="{{ $user->cpf }}" name="cpf" id="cpf" required autofocus maxlength="11" data-mask="000.000.000-00" onpaste="return false;">
                            @error('cpf')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-4">
                            <label class="control-label">{{ __('Identidade *') }}</label>
                            <input type="text" class="form-control boxed @error('idtMil') is-invalid @enderror" value="{{ old('idtMil') }}" name="idtMil" id="idtMil" maxlength="9" data-mask="0000000-0" required="required" autocomplete="off" onpaste="return false;">
                            @error('idtMil')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-4">
                            <label class="control-label">{{ __('Celular Contato *') }}</label>
                            <input type="text" class="form-control boxed @error('telefone') is-invalid @enderror" value="{{ old('telefone') }}" name="telefone" id="telefone" maxlength="11" onpaste="return false;" required="required" data-mask="(00) 00000-0000">
                            @error('telefone')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row form-group has-error">
                         <div class="col-8">
                            <label class="control-label">{{ __('Endereço *') }}</label>
                            <input type="text" class="form-control boxed @error('endereco') is-invalid @enderror" value="{{ old('endereco') }}" name="endereco" id="endereco" required maxlength="100" onpaste="return false;">
                            @error('endereco')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-4">
                            <label class="control-label">{{ __('CEP *') }}</label>
                            <input type="text" class="form-control boxed @error('cep') is-invalid @enderror" value="{{ old('cep') }}" name="cep" id="cep" required maxlength="8" data-mask="00000-000" onpaste="return false;">
                            @error('cep')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                     <div class="row form-group has-error">


                             <div class="col-4">
                                <label class="control-label">{{ __('UF') }}</label>
                                <select name="uf" id="uf" required class="form-control boxed @error('uf') is-invalid @enderror">
                                <option value="">Selecione UF</option>
                                    @foreach($ufs as $uf)
                                    <option value="{{$uf->id}}">{{$uf->sigla}}</option>
                                    @endforeach
                                </select>

                                @error('uf')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                              <div class="col-8">
                                <label class="control-label">{{ __('Cidade') }}</label>
                                <select name="cidade" id="cidade" required="required" class="form-control boxed @error('cidade') is-invalid @enderror">
                                <option value="">Selecione Cidade</option>
                                    @foreach($cidades as $cidade)
                                    <option value="{{$cidade->id}}">{{$cidade->descricao}}</option>
                                    @endforeach
                                </select>

                                @error('cidade')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                    </div>

                    <div class="row form-group has-error">
                        <div class="col-12">
                            <label class="control-label">{{ __('Situação') }}</label>
                            <select name="situacao" id="situacao" required class="form-control boxed @error('situacao') is-invalid @enderror">
                            <option value="">Selecione Situação</option>
                                
                               @foreach($situacoes as $situacao)
                                    <option value="{{$situacao->id}}">{{$situacao->situacao}}</option>
                                    @endforeach
                                
                            </select>

                            @error('situacao')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row form-group has-error milReserva">
                            <div class="col-12">
                                <label class="form-check">
                                    <input type="checkbox" id="pttc" name="pttc" value="1" 
                                                        class="form-check-input" required="no">
                                                        PTTC
                                </label>
                                    @error('pttc')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div>
                    </div>
                   

                   <div class="row form-group has-error militarAtiva">
                        
                        <div class="col-8">
                            <label class="control-label">{{ __('Nome de Guerra') }}</label>
                            <input type="text" class="form-control boxed @error('nomeguerra') is-invalid @enderror" value="{{ old('nomeguerra') }}" name="nomeguerra" id="nomeguerra" autofocus maxlength="50" onpaste="return false;">
                            @error('nomeguerra')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-4">
                            <label class="control-label">{{ __('Data Última Promoção') }}</label>
                            <input type="date" class="form-control boxed @error('dtUltPromo') is-invalid @enderror" value="{{ old('dtUltPromo') }}" name="dtUltPromo" id="dtUltPromo" autofocus onpaste="return false;" placeholder="dd-mm-yyyy">
                            @error('dtUltPromo')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                </div>
           


                <div class="row form-group has-error ForcaOmPosto">
                        <div class="col-4">
                            <label class="control-label">{{ __('Força') }}</label>
                            <select name="forca" id="forca" required class="form-control boxed @error('forca') is-invalid @enderror">
                            <option value="">Selecione Força</option>
                                @foreach($forcas as $forca)
                                <option value="{{$forca->id}}">{{$forca->forca}}</option>
                                @endforeach
                            </select>

                            @error('om')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-4">
                                <label class="control-label">{{ __('OM') }}</label>
                                <select name="om" id="om" required class="form-control boxed @error('om') is-invalid @enderror">
                                <option value="">Selecione OM</option>
                                    @foreach($oms as $om)
                                    <option value="{{$om->id}}">{{$om->sigla}}</option>
                                    @endforeach
                                </select>

                                @error('om')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        <div class="col-4">
                            <label class="control-label">{{ __('Posto / Graduação') }}</label>
                            <select name="posto" id="posto" required class="form-control boxed @error('posto') is-invalid @enderror">
                                <option value="">Selecione</option>
                                @foreach($postos as $posto)
                                <option value="{{$posto->id}}">{{$posto->sigla}}</option>
                                @endforeach
                            </select>
                            @error('posto')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                </div>
           

                <div class="row form-group has-error siape">
                        <div class="col-4">
                            <label class="control-label">{{ __('Siape') }}</label>
                           <input type="text" class="form-control boxed @error('siape') is-invalid @enderror" value="{{ old('siape') }}" name="siape" id="siape" autofocus maxlength="14" onpaste="return false;">

                            @error('siape')
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
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle fa-sm"></i>  
                                    CADASTRAR
                            </button>
                        </div>
                    </div>


                </form>
                <p></p>
                <p></p>
                <p></p>
                <p></p>
                
                <p></p>
                <p></p>

                <p></p>
                <p></p>
                <p></p>

                <p>&nbsp;</p>
            </div>
        </div>
    </div>
</section>


@push('javascript')
    <script src="{{asset('lib/jquery-mask-plugin/dist/jquery.mask.min.js')}}"></script>
    <script src="{{ asset('js/jquery.min.js') }}" ></script>   
    <script src="{{ asset('js/script_cadastro.js') }}" ></script>   
    
@endpush
@endsection