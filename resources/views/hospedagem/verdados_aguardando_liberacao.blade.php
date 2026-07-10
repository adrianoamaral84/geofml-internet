@extends('layouts.app')

@section('content')
<div class="title-block">
    <h3 class="title"> Dados do Pedido </h3> 
    <p class="title-description">Usuário aguardando a confirmação da sua solicitação!</p>
</div>
<section class="section">
    <div class="row sameheight-container">
        <div class="col-12">

            <div class="card card-block sameheight-item">
                <p class="title-description">  </p><br>
                <form id="profile-form" action="{{ route('hospedagem.liberar')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row has-error">

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Posto / Graduação') }}</label>
                            <input type="text" class="form-control boxed @error('posto') is-invalid @enderror" value="{{ $hospedagem->user->posto->sigla}}" name="posto" id="posto" autofocus required readonly="" maxlength="100" onpaste="return false;" style="text-transform: uppercase;">
                            <input type="hidden" name="id1" value="{{ $hospedagem->id }}" placeholder="">
                            <input type="hidden" name="posto_id" value="{{ $hospedagem->user->posto->id}}">
                            
                            @error('posto')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                            <label class="control-label">{{ __('Nome') }}</label>
                            <input type="text" class="form-control boxed @error('nome') is-invalid @enderror" value="{{ $hospedagem->user->name}}" name="nome" id="nome" autofocus required readonly="" maxlength="100" onpaste="return false;" style="text-transform: uppercase;">
                            <input type="hidden" name="id" value="{{ Crypt::encrypt($hospedagem) }}" placeholder="">
                            
                            @error('nome')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('CPF') }}</label>
                            <input type="text" class="form-control boxed @error('cpf') is-invalid @enderror" readonly="" value="{{ $hospedagem->user_cpf }}" name="cpf" id="cpf" required readonly="" autofocus maxlength="11" data-mask="000.000.000-00" onpaste="return false;">
                            @error('cpf')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
              
                    </div>

                    <div class="row has-error">

                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('UF') }}</label>
                            <input type="text" class="form-control boxed @error('uf') is-invalid @enderror" readonly="" value="{{ $hospedagem->user->uf->descricao }}" name="uf" id="uf" required readonly="" autofocus onpaste="return false;">
                            @error('uf')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                         <div class="form-group col-sm-3 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Cidade') }}</label>
                            <input type="text" class="form-control boxed @error('cidade') is-invalid @enderror" readonly="" value="{{ $hospedagem->user->cidade->descricao }}" name="cidade" id="cidade" required readonly="" autofocus onpaste="return false;">
                            @error('cidade')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('OM') }}</label>
                            <input type="text" class="form-control boxed @error('om') is-invalid @enderror" readonly="" value="{{ $hospedagem->user->om->sigla }}" name="om" id="om" required readonly="" autofocus onpaste="return false;">
                            @error('om')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
    <label class="control-label">Mecenas</label>

    <input type="text"
       class="form-control"
       value="{{ $hospedagem->user->mecenas ? 'Sim' : 'Não' }}"
       readonly>

