@extends('layouts.precadastro')
@section('title', 'Pre Cadastro')
@section('content')
<div class="title-block">
    <h3 class="title"> Cadastro </h3>
    <p class="title-description"> Realize o seu cadastro para acesso ao sistema! <br>
    <br>
    <b>Aviso:</b>                       
    <font style="color:red"> Para Militares da Força Aérea e Marinha selecione Estado PR, Cidade Curitiba e em OM selecione sua Força
    </font> 
                 
    </p>
</div>
<section class="section">
    <div class="row sameheight-container">
        <div class="col-12">

            <div class="card card-block sameheight-item">
                <p class="title-description">  </p><br>
                <form id="profile-form" action="{{ route('usuario.create.new') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row has-error">
                        <div class="form-group col-sm-8 col-md-8 col-lg-8">
                            <label class="control-label">{{ __('Nome') }}</label>
                            <input type="text" class="form-control boxed @error('nome') is-invalid @enderror" value="{{ $user->name }}" name="nome" id="nome" autofocus required maxlength="100" onpaste="return false;" style="text-transform: uppercase;">

                            
                            @error('nome')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                            <label class="control-label">{{ __('CPF') }}</label>
                            <input type="text" class="form-control boxed @error('cpf') is-invalid @enderror" value="{{ $user->cpf }}" name="cpf" id="cpf" required readonly="" autofocus maxlength="11" data-mask="000.000.000-00" onpaste="return false;">
                            @error('cpf')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
              
                    </div>


                    <div class="row has-error">
                        <div class="form-group col-sm-8 col-md-8 col-lg-8">
                            <label class="control-label">{{ __('Situação') }}</label>
                
                                <select name="situacao" id="situacao" name="situacao" required class="custom-select mr-sm-2 @error('situacao') is-invalid @enderror" autocomplete="off" @if($user->situacao_id == 2) readonly="readonly" tabindex="-1" aria-disabled="true" style="touch-action: none; pointer-events: none;" @endif>
                                     <option value="">Selecione Situação</option>
                                
                                    @foreach($situacoes as $situacao)

                                         <option value="{{$situacao->id}}" @if($user->situacao_id == $situacao->id)selected @endif>{{$situacao->situacao}}</option>
                                    @endforeach
                            </select>
                            @error('situacao')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
    <label class="control-label">Mecenas</label>

    <select name="mecenas" class="form-control">
        <option value="0" @if(old('mecenas', $user->mecenas ?? 0) == 0) selected @endif>Não</option>
        <option value="1" @if(old('mecenas', $user->mecenas ?? 0) == 1) selected @endif>Sim</option>
    </select>
</div>
                    </div>
                   
               
                    <div class="row has-error milReserva">
                            <div class="form-group col-sm-12 col-md-12 col-lg-12">
                               
                            <label class="form-check">
                            @if($user->pttc === 1)
                            <input type="checkbox" id="pttc" name="pttc" value="1" checked="" class="form-check-input" required="no">
                            @else
                            <input type="checkbox" id="pttc" name="pttc" value="1" class="form-check-input" required="no"> 
                            @endif
                            PTTC 
                            </label>
                                    @error('pttc')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div>
                    </div>      
                  
                

               

                    <div class="row has-error">


                         <div class="col-sm-4 col-md-4 col-lg-4 form-group ForcaOmPosto">

    <input
        type="hidden"
        id="posto_selecionado"
        value="{{ old('posto', $user->postograd_id) }}"
    >

    <label class="control-label" id="nivel">
        {{ __('Posto / Graduação') }}
    </label>

    <select
        name="posto"
        id="posto"
        class="custom-select mr-sm-2 @error('posto') is-invalid @enderror"
        autocomplete="off"
        @if($user->situacao_id == 2)
            aria-disabled="true"
            tabindex="-1"
            style="pointer-events: none;"
        @endif
    >
        <option value="">Selecione</option>
    </select>

    @error('posto')
        <span class="has-error" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror

