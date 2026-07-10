@extends('layouts.app')

@section('content')
<div class="title-block">
    <h3 class="title"> Dados do Usuário </h3>
    <p class="title-description">  </p>
</div>
<section class="section">
    <div class="row sameheight-container">
        <div class="col-12">

            <div class="card card-block sameheight-item">
                <p class="title-description">  </p><br>
                <form id="profile-form" action="{{ route('atualiza.dados.usuario') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row has-error">
                        <div class="form-group col-sm-8 col-md-8 col-lg-8">
                            <label class="control-label">{{ __('Nome') }}</label>
                            <input type="text" class="form-control boxed @error('nome') is-invalid @enderror" value="{{ $user->name }}" name="nome" id="nome" autofocus required maxlength="100" onpaste="return false;" style="text-transform: uppercase;">
                            <input type="hidden" name="id" value="{{ Crypt::encrypt($user->id) }}" placeholder="">
                            
                            @error('nome')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                            <label class="control-label">{{ __('CPF') }}</label>
                            <input type="text" class="form-control boxed @error('cpf') is-invalid @enderror" value="{{ $user->cpf }}" name="cpf" id="cpf" required autofocus maxlength="11" data-mask="000.000.000-00" onpaste="return false;">
                            @error('cpf')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
              
                    </div>

    
                    <div class="row has-error">
                        @role('administrador_geral')
                          <div class="form-group col-sm-4 col-md-4 col-lg-4">
                    

                            <label class="control-label">{{ __('Perfil') }}</label>
                            <select name="perfil_id" id="perfil_id" required autocomplete="off" class="custom-select mr-sm-2 @error('perfil_id') is-invalid @enderror">
                                <option value="" disabled="">Selecione Perfil</option>
                                
                                @foreach($perfis as $perfil)

                                <option value="{{$perfil->id}}" @if($user->perfil_id == $perfil->id)selected="selected" @endif>{{$perfil->display_name}}</option>
                                                
                                @endforeach

                            </select>
                            @error('perfil_id')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                   
                        </div>
                         @endrole


                        <div class="form-group col-sm-8 col-md-8 col-lg-8">
                            <label class="control-label">{{ __('Situação') }}</label>
                     <input type="hidden" name="posto" value="{{$user->postograd_id}}">
                                <select name="situacao" id="situacao" required class="custom-select mr-sm-2 @error('situacao') is-invalid @enderror" onpaste="return false;">
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

                          


                    </div>
                   
               
                    <div class="row has-error milReserva">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                               
                            <label class="form-check">
                            @if($user->pttc == 1)
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
                             
                            <input type="hidden" name="postos" value="{{$user->postograd_id}}">
                            <label class="control-label" id="nivel">{{ __('Posto / Graduação') }}</label>
                                <select name="posto" id="posto" class="custom-select mr-sm-2 @error('posto') is-invalid @enderror" autocomplete="off">
                                <option value="">Selecione</option>
                                @foreach($postos as $posto)
                               
                                 @if($user->postograd_id == $posto->id)
                                <option value="{{$posto->id}}" selected="selected">{{$posto->sigla}}</option>
                                @else
                                 <option value="{{$posto->id}}">{{$posto->sigla}}</option>
                                @endif
                                @endforeach
                            </select>
                            @error('posto')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-4 col-md-4 col-lg-4 form-group militarAtiva">
                            <label class="control-label">{{ __('Data Última Promoção') }}</label>
                            <input type="date" class="form-control boxed @error('dtUltPromo') is-invalid @enderror" value="{{$user->dtUltPromo}}" name="dtUltPromo" id="dtUltPromo" autofocus onpaste="return false;" placeholder="dd-mm-yyyy" max='{{ $hoje }}'>
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
                            <label class="control-label" id="texto"> {{ __('Validade') }}</label> <div style="float: right;">
                                <small>
                        @if($user->indeterminado == 1 )
                        <input type="checkbox" name="indeterminado" id="indeterminado" value="1"  checked="" class="form-check-input"> <label for="indeterminado">
                            Indeterminada </label>
                        @else
                        <input type="checkbox" name="indeterminado" id="indeterminado" value="1"> <label for="indeterminado">
                            Indeterminada </label>
                        @endif
                        
                        </small></div>

                            <input type="date" class="form-control boxed @error('validade') is-invalid @enderror" value="{{$user->validade}}" name="validade" id="validade" placeholder="dd-mm-yyyy" required="required" onpaste="return false;" @if($user->indeterminado == 1) readonly="readonly" @endif>
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
                                <input type="text" class="form-control boxed @error('siape') is-invalid @enderror" value="{{ ($user->siape) ? $user->siape : old('siape') }}" name="siape" id="siape" autofocus maxlength="14" onpaste="return false;">

                            @error('siape')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                </div>
  
                     <div class="row has-error">


                             <div class="col-sm-4 col-md-4 col-lg-4 form-group">
                                <label class="control-label">{{ __('UF') }}</label>
                                <select name="uf" id="uf" required class="custom-select mr-sm-2 @error('uf') is-invalid @enderror" autocomplete="off">
                                <option value="">Selecione UF</option>
                                    @foreach($ufs as $uf)
                                    
                            <option value="{{$uf->id}}" @if($user->uf_id == $uf->id)selected @endif>{{$uf->sigla}}</option>
                                   
        
                                    @endforeach
                                </select>

                                @error('uf')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                              <div class="col-sm-4 col-md-4 col-lg-4 form-group">
                                <input type="hidden" name="cidade" id="cidade" value="{{ ($user->cidade_id) ? $user->cidade_id : old('cidade') }}">
                                
                                <input type="hidden" name="cidadee" id="cidadee" value="{{ ($user->cidade_id) ? $user->cidade_id : old('cidade') }}">
                                
                                <label class="control-label">{{ __('Cidade') }}</label>
                                <select name="cidade" id="cidade" required="required" class="custom-select mr-sm-2 @error('cidade') is-invalid @enderror" autocomplete="off">
                                <option value="">Selecione Cidade</option>
                                   
                                </select>

                                @error('cidade')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-4 col-md-4 col-lg-4 form-group">
                          
                                <input type="hidden" name="om" value="{{$user->om_id}}">
                                <label class="control-label">{{ __('OM') }}</label>
                                <select name="om" id="om" required class="custom-select mr-sm-2 @error('om') is-invalid @enderror" autocomplete="off">
                                <option value="">Selecione OM</option>
                                
                                                       
                                </select>

                                @error('om')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                    </div>

                 <div class="row form-group has-error">
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
                        <div class="col-3 form-group">
                         <label class="control-label">{{ __('Documento') }}</label><br>
                     </div>
                     </div>
                     <div class="row form-group has-error">
                        <div class="col-3 form-group">
                           @if($user->documento) 
                          
                           
                                <a href="{{ route('documentos.verdocumento', ['id' => Crypt::encrypt($user->id), 'doc' => Crypt::encrypt($user->tipo_doc), 'tipo' => '1']) }}" target="_blank" class="btn btn-secondary btn-xl rounded-s">
                                    <i class="fas fa-address-card"></i>
                                    Frente
                                </a>

                            @endif
                        </div>
                          <div class="col-3 form-group">
                             @if($user->documento_verso)   
                           
                                   <a href="{{ route('documentos.verdocumento', ['id' => Crypt::encrypt($user->id), 'doc' => Crypt::encrypt($user->tipo_doc_verso), 'tipo' => '2']) }}" target="_blank" class="btn btn-secondary btn-xl rounded-s">
                                    <i class="fas fa-address-card"></i>
                                    Verso
                                    </a>       
                                       
                            @endif
                        </div>
                    </div>


                    <div class="row form-group has-error">
                        <div class="col-sm-12 col-md-12 col-lg-12 form-group">
                            <label class="control-label">{{ __('Anexar Documento Frente') }}</label>

                             <p class="title-description"> Imagem da Identidade no formato .JPG, .PNG, .PDF até 4MB ou PRINT aplicativo EBCIM </p>
                        <div class="custom-file">
                         <input type="file" class="custom-file-input" name="documento" id="documento" accept=".jpg,.png,.pdf" @if(!$user->documento) required="" @endif name="documento">
                            <label class="custom-file-label" for="customFile">Escolha o arquivo</label>
                        </div>
                        @error('documento')
                        <span class="has-error" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                       </div>

                              <div class="col-sm-12 col-md-12 col-lg-12 form-group">
                            <label class="control-label">{{ __('Anexar Documento Verso') }}</label>
                             <p class="title-description"> Imagem da Identidade no formato .JPG, .PNG, .PDF até 4MB ou PRINT aplicativo EBCIM </p>
                        <div class="custom-file">
                         <input type="file" class="custom-file-input" name="documento_verso" id="documento_verso" accept=".jpg,.png,.pdf" @if(!$user->documento_verso) required="" @endif name="documento">
                            <label class="custom-file-label" for="customFile">Escolha o arquivo</label>
                        </div>
                        @error('documento_verso')
                        <span class="has-error" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                       </div>
                    </div>              
                    

                    
                    @role('administrador_geral|administrador')
                          <div class="row form-group col-sm-12 col-md-12 col-lg-12">
                    
                            <label class="control-label">{{ __('Observações') }}</label>
                            <select name="motivo" id="motivo" autocomplete="off" class="custom-select mr-sm-2 @error('motivo') is-invalid @enderror">

                                <option value="" @if($user->motivo_id == '')selected="selected" @endif>Selecione Motivo</option>
                                
                                @foreach($motivos as $motivo)

                                <option value="{{$motivo->id}}" @if($user->motivo_id == $motivo->id)selected="selected" @endif>{{$motivo->motivo}}</option>
                                                
                                @endforeach                                            

                            </select>
                            @error('motivo')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                   
                        </div>
                    @endrole
                    

                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-12 col-xl-12">
                            <p class="title-description"> 
                            * Verifique se os dados estão corretos antes de Salvar! <br>
                            * Resetar a senha para o CPF cadastrado!

                             </p><br> @role('administrador_geral')
                             <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle fa-sm"></i>  
                                    Salvar Alterações
                            </button>
                            @endrole
                            @role('administrador_geral|auxiliar_administrador_geral')
                                <a href="{{ route('usuario.reset', ['id' => Crypt::encrypt($user->id)]) }}" class="btn btn-dark"><i class="fas fa-check-circle fa-sm"></i> Resetar Senha! </a>
                            @endrole

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
    <script src="{{ asset('js/script_cadastro.js') }}" ></script>   
     <script>
    
    
        $('#siape').mask('00000000000000');
        
      $('#indeterminado').on('change', ()=>{
    
        if($('#indeterminado').is(':checked')){
            
                $('#validade').attr('readonly', true);
                $('#documento').prop('required', true);
                $('#documento_verso').prop('required', true);
                alert('Confirma a data de validade da sua identidade militar? Atualmente a validade é de 10 anos.')

        }else{
                $('#documento').prop('required', true);
                $('#documento_verso').prop('required', true);
                $('#validade').attr('readonly', false);
                alert('Confirma a data de validade da sua identidade militar? Atualmente a validade é de 10 anos.')
              
                
        }
            
    });

    </script>
   
    
@endpush
@endsection
