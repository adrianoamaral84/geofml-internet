@extends('layouts.app')

@section('content')
<div class="title-block">
    <h3 class="title"> Dados do Usuário</h3>
    <p class="title-description"> Usuário aguardando a confirmação do seu cadastro </p>
</div>
<section class="section">
    <div class="row sameheight-container">
        <div class="col-12">

            <div class="card card-block sameheight-item">
                <p class="title-description">  </p><br>
                <form id="profile-form" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row has-error">
                        <div class="form-group col-sm-8 col-md-8 col-lg-8">
                            <label class="control-label">{{ __('Nome') }}</label>
                            <input type="text" class="form-control boxed @error('nome') is-invalid @enderror" value="{{ $user->name }}" name="nome" id="nome" autofocus required readonly="" maxlength="100" onpaste="return false;" style="text-transform: uppercase;">
                            <input type="hidden" name="id" value="{{ Crypt::encrypt($user->id) }}" placeholder="">
                            
                            @error('nome')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                            <label class="control-label">{{ __('CPF') }}</label>
                            <input type="text" class="form-control boxed @error('cpf') is-invalid @enderror" readonly="" value="{{ $user->cpf }}" name="cpf" id="cpf" required readonly="" autofocus maxlength="11" data-mask="000.000.000-00" onpaste="return false;">
                            @error('cpf')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
              
                    </div>


                    <div class="row has-error">

                        <div class="form-group col-sm-12 col-md-12 col-lg-12">
                            <label class="control-label">{{ __('Situação') }}</label>
                
                                <select name="situacao" id="situacao" required class="custom-select mr-sm-2 @error('situacao') is-invalid @enderror" readonly="" autocomplete="off">
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
                            @if($user->pttc === 1)
                            <input type="checkbox" readonly="" id="pttc" name="pttc" value="1" checked="" class="form-check-input" required="no">
                            @else
                            <input type="checkbox" readonly="" id="pttc" name="pttc" value="1" class="form-check-input" required="no"> 
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
                         <div class="col-sm-4 col-md-4 col-lg-4 form-group militarAtiva">
                            <label class="control-label">{{ __('Data Última Promoção') }}</label>
                            <input type="date" class="form-control boxed @error('dtUltPromo') is-invalid @enderror" value="{{$user->dtUltPromo}}" name="dtUltPromo" id="dtUltPromo" autofocus onpaste="return false;" placeholder="dd-mm-yyyy" readonly="">
                            @error('dtUltPromo')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>




                        <div class="col-sm-4 col-md-4 col-lg-4 form-group identidade">
                            <label class="control-label" id="texto"> {{ __('Identidade Militar') }}</label>
                            <input type="text" class="form-control boxed @error('idtMil') is-invalid @enderror" value="@if($user->idtMil){{$user->idtMil}}@endif" name="idtMil" id="idtMil" maxlength="9" readonly="" required="required" autocomplete="off" onpaste="return false;">
                            @error('idtMil')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                       
                     

                          <div class="col-sm-4 col-md-4 col-lg-4 form-group ForcaOmPosto">
                             
                            <input type="hidden" name="posto" value="{{$user->postograd_id}}">
                            <label class="control-label" id="nivel">{{ __('Posto / Graduação') }}</label>
                                <select name="posto" id="posto" class="custom-select mr-sm-2 @error('posto') is-invalid @enderror" readonly="" autocomplete="off">
                                <option value="">Selecione</option>
                                
                            </select>
                            @error('posto')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                 

                        <div class="col-sm-4 col-md-4 col-lg-4 form-group nivelescola">
                            <label class="control-label">{{ __('Nivel') }}</label>
                
                                <select name="nivel" id="nivel" readonly="" class="custom-select mr-sm-2 @error('nivel') is-invalid @enderror" autocomplete="off">
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
                                <input type="text" class="form-control boxed @error('siape') is-invalid @enderror" value="{{ ($user->siape) ? $user->siape : old('siape') }}" name="siape" id="siape" autofocus readonly="" maxlength="14" onpaste="return false;" @if($user->siape) readonly @endif>

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
                                <select name="uf" id="uf" readonly="" required class="custom-select mr-sm-2 @error('uf') is-invalid @enderror" autocomplete="off">
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
                                <select name="cidade" readonly="" id="cidade" required="required" class="custom-select mr-sm-2 @error('cidade') is-invalid @enderror" autocomplete="off">
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
                                <select name="om" id="om" readonly="" required class="custom-select mr-sm-2 @error('om') is-invalid @enderror" autocomplete="off">
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
                            <input type="email" class="form-control boxed @error('email') is-invalid @enderror" value="{{ $user->email }}" name="email" id="email" required readonly="" maxlength="100" onpaste="return false;">
                            @error('email')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="col-sm-6 col-md-4 col-lg-4 form-group">
                            <label class="control-label">{{ __('Telefone C/ WhatsApp') }}</label>
                            <input type="text" class="form-control boxed @error('telefone') is-invalid @enderror" 
                            value="@if($user->telefone){{$user->telefone}}@endif" name="telefone" id="telefone" readonly="" maxlength="11" onpaste="return false;" required="required" data-mask="(00) 00000-0000" autocomplete="off">
                            @error('telefone')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                </div>


                <div class="row has-error">
                        <div class="col-3 form-group">
                         <label class="control-label">{{ __('Documento de Identidade Militar') }}</label><br>
                     </div>
                     </div>
            
                   <div class="row form-group has-error">
                        <div class="col-3 form-group">
                           @if($user->documento) 
                          
                           
                                <a href="{{url('storage/'. $user->documento)}}" target="_blank" class="btn btn-secondary btn-xl rounded-s"> <i class="fas fa-address-card"></i> Frente
                                </a>
                            @endif
                        </div>
                          <div class="col-3 form-group">
                             @if($user->documento)   
                           
                                          
                                <a href="{{url('storage/'. $user->documento_verso)}}" target="_blank" class="btn btn-secondary btn-xl"><i class="fas fa-address-card"></i>
                                Verso
                                </a>
                                       
                            @endif
                        </div>
                    </div>



                    <!--
                    <div class="row form-group has-error">
                        <div class="col-xl-12 col-sm-12 col-md-12 col-lg-12">
                            <label class="control-label">{{ __('Anexar Documento') }}</label>
                             <p class="title-description"> Carteira de Identidade formato .jpg .png </p>
                        <div class="custom-file">
                         <input type="file" class="custom-file-input" id="documento" accept=".jpg,.png,.pdf" @if(!$user->documento) required="" @endif name="documento">
                            <label class="custom-file-label" for="customFile">Escolha o arquivo</label>
                        </div>
                        @error('documento')
                        <span class="has-error" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                       </div>
                    </div>              
                    -->

                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-12 col-xl-12">
                            <p class="title-description"> 

                             </p><br>
                             <!--
                             <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle fa-sm"></i>  
                                    Salvar Alterações
                            </button>
                            -->
                             <a href="javascript:;" data-toggle="modal" onclick="aprovapedido('{{Crypt::encrypt($user->id)}}')" class="btn btn-primary" data-target="#AprovaModal" title="Aprovar Pedido">
                                    <i class="fas fa-check-square" style="color: #ffffff"></i> Liberar Acesso!</a>

                                    <a href="javascript:;" data-toggle="modal" data-target="#NegarModal" class="btn btn-danger" title="Negar Pedido">
                                    <i class="fas fa-ban fa-sm" ></i> Negar Acesso!</a>

                            
                            <a href="{{ route('usuario.reset', ['id' => Crypt::encrypt($user->id)]) }}" class="btn btn-dark"><i class="fas fa-check-circle fa-sm"></i>  Resetar Senha! </a>
                        </div>
                    </div>


                </form>
             
             
                
                <p></p>
                <p></p>

                <p></p>
                <p></p>
                <p></p>

                <p>&nbsp;</p>
            </div>
            
                                    <div class="modal fade" id="AprovaModal">
                                    <div class="modal-dialog" role="document">
                                        <form action="" id="aprovapedido" method="get">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><i class="fa fa-warning"></i> Atenção</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}  
                                                    {{ method_field('PUT') }}
                                                    <p>Deseja realmente Aprovar esse Pedido?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="formSubmitAprova()">Sim</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>


                                <div class="modal fade" id="NegarModal">
                                    <div class="modal-dialog" role="document">
                                    <form id="negarpedido" action="{{ route('envia.mail.negado') }}" method="POST" enctype="multipart/form-data">
                                       
                                            <div class="modal-content">
                                                <div class="modal-header-dangeri">
                                                    <h4 class="modal-title"><i class="fa fa-warning"></i> Atenção</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}  
                                                    {{ method_field('POST') }}
                                                    <p>Deseja realmente Negar esse Pedido?</p>
                                                    <p>Motivo</p>
                                                    <p><textarea name="motivo" id="motivo"></textarea></p>
                                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="formSubmitNegar()">Sim</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
        </div>
    </div>
</section>


@push('javascript')
<script type="text/javascript">
     function aprovapedido(id)
     {
        var id = id;
        var url = '{{ route("libera.acesso", ":id") }}';
        url = url.replace(':id', id);
        $("#aprovapedido").attr('action', url);
     }
     function negarpedido(id)
     {
        var motivo = document.getElementById("motivo").value;
        var id = id;
        var url = '{{ route("envia.mail.negado", ":id", ":motivo") }}';
        url = url.replace(':id', id);
        url = url.replace(':motivo', motivo);
        alert(url);

        //$("#negarpedido").attr('action', url);
     }

     function formSubmitAprova()
     {
         $("#aprovapedido").submit();
     }

     function formSubmitNegar()
     {
         $("#negarpedido").submit();
     }
</script>
    <script src="{{asset('lib/jquery-mask-plugin/dist/jquery.mask.min.js')}}"></script>
    <script src="{{ asset('js/script_cadastro.js') }}" ></script>   
    
@endpush
@endsection