</div>



                              <div class="col-sm-4 col-md-4 col-lg-4 form-group militarAtiva">
                            <label class="control-label">{{ __('Data Última Promoção') }}</label>
                            <input type="date" class="form-control boxed @error('dtUltPromo') is-invalid @enderror" value="{{$user->dtUltPromo}}" name="dtUltPromo" id="dtUltPromo" max='{{ $hoje }}' autofocus onpaste="return false;" placeholder="dd-mm-yyyy">
                            @error('dtUltPromo')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2 form-group identidade">
                            <label class="control-label" id="texto"> {{ __('Identidade Militar') }}</label>
                            <input type="text" class="form-control boxed @error('idtMil') is-invalid @enderror" value="@if($user->idtMil){{$user->idtMil}}@endif" name="idtMil" id="idtMil" maxlength="14" required="required" autocomplete="off" onpaste="return false;">
                            @error('idtMil')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2 form-group identidade">
                            <label class="control-label" id="texto"> {{ __('Validade') }}</label> <div style="float: right;"><small>
                        @if($user->indeterminado == 1 )
                        <input type="checkbox" name="indeterminado" id="indeterminado" value="1"  checked="" class="form-check-input"> <label for="indeterminado">
                            Indeterminada
                        @else
                        <input type="checkbox" name="indeterminado" id="indeterminado" value="1"> <label for="indeterminado">
                            Indeterminada
                        @endif
                        
                        </label> </small></div>

                            <input type="date" class="form-control boxed @error('validade') is-invalid @enderror" value="{{$user->validade}}" name="validade" id="validade" placeholder="dd-mm-yyyy" min="{{$min}}" required="required" onpaste="return false;" @if($user->indeterminado == 1) readonly="readonly" @endif>
                            @error('validade')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                  

                        

                        <div class="col-sm-4 col-md-4 col-lg-4 form-group nivelescola">
                            <label class="control-label">{{ __('Nivel') }}</label>
                
                                <select name="nivel" id="nivel" class="custom-select mr-sm-2 @error('nivel') is-invalid @enderror" autocomplete="off">
                                     <option value="">Nivel </option>
                                
                                    @foreach($nivels as $nivel)

                                         <option value="{{$nivel->id}}" @if($user->nivel == $nivel->id)selected @endif>{{$nivel->nivel}}</option>
                                    @endforeach

                                         
                                    
                            </select>
                            @error('nivel')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                      
                        <div class="col-sm-4 col-md-4 col-lg-4 form-group siape">
                            <label class="control-label">{{ __('Siape') }}</label>
                                <input type="text" class="form-control boxed @error('siape') is-invalid @enderror" value="{{ ($user->siape) ? $user->siape : old('siape') }}" name="siape" id="siape" autofocus maxlength="14" onpaste="return false;" @if($user->siape) readonly @endif>

                            @error('siape')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                </div>
  
                                    <div class="row has-error">

    {{-- UF --}}
    <div class="col-sm-4 col-md-4 col-lg-4 form-group">
        <label class="control-label">UF</label>

        <select
            name="uf"
            id="uf"
            required
            class="custom-select mr-sm-2 @error('uf') is-invalid @enderror"
            autocomplete="off"
        >
            <option value="">Selecione UF</option>

            @foreach($ufs as $uf)
                <option
                    value="{{ $uf->id }}"
                    {{ old('uf', $user->uf_id) == $uf->id ? 'selected' : '' }}
                >
                    {{ $uf->sigla }}
                </option>
            @endforeach
        </select>

        @error('uf')
            <span class="has-error" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>


    {{-- Cidade --}}
    <div class="col-sm-4 col-md-4 col-lg-4 form-group">

        <input
            type="hidden"
            id="cidade_selecionada"
            value="{{ old('cidade', $user->cidade_id) }}"
        >

        <label class="control-label">Cidade</label>

        <select
            name="cidade"
            id="cidade"
            required
            class="custom-select mr-sm-2 @error('cidade') is-invalid @enderror"
            autocomplete="off"
        >
            <option value="">Selecione Cidade</option>
        </select>

        @error('cidade')
            <span class="has-error" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>


    {{-- OM --}}
    <div class="col-sm-4 col-md-4 col-lg-4 form-group">

        <input
            type="hidden"
            id="om_selecionada"
            value="{{ old('om', $user->om_id) }}"
        >

        <label class="control-label">OM</label>

        <select
            name="om"
            id="om"
            required
            class="custom-select mr-sm-2 @error('om') is-invalid @enderror"
            autocomplete="off"
        >
            <option value="">Selecione OM</option>
        </select>

        @error('om')
            <span class="has-error" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