</div>


                   </div>



                       <div class="row has-error">

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Adulto') }}</label>
                            <input type="text" class="form-control boxed @error('adulto') is-invalid @enderror" readonly="" value="{{ $hospedagem->adulto }}" name="adulto" id="adulto" required autofocus onpaste="return false;">
                            @error('adulto')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                         <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Criança') }}</label>
                            <input type="text" class="form-control boxed @error('crianca') is-invalid @enderror" readonly="" value="{{ $hospedagem->crianca }}" name="crianca" id="crianca" required autofocus onpaste="return false;">
                            @error('crianca')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                        
                             <label class="control-label">{{ __('PNE') }}</label>
                                <select name="uf" id="uf" required readonly="" class="custom-select mr-sm-2 @error('pne') is-invalid @enderror" autocomplete="off">
                                
                                 @if($hospedagem->pne == 1)
                                 <option value="1" selected >Sim</option>
                                @else
                                <option value="0">Não</option>
                                @endif   
        
                                
                                </select>
                               
                           
                                    @error('pne')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror


                                     </div>


                    <div class="form-group col-sm-12 col-md-3 col-lg-3">

                        <label class="control-label">{{ __('PET') }}</label>
                                <select name="pet" id="pet" required readonly="" class="custom-select mr-sm-2 @error('pet') is-invalid @enderror" autocomplete="off">
                                
                                 @if($hospedagem->pet == 1)
                                 <option value="1" selected >Sim</option>
                                @else
                                <option value="0">Não</option>
                                @endif   
        
                                
                                </select>
                               
                           
                                    @error('pet')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                        
                            
                           
                            
                        </div>



                    </div>

                    <div class="row has-error">

                        <div class="form-group col-sm-12 col-md-6 col-lg-6">
                            <label class="control-label">{{ __('Tipo Unidade Habitacional') }}</label>
                            <input type="text" class="form-control boxed @error('unidade_habitacional') is-invalid @enderror" readonly="" value="{{ $hospedagem->tipouh->descricao }}" name="unidade_habitacional" id="unidade_habitacional" required readonly="" autofocus onpaste="return false;">
                            <input type="hidden" name="unidade_habitacional_id" value="{{ $hospedagem->tipouh->id }}">
                            @error('unidade_habitacional')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                      
                        @if($hospedagem->status == 4)
                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Período Entrada') }}</label>
                            <input type="text" class="form-control boxed @error('peridoinicial') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($hospedagem->data_inicio)->format('d-m-Y') }}" readonly="" name="peridoinicial12" id="peridoinicial12">
                            @error('peridoinicial')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Período Saída') }}</label>
                            <input type="text" class="form-control boxed @error('final') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($hospedagem->data_termino)->format('d-m-Y') }}" readonly="" name="final12" id="final12" required autofocus onpaste="return false;">
                            @error('final')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        @else
                         <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Período Entrada') }}</label>
                            <input type="text" class="form-control boxed @error('peridoinicial') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($hospedagem->data_inicio)->format('d-m-Y') }}" readonly="" name="peridoinicial1" id="peridoinicial" required autofocus onpaste="return false;">
                            @error('peridoinicial')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Período Saída') }}</label>
                            <input type="text" class="form-control boxed @error('final') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($hospedagem->data_termino)->format('d-m-Y') }}" readonly="" name="final1" id="final" required autofocus onpaste="return false;">
                            @error('final')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>




                        @endif


                    </div>






                    <div class="row has-error">


                    <div class="form-group col-sm-12 col-md-3 col-lg-3">
                        
                             <label class="control-label">{{ __('Unidades Habitacionais') }}</label>
                                <select name="unidadeshabitacionais" id="unidadeshabitacionais" required class="custom-select mr-sm-2 @error('unidadeshabitacionais') is-invalid @enderror" autocomplete="off" readonly="">
                                   <option value="">Selecione Unidade Habitacional</option>
                                    @foreach($unidades_habitacionais as $unidades_habitacional)

                        <option value="{{$unidades_habitacional->id}}" @if($unidades_habitacional->id == $hospedagem->und_habitacionais_id) selected @endif>{{$unidades_habitacional->sigla}} - {{ $unidades_habitacional->classe->descricao}} - {{ $unidades_habitacional->tipohabitacao->descricao}} @if($unidades_habitacional->pet == 1) - Pet SIM @endif
                        </option>
                        @endforeach
                                   
        
                                
                                </select>
                               
                           
                                    @error('unidadeshabitacionais')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror


                    </div>
                     <div class="form-group col-sm-12 col-md-3 col-lg-3">

                        

                            <label class="control-label">{{ __('Valor Diária') }}</label>
                            <input type="text" class="form-control boxed @error('valordiaria') is-invalid @enderror" value="{{ number_format( $hospedagem->valortarifa, 2, ',', '.' )}}" name="valordiaria" id="valordiaria" required autofocus readonly onpaste="return false;">
                            @error('valor')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        

                        </div>
                        <div class="form-group col-sm-12 col-md-3 col-lg-3">

                        

                            <label class="control-label">{{ __('Quantidade de Diarias') }}</label>
                            <input type="text" class="form-control boxed @error('qntdiarias') is-invalid @enderror" value="{{  $hospedagem->qntdiarias }}" name="qntdiarias" id="qntdiarias" required autofocus readonly onpaste="return false;">
                            @error('valor')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                      

                        </div>
                        <div class="form-group col-sm-12 col-md-3 col-lg-3">

                        

                            <label class="control-label">{{ __('Valor Total') }}</label>
                            <input type="text" class="form-control boxed @error('valor') is-invalid @enderror" value="{{ number_format( $hospedagem->valor, 2, ',', '.' )}}" name="valor" id="valor" required autofocus readonly onpaste="return false;">
                            @error('valor')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        

                        </div>

                        </div>
                        <div class="row has-error">

                        
                         <div class="form-group col-sm-12 col-md-3 col-lg-3">                  

                            <label class="control-label">{{ __('Valor Pago') }}</label>
                            <input type="text" class="form-control boxed @error('valor') is-invalid @enderror" value="{{ number_format( $hospedagem->valor_pago, 2, ',', '.' )}}" name="valorpago" id="valorpago" required autofocus readonly onpaste="return false;">
                            @error('valor')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        <div class="form-group col-sm-12 col-md-3 col-lg-3">                  

                            <label class="control-label">{{ __('Valor Restante') }}</label>
                            <input type="text" class="form-control boxed @error('valor') is-invalid @enderror" value="{{ number_format( $hospedagem->valor_restante, 2, ',', '.' )}}" name="valorrestante" id="valorrestante" required autofocus readonly onpaste="return false;">
                            @error('valor')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>   

                        </div>




                        @if($hospedagem->status == 4)
                        <div class="row has-error">
                       
                        <div class="form-group col-sm-12 col-md-6 col-lg-6"> 
                            
                            <a href="data:image/png;base64, {{ $comprovante->arquivo }}" target="_blank" title="Ver Comprovante de Pagamento" class="btn btn-secondary btn-xl" style="margin-top: 20px;"><i class="fas fa-address-card"></i>
                                Ver Comprovante de Pagamento
                            </a> 

                        </div>   
                        </div>   


                        @endif
  
              

               

                



                 

                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-12 col-xl-12">
                            <p class="title-description"> 

                             </p><br>
                            <a href="javascript:;" data-target="#AprovaModal" data-toggle="modal" onclick="aprovapedido('{{Crypt::encrypt($hospedagem->id)}}')" title="Ver Aguardando Liberação" class="btn btn-success btn-xl" style="color: #fff">
                                   <i class="fas fa-angle-double-up"></i>
                                Liberar Unidade Para Uso
                            </a>

                            
                            <!--
                             <a href="javascript:;" data-toggle="modal" onclick="aprovapedido('{{Crypt::encrypt($hospedagem->id)}}')" class="btn btn-primary" data-target="#AprovaModal" title="Aprovar Pedido">
                                    <i class="fas fa-check-square" style="color: #ffffff"></i> Liberar Acesso!</a>
                            -->

                                    <a href="javascript:;" data-toggle="modal" onclick="negarpedido('{{Crypt::encrypt($hospedagem->id)}}')" data-target="#NegarModal" class="btn btn-danger" title="Negar Pedido">
                                    <i class="fas fa-ban fa-sm" ></i> Negar Solicitação!</a>


                            
                           



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
                                                    <p>Liberar Unidade para Uso?</p>
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
                                        <form action="" id="negarpedido" method="get">
                                            <div class="modal-content">
                                                <div class="modal-header-dangeri">
                                                    <h4 class="modal-title"><i class="fa fa-warning"></i> Atenção</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}  
                                                    {{ method_field('PUT') }}
                                                    <p>Deseja realmente Negar esse Pedido?</p>
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
         var url = '{{ route("hospedagem.liberar_uso", ":id") }}';
         url = url.replace(':id', id);
         $("#aprovapedido").attr('action', url);
     }
     function negarpedido(id)
     {
         var id = id;
         var url = '{{ route("hospedagem.negar_uso", ":id") }}';
         url = url.replace(':id', id);
         $("#negarpedido").attr('action', url);
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
    <script src="{{ asset('js/litepickerBudler.js')}}"></script>
<script src="{{ asset('js/mobilefriendly.js')}}"></script>


<script>
    
window.disableLitepickerStyles = true;

const picker = new Litepicker({ 
    element: document.getElementById('peridoinicial'),
    elementEnd: document.getElementById('final'),
    plugins: ['mobilefriendly','keyboardnav'],
    
    keyboardnav: {
        firstTabIndex: 2,
    },

    mobilefriendly: {
        breakpoint: 480,
    },

    dropdowns: {
        "minYear":2020,
        "maxYear":2022,
        "months":false,
        "years":false,
    },

    singleMode: false,
    allowRepick: false,
    numberOfMonths: 2,
    autoRefresh: true,

    disallowLockDaysInRange: true,

    format: "DD-MM-YYYY",
    //inlineMode: true,
    lang: "pt-BR",
    //maxDays: 2,
    numberOfColumns: 2,


    //maxDate: "{{ $maxDate }}",
    //minDate: "{{ $minDate }}",

    lockDays: {!! $a !!},
    
    //lockDays: [["2021-04-17","2021-04-19"],["2021-04-21","2021-04-23"],"2021-04-20","2021-04-28"],


    //lockDays: [["2021-04-19","2021-04-23"],["2021-05-03","2021-05-06"]],
    //lockDays: [['2021-05-01', '2021-05-05'],'2021-04-28'],
    tooltipText: {"one":"dia","other":"dias"},
    
    //tooltipNumber: (totalDays) => {
        
        //return totalDays - 1;

    //},


});


</script>  
    
@endpush
@endsection