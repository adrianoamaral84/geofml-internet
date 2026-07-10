@extends('layouts.calendario')

@section('content')
<div class="title-block">
    <h3 class="title"> Dados do Pedido</h3>
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


                         <div class="form-group col-sm-6 col-md-6 col-lg-6">
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
                            <input type="text" class="form-control boxed @error('peridoinicial') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($hospedagem->data_inicio)->format('d-m-Y') }}" name="peridoinicial1" id="peridoinicial" required autofocus onpaste="return false;">
                            @error('peridoinicial')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Período Saída') }}</label>
                            <input type="text" class="form-control boxed @error('final') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($hospedagem->data_termino)->format('d-m-Y') }}" name="final1" id="final" required autofocus onpaste="return false;">
                            @error('final')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        @endif

                    </div>

                    <div class="row has-error">

                        <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                
                             <label class="control-label">{{ __('Grupo Destinação') }}</label>
                                <select name="grupodestinacao" id="grupodestinacao" required class="custom-select mr-sm-2 @error('grupodestinacao') is-invalid @enderror" autocomplete="off">
                                   <option value="">Selecione Grupo Destinação</option>
                                    
                                    @foreach($grupoDestino as $grupo)
                                          <option value="{{$grupo->id}}">{{$grupo->descricao}}</option>
                                    @endforeach

                                                                                   
                                </select>
                               
                           
                                    @error('grupodestinacao')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror


                        </div>

                        <div class="form-group col-sm-12 col-md-6 col-lg-6">
                               
                            <div id="unidadeshabitacionaisdiv">   
                             <label class="control-label">{{ __('Unidades Habitacionais') }}</label>
                                <select name="unidadeshabitacionais" id="unidadeshabitacionais" required class="custom-select mr-sm-2 @error('unidadeshabitacionais') is-invalid @enderror" autocomplete="off">                                   
                                
                                </select>
                               
                           
                                    @error('unidadeshabitacionais')
                                    <span class="has-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                            </div> 
                        </div>
          
                        <div class="form-group col-sm-12 col-md-6 col-lg-6">

                                @if($hospedagem->status == 4)

                                    <label class="control-label">{{ __('Valor') }}</label>
                                    <input type="text" class="form-control boxed @error('valor') is-invalid @enderror" value="{{ number_format( $hospedagem->valor, 2, ',', '.' )}}" name="valor" id="valor" required autofocus readonly onpaste="return false;">
                                    @error('valor')
                                    <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                                @endif

                        </div>    

                    </div>

                    <div class="row has-error">
                        <div class="form-group col-sm-12 col-md-6 col-lg-6">
                       
                        <button type="button" class="btn-lg" id="myBtn">VER DISPONIBILIDADE</button>

                       </div> 
                    </div>

                 @if($hospedagem->status == 4)

                        <!---
                        <img id="myImg" src="{{url('storage/'. $comprovante->caminho)}}" alt="" style="width:100px; margin-top: 20px">
                        -->

                        
                            <a href="{{url('storage/'. $comprovante->caminho)}}" target="_blank" title="Ver Comprovante de Pagamento" class="btn btn-secondary btn-xl" style="margin-top: 30px;"><i class="fas fa-address-card"></i>
                                Ver Comprovante de Pagamento
                            </a>


                        @endif
  
              

               

                



                 

                    <hr>

                    <div class="form-group row">
                        <div class="col-sm-12 col-xl-12">
                            <p class="title-description"> 

                             </p><br>
                            
                             <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle fa-sm"></i>  
                                    Aprovar Solicitação!
                            </button>
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
            <div class="row">
                <div class="col-lg-12 text-center">
                    <p class="lead"></p>
                    <div id="calendars" class="col-centered">
                    </div>
                </div>
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


    <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header" style="padding:5px 10px;">
            
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          
        </div>
        <div class="modal-body" style="padding:40px 50px;">
          
            <div style="width: 50%; height: 50%;">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <p class="lead"></p>
                    <div id="calendars" class="col-centered">
                    </div>
                </div>
            </div>
            <iframe style="width: 1000px; height: 1000px;" src="http://127.0.0.1:8888/GEOFML2.1/public/admin/calendario/unidade/13"></iframe>
            </div>


        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
          
        </div>
        </div>
      
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