</div>

                 <div class="row has-error">
                         <div class="col-sm-6 col-md-8 col-lg-8 form-group">
                            <label class="control-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control boxed @error('email') is-invalid @enderror" value="{{ $user->email }}" name="email" id="email" required maxlength="100" onpaste="return false;">
                            @error('email')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="col-sm-6 col-md-4 col-lg-4 form-group">
                            <label class="control-label">{{ __('Telefone C/ WhatsApp') }}</label>
                            <input type="text" class="form-control boxed @error('telefone') is-invalid @enderror" 
                            value="@if($user->telefone){{$user->telefone}}@endif" name="telefone" id="telefone" maxlength="11" onpaste="return false;" required="required" data-mask="(00) 00000-0000" autocomplete="off">
                            @error('telefone')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                </div>

                 <div class="row has-error">
                         
                         <div class="col-sm-6 col-md-6 col-lg-6 form-group">
                            <label class="control-label">{{ __('Senha') }}</label><small> ( min. 6 caracteres max 15)</small>
                            <input type="password" class="form-control boxed @error('password') is-invalid @enderror" value="" name="password" id="password" required maxlength="15" minlength="6" onpaste="return false;">
                            @error('password')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="col-sm-6 col-md-6 col-lg-6 form-group">
                            <label class="control-label">{{ __('Confirmar Senha') }}</label><small> ( min. 6 caracteres max 15)</small>
                            <input type="password" class="form-control boxed @error('resenha') is-invalid @enderror" 
                            value="" name="resenha" id="resenha" maxlength="15" minlength="6" onpaste="return false;" required="required">
                            @error('resenha')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                </div>

 

            
             
                       <div class="row has-error">
                        <div class="col-sm-12 col-md-12 col-lg-12 form-group">
                         <label class="control-label">{{ __('Documento de Identidade Militar') }}</label>
                         
                     </div>
                     </div>

                      <div class="row has-error">
                          <div class="col-sm-12 col-md-12 col-lg-12 form-group">
                            @if($docFrente)

<a href="{{ route('documentos.verdocumento', [
    'id' => Crypt::encrypt($user->id),
    'doc' => Crypt::encrypt($docFrente->mime),
    'tipo' => '1'
]) }}" target="_blank" class="btn btn-secondary btn-xl rounded-s">
    <i class="fas fa-address-card"></i>
    Frente
</a>

@endif


@if($docVerso)

<a href="{{ route('documentos.verdocumento', [
    'id' => Crypt::encrypt($user->id),
    'doc' => Crypt::encrypt($docVerso->mime),
    'tipo' => '2'
]) }}" target="_blank" class="btn btn-secondary btn-xl rounded-s">
    <i class="fas fa-address-card"></i>
    Verso
</a>

@endif
                        </div>
                          

                    </div>


                    <div class="row form-group has-error">
                       
                          <div class="col-sm-12 col-md-12 col-lg-12 form-group">
                            <label class="control-label">{{ __('Anexar Documento de Identidade Militar Frente') }}</label>

                             
                        <div class="custom-file">
                         <input type="file" class="custom-file-input" name="documento" id="documento" accept=".jpg,.png,.pdf" @if(!$user->documento) required="" @endif name="documento">
                            <label class="custom-file-label" for="customFile">Escolha o arquivo</label>
                        </div>
                        <p style="color: red;"><b>Imagem da Identidade no formato .JPG, .PNG, .PDF até 4MB ou PRINT aplicativo EBCIM</b></p>
                        @error('documento')
                        <span class="has-error" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                       </div>

                              <div class="col-sm-12 col-md-12 col-lg-12 form-group">
                            <label class="control-label">{{ __('Anexar Documento de Identidade Militar Verso') }}</label>
                             
                        <div class="custom-file">
                         <input type="file" class="custom-file-input" name="documento_verso" id="documento_verso" accept=".jpg,.png,.pdf" @if(!$user->documento_verso) required="" @endif name="documento">
                            <label class="custom-file-label" for="customFile">Escolha o arquivo</label>
                        </div>
                        <p style="color: red;"><b>Imagem da Identidade no formato .JPG, .PNG, .PDF até 4MB ou PRINT aplicativo EBCIM</b></p>
                        @error('documento_verso')
                        <span class="has-error" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                       </div>
                    </div>              
                    

                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-12 col-xl-12">
                            <p class="title-description"> 

                             </p><br>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle fa-sm"></i>  
                                    Enviar
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
                <p></p>

            </div>
        </div>
    </div>
</section>


@push('javascript')
    <script src="{{ asset('js/jquery.mask.js')}}"></script>   
    <script src="{{ asset('js/script_cadastro.js') }}" ></script>
    <script>
    
        $('#siape').mask('00000000000000');
       
    </script> 
    <script>
        
        $('#indeterminado').on('change', ()=>{
    
        if($('#indeterminado').is(':checked')){
            
                $('#validade').attr('readonly', true);

        }else{

                $('#validade').attr('readonly', false);
        }
            
    });
        
    </script>

@endpush
@endsection