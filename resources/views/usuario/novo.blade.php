@extends('layouts.app')

@section('content')
<div class="title-block">
    <h3 class="title"> Novo Usuário</h3>
    <p class="title-description"> Cadastra novo usuário no sistema! </p>
</div>
                         
<section class="section">
    <div class="row sameheight-container">
        <div class="col-12">

            <div class="card card-block sameheight-item">
                <p class="title-description">  </p><br>
                <form id="profile-form" action="{{ route('usuario.new') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row has-error">
                        <div class="form-group col-sm-8 col-md-8 col-lg-8">
                            <label class="control-label">{{ __('Nome') }}</label>
                            <input type="text" class="form-control boxed @error('nome') is-invalid @enderror" value="{{ old('nome') }}" name="nome" id="nome" autofocus required maxlength="100" onpaste="return false;" style="text-transform: uppercase;">
                           
                            
                            @error('nome')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                            <label class="control-label">{{ __('CPF') }}</label>
                            <input type="text" class="form-control boxed @error('cpf') is-invalid @enderror" value="{{ old('cpf') }}" name="cpf" id="cpf" required autofocus maxlength="11" data-mask="000.000.000-00" onpaste="return false;">
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
                
                                <select name="situacao" id="situacao" required class="custom-select mr-sm-2 @error('situacao') is-invalid @enderror" autocomplete="off">
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

                            <div class="form-group col-sm-4 col-md-4 col-lg-4">
                            <label class="control-label">{{ __('Perfil') }}</label>
                            <select name="perfil_id" id="perfil_id" required autocomplete="off" class="custom-select mr-sm-2 @error('perfil_id') is-invalid @enderror">
                                <option value="" disabled="">Selecione Perfil</option>
                                
                                @foreach($perfis as $perfil)

                                <option value="{{$perfil->id}}">{{$perfil->display_name}}</option>
                                                
                                @endforeach

                            </select>
                            @error('perfil_id')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                    </div>
                   
               
                    <div class="row has-error milReserva">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                               
                            <label class="form-check">
                           
                            <input type="checkbox" id="pttc" name="pttc" value="1" class="form-check-input" required="no"> 
                           
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
                             
                            
                            <label class="control-label" id="nivel">{{ __('Posto / Graduação') }}</label>
                                <select name="posto" id="posto" class="custom-select mr-sm-2 @error('posto') is-invalid @enderror" autocomplete="off">
                                <option value="" >Selecione Posto / Grad</option>
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

                        


                        <div class="col-sm-4 col-md-4 col-lg-4 form-group militarAtiva">
                            <label class="control-label">{{ __('Data Última Promoção') }}</label>
                            <input type="date" class="form-control boxed @error('dtUltPromo') is-invalid @enderror" name="dtUltPromo" id="dtUltPromo" autofocus onpaste="return false;" value="{{ old('dtUltPromo') }}" placeholder="dd-mm-yyyy">
                            @error('dtUltPromo')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-4 col-md-4 col-lg-4 form-group identidade">
                            <label class="control-label" id="texto"> {{ __('Identidade Militar') }}</label>
                            <input type="text" class="form-control boxed @error('idtMil') is-invalid @enderror" value="{{ old('idtMil') }}" name="idtMil" id="idtMil" maxlength="9" required="required" autocomplete="off" onpaste="return false;">
                            @error('idtMil')
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

                                         <option value="{{$nivel->id}}">{{$nivel->nivel}}</option>

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
                                <input type="text" class="form-control boxed @error('siape') is-invalid @enderror" value="{{ old('siape') }}" name="siape" id="siape" autofocus maxlength="14" onpaste="return false;">

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
                                    
                            <option value="{{$uf->id}}">{{$uf->sigla}}</option>
                                   
        
                                    @endforeach
                                </select>

                                @error('uf')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                              <div class="col-sm-4 col-md-4 col-lg-4 form-group">
                                <input type="hidden" name="cidade" id="cidade" value="old('cidade') }}">
                                
                                <input type="hidden" name="cidadee" id="cidadee" value="{{ old('cidade') }}">
                                
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
                          
                                <input type="hidden" name="om" value="">

                                <label class="control-label">{{ __('OM') }}</label>
                                <select name="om" id="om" class="custom-select mr-sm-2 @error('om') is-invalid @enderror" autocomplete="off">
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
                            <input type="email" class="form-control boxed @error('email') is-invalid @enderror" value="{{ old('email') }}" name="email" id="email" required maxlength="100" onpaste="return false;">
                            @error('email')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="col-sm-6 col-md-4 col-lg-4 form-group">
                            <label class="control-label">{{ __('Telefone C/ WhatsApp') }}</label>
                            <input type="text" class="form-control boxed @error('telefone') is-invalid @enderror" 
                            value="{{ old('telefone') }}" name="telefone" id="telefone" maxlength="11" onpaste="return false;" required="required" data-mask="(00) 00000-0000" autocomplete="off">
                            @error('telefone')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                </div>



            
                     


                <div class="row form-group has-error">
                    <div class="col-sm-12 col-md-12 col-lg-12 form-group">
                            <label class="control-label">{{ __('Anexar Documento Frente') }}</label>

                             <p class="title-description"> Carteira de Identidade formato .jpg .png (Máximo 2mb)</p>
                        <div class="custom-file">
                         <input type="file" class="custom-file-input" name="documento" id="documento" accept=".jpg,.png,.pdf" required="" name="documento">
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
                             <p class="title-description"> Carteira de Identidade formato .jpg .png (Máximo 2mb)</p>
                        <div class="custom-file">
                         <input type="file" class="custom-file-input" name="documento_verso" id="documento_verso" accept=".jpg,.png,.pdf" required="" name="documento">
                            <label class="custom-file-label" for="customFile">Escolha o arquivo</label>
                        </div>
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
                                    Cadastrar
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
   
    <script src="{{ asset('js/script_cadastro.js') }}" ></script>   
    
@endpush
@endsection