<script src="{{ asset('lib/jquery-mask-plugin/dist/jquery.mask.min.js')}}"></script>
<script src="{{ asset('js/litepickerBudler.js')}}"></script>
<script src="{{ asset('js/mobilefriendly.js')}}"></script>


 

<script type="text/javascript">
     function aprovapedido(id)
     {
         var id = id;
         var url = '{{ route("hospedagem.liberar", ":id") }}';
         url = url.replace(':id', id);
         $("#aprovapedido").attr('action', url);
     }
     function negarpedido(id)
     {
         var id = id;
         var url = '{{ route("hospedagem.negar", ":id") }}';
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
    


<script>
function validaGrupo(ufId, cidadeFieldName){
   
    //alert(cidadeFieldName);
    if(ufId) {
        $.ajax({

            url: $host+'/cascade/carregarUnidades/'+ufId,
            type: "GET",
            dataType: "json",

            success:function(data) {
                $('select[name="'+cidadeFieldName+'"]').empty();
                var count = Object.keys(data).length;
                
                if(count > 0) {
                    
                    
                    $('select[name="'+cidadeFieldName+'"]').append('<option value="" disabled selected>Selecione Unidade Habitacional</option>');
                    for(var i = 0; i < count; i++) {

                    if(data[i].pet == 1){

                        $('select[name="'+cidadeFieldName+'"]').append('<option value="'+ data[i].id +'">Nº '+ data[i].sigla +' - '+ data[i].tipohabitacao.descricao +' - PET</option>');
                    }else{
                         $('select[name="'+cidadeFieldName+'"]').append('<option value="'+ data[i].id +'">Nº '+ data[i].sigla +' - '+ data[i].tipohabitacao.descricao +'</option>');
                    }

                       
                            
                        
                        
                    }
                } else {
                    $('select[name="'+cidadeFieldName+'"]').append('<option value="" disabled selected>Selecione</option>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert("Erro ao carregar Grupo Destinação. Status: " + textStatus); alert("Error: " + errorThrown);
            }
        });
    }else{
                    $('select[name="'+cidadeFieldName+'"]').append('<option value="" disabled selected>Selecione Unidade Habitacional</option>');
        
    }
    
}
    $(document).ready(function(){

        $('#unidadeshabitacionaisdiv').hide();
        $protocol = window.location.protocol;
        $host = $protocol+ '//'+$(location).attr('host')+'/GEOFML2.1/public';
        
        $("#myBtn").click(function(){
        $("#myModal").modal();
        });

    });


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
    lang: "pt-BR",
    numberOfColumns: 2,

    lockDays: {!! $a !!},
    tooltipText: {"one":"diária","other":"diarias"},
    
    tooltipNumber: (totalDays) => {
        
        return totalDays - 1;

    },


});
       


    var ufEndId = $('select[name="grupodestinacao"] option:selected').val();
    if (ufEndId != '') {
        //validaGrupo(ufEndId, 'cidade');
    }
    
    
    if($('select[name="grupodestinacao"] option:selected').val() != '') {
        var ufId = $('select[name="grupodestinacao"] option:selected').val();
        //alert(ufId);
        //validaGrupo(ufId, 'cidade');
    }
    $('select[name="grupodestinacao"]').on('change', function() {
        var ufId = $(this).val();
        $('#unidadeshabitacionaisdiv').show();

        //alert(ufId);
        //$('#cidade').prop('readonly', null);
        validaGrupo(ufId, 'unidadeshabitacionais');
    });


    $('select[name="unidadeshabitacionais"]').on('change', function() {
        
        var UH = $(this).val();
        var url = '{{ route("calendario.unidade", ":UH") }}';
        url = url.replace(':UH', UH);
        window.open(url, '_blank', 'width=400, height=450');
        //alert(UH);

    });



</script>  
    
@endpush
@endsection