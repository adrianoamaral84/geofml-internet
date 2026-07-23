@extends('layouts.app')
@section('title', 'Dados do seu pedido')
@section('content')

<style type="text/css">
    
    @media print {
  body * {
    visibility: hidden;
  }
  #printable, #printable * {
    visibility: visible;
  }
  #printable {
    position: fixed;
    left: 0;
    top: 0;
  }
}


#checkout22{
  display: none;
}


/* Style the Image Used to Trigger the Modal */
#myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modals {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (Image) */
.modals-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image (Image Text) - Same Width as the Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation - Zoom in the Modal */
.modals-content, #caption {
  animation-name: zoom;
  animation-duration: 0.6s;
}

@keyframes zoom {
  from {transform:scale(0)}
  to {transform:scale(1)}
}

/* The Close Button */
.close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modals-content {
    width: 100%;
  }
} 


 

</style>

<div id="printable">
<div class="title-block">    
    <h3 class="title"> Dados do seu Pedido </h3>
    <p class="title-description"> Verifique aqui os dados do seu pedido de inscrição para hospedagem no Forte Marechal Luz </p>
    


</div>
<section class="section">
    <div class="row sameheight-container">
        <div class="col-12">

            <div class="card card-block sameheight-item">
                <p class="title-description">  </p><br>
                <form id="profile-form" action="{{ route('hospede.uploadrecibo')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                        @if($hospedagem->status == 5)
                            <div>
                                <div class="alert alert-danger" role="alert">
                                    <div align="center" class="card-content">
                                       Faça o UPLOAD do comprovante de pagamento para confirmar a reserva
                                    </div>
                                </div>
                            </div>
                        @endif
                    
                    <div class="row has-error">
                       
                        <div class="form-group col-sm-8 col-md-8 col-lg-8">
                                   
                                    @if($hospedagem->status == 0)

                                    <h6>
                                            <label class="control-label">Status: <span class="badge badge-secondary">
                                            {{ $hospedagem->status_hospedagem->status }}
                                        </span></label>
                                    </h6>

                                    @endif

                                    @if($hospedagem->status == 1)

                                    <h6>
                                            <label class="control-label">Status: <span class="badge badge-danger">
                                            {{ $hospedagem->status_hospedagem->status }}
                                        </span></label>
                                    </h6>

                                    @endif

                                    @if($hospedagem->status == 2)

                                    <h6>
                                            <label class="control-label">Status: <span class="badge badge-success">
                                            {{ $hospedagem->status_hospedagem->status }}
                                        </span></label>
                                    </h6>

                                    @endif

                                    @if($hospedagem->status == 3)

                                    <h6>
                                            <label class="control-label">Status: <span class="badge badge-secondary">
                                            {{ $hospedagem->status_hospedagem->status }}
                                        </span></label>
                                    </h6>

                                    @endif

                                     @if($hospedagem->status == 4)

                                    <h6>
                                            <label class="control-label">Status: <span class="badge badge-secondary">
                                            {{ $hospedagem->status_hospedagem->status }}
                                        </span></label>
                                    </h6>

                                    @endif

                                    @if($hospedagem->status == 5)

                                    <h6>
                                            <label class="control-label">Status: <span class="badge badge-secondary">
                                            {{ $hospedagem->status_hospedagem->status }}
                                        </span></label>
                                    </h6>

                                    @endif

                                     @if($hospedagem->status == 6)

                                    <h6>
                                            <label class="control-label">Status: <span class="badge badge-secondary">
                                            {{ $hospedagem->status_hospedagem->status }}
                                        </span></label>
                                    </h6>

                                    @endif


                        </div>
                    </div>

                    <div class="row has-error">

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Posto / Graduação') }}</label>
                            <input type="text" class="form-control boxed @error('posto') is-invalid @enderror" value="{{ $hospedagem->user->posto->sigla}}" name="posto" id="posto" autofocus required readonly="" maxlength="100" onpaste="return false;" style="text-transform: uppercase;">
                            <input type="hidden" name="hospedagem_id" value="{{ $hospedagem->id }}">
                            <input type="hidden" name="posto_id" value="{{ $hospedagem->user->posto->id}}">
                           
                            @error('posto')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group col-sm-12 col-md-6 col-lg-6">
                            <label class="control-label">{{ __('Nome') }}</label>
                            <input type="text" class="form-control boxed @error('nome') is-invalid @enderror" value="{{ $hospedagem->user->name}}" name="nome" id="nome" autofocus required readonly="" maxlength="100" onpaste="return false;" style="text-transform: uppercase;">
                            <input type="hidden" name="id" value="{{ Crypt::encrypt($hospedagem->user_id) }}" placeholder="">
                            
                            @error('nome')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
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

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('UF') }}</label>
                            <input type="text" class="form-control boxed @error('uf') is-invalid @enderror" readonly="" value="{{ $hospedagem->user->uf->descricao }}" name="uf" id="uf" required readonly="" autofocus onpaste="return false;">
                            @error('uf')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                         <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Cidade') }}</label>
                            <input type="text" class="form-control boxed @error('cidade') is-invalid @enderror" readonly="" value="{{ $hospedagem->user->cidade->descricao }}" name="cidade" id="cidade" required readonly="" autofocus onpaste="return false;">
                            @error('cidade')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
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

                        
                      

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Período Entrada') }}</label>
                            <input type="text" class="form-control boxed @error('peridoinicial') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($hospedagem->data_inicio)->format('d-m-Y') }}" name="peridoinicial1" id="peridoinicial" required readonly autofocus onpaste="return false;">
                            @error('peridoinicial')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Período Saída') }}</label>
                            <input type="text" class="form-control boxed @error('final') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($hospedagem->data_termino)->format('d-m-Y') }}" name="final1" id="final" required autofocus readonly onpaste="return false;">
                            @error('final')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>



                         <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Horário Entrada') }}</label>
                            <input type="text" class="form-control boxed @error('peridoinicial') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($horario->entrada)->format('H:i') }}" name="peridoinicial1" id="peridoinicial" required readonly autofocus onpaste="return false;">
                            @error('peridoinicial')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label class="control-label">{{ __('Horário Saída') }}</label>
                            <input type="text" class="form-control boxed @error('horariosaida') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($horario->saida)->format('H:i') }}" name="final1" id="horariodaida" required autofocus readonly onpaste="return false;">
                            @error('horariodaida')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                    </div>


                    <div class="row has-error">

                       @if($hospedagem->status == 0)
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
                        @endif


                        @if($hospedagem->status == 2 or $hospedagem->status == 3 or $hospedagem->status == 4 or $hospedagem->status == 5)
                        
                         <div class="form-group col-sm-12 col-md-3 col-lg-3">
                        
                          <label class="control-label" style="color: red;">UH Distribuída</label>
                            <select name="unidadeshabitacionais" id="unidadeshabitacionais" required class="custom-select mr-sm-2 @error('unidadeshabitacionais') is-invalid @enderror" autocomplete="off" readonly="">
                            <option value="">Selecione Unidade Habitacional</option>
                            @foreach($unidades_habitacionais as $unidades_habitacional)

                        <option value="{{$unidades_habitacional->id}}" @if($unidades_habitacional->id == $hospedagem->und_habitacionais_id) selected @endif>{{$unidades_habitacional->sigla}}{{$hospedagem->undHB->classe->classe}} - {{ $unidades_habitacional->classe->descricao}} - {{ $unidades_habitacional->tipohabitacao->descricao}} @if($unidades_habitacional->pet == 1) - Pet SIM @endif
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

                        

                            <label class="control-label">{{ __('Quantidade de Diárias') }}</label>
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
                        @if($hospedagem->status == 2 or $hospedagem->status == 4)
                        @if($hospedagem->checkin == 1 or $hospedagem->checkin == null)
                          <div class="form-group col-sm-12 col-md-3 col-lg-3">                  

                            <label class="control-label">{{ __('Valor Pago') }}</label>
                            <input type="text" class="form-control boxed @error('valor') is-invalid @enderror" value="{{ number_format( $hospedagem->valor_pago, 2, ',', '.' )}}" name="valorpago" id="valorpago" required autofocus readonly onpaste="return false;">
                            @error('valor')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        @endif
                        @endif
                        


                        
                        
                        @if($hospedagem->status == 2)
                        @if($hospedagem->checkin_at != null)
                        @if($hospedagem->checkin == null)
                        
                         <div class="form-group col-sm-12 col-md-3 col-lg-3">                  

                            <label class="control-label">{{ __('Valor Restante') }}</label>
                            <input type="text" class="form-control boxed @error('valor') is-invalid @enderror" value="{{ number_format( $hospedagem->valor_restante, 2, ',', '.' )}}" name="valorrestante" id="valorrestante" required autofocus readonly onpaste="return false;">
                            @error('valor')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        
                        @endif
                        @endif
                        
 
                        @if($hospedagem->status == 2)
                         @if($hospedagem->checkin_at != null)
                         @if($hospedagem->checkin == 1)

                         <div class="form-group col-sm-12 col-md-3 col-lg-3">                  

                            <label class="control-label">{{ __('Valor Restante') }}<font color="red"> até o momento </font></label>
                            <input type="text" class="form-control boxed @error('valor') is-invalid @enderror" value="{{ number_format( $hospedagem->valor_restante, 2, ',', '.' )}}" name="valorrestante" id="valorrestante" required autofocus readonly onpaste="return false;">

                            @error('valor')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        @endif
                        @endif


                          @if($CheckInAntecipado == 1)
                          @if($hospedagem->checkin_at != null)
                          @if($hospedagem->checkin == 1)
                        

                            <div class="form-group col-sm-12 col-md-3 col-lg-3">                  
                            <label class="control-label">{{ __('Acréscimo Check-in') }}</label>
                            <input type="text" class="form-control boxed @error('valor') is-invalid @enderror" value="{{ number_format( $hospedagem->valortarifa, 2, ',', '.' )}}" name="checkinAntecipado" id="checkinAntecipado" required autofocus readonly onpaste="return false;">
                            @error('checkinAntecipado')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        @endif
                        @endif
                        @endif
                        

                        @if($CheckOutAtrasado == 1)
                         @if($hospedagem->checkin_at != null)
                         @if($hospedagem->checkin == 1)
                                <div class="form-group col-sm-12 col-md-3 col-lg-3">                  

                            <label class="control-label">{{ __('Acréscimo Check-Out') }}</label>
                            <input type="text" class="form-control boxed @error('valor') is-invalid @enderror" value="{{ number_format( $hospedagem->valortarifa, 2, ',', '.' )}}" name="CheckOutAtrasado" id="CheckOutAtrasado" required autofocus readonly onpaste="return false;">
                            @error('CheckOutAtrasado')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        @endif
                        @endif
                        @endif
                        

                        @endif
                        @endif
                        


                        @endif
                        

                        
                      </div>
                      @if($hospedagem->status == 2)
                          @role('atendente|administrador_geral|administrador|auxiliar_administrador_geral')
                           
                              <div class="row has-error">                         

                               @if($hospedagem->checkin == 1)
                                <div class="form-group col-sm-12 col-md-3 col-lg-3">  
                                  <label class="control-label">{{ __('Data Check-In') }}</label>
                                    <input type="text" class="form-control boxed @error('datacheckin') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($hospedagem->checkin_at)->format('d-m-Y H:i:s') }}" name="datacheckin" id="datacheckin" required readonly autofocus onpaste="return false;">
                                    @error('datacheckin')
                                    <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>     
                                @endif
                               
                               @if($hospedagem->checkout_at != null)
                                <div class="form-group col-sm-12 col-md-3 col-lg-3">  
                                <label class="control-label">{{ __('Data Check-Out') }}</label>
                                <input type="text" class="form-control boxed @error('datacheckout') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($hospedagem->checkout_at)->format('d-m-Y H:i:s') }}" name="datacheckout" id="datacheckout" required readonly autofocus onpaste="return false;">
                                @error('datacheckout')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                </div>     
                                @endif
                                                               

                                <div class="form-group col-sm-12 col-md-3 col-lg-3">  
                                <label class="control-label">{{ __('Celular ( WhatsApp )') }}</label>
                                <input type="text" class="form-control boxed @error('celular') is-invalid @enderror" value="{{ $hospedagem->user->telefone }}" data-mask="(00) 00000-0000" name="celular" id="celular" required readonly autofocus onpaste="return false;">
                                @error('celular')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                </div>   
                                
                                </div>

                          @endrole
                      @endif

              
  
                <div class="row has-error">    
                    
                      

                        @if($hospedagem->status == 4)
                         <div class="form-group col-sm-12 col-md-12 col-lg-6">


                            <a href="{{ route('documentos.verdocumento', ['id' => Crypt::encrypt($comprovante->id), 'doc' => Crypt::encrypt($comprovante->tipo_doc), 'tipo' => '3' ]) }}" target="_blank" class="btn btn-secondary btn-xl rounded-s" style="margin-top: 20px;">
                                    <i class="fas fa-address-card"></i>
                                    Ver Comprovante de Pagamento
                            </a> 



                            

                        </div>
                       
                        @endif
                </div>
                <div class="row has-error"> 
                        @if($hospedagem->status == 5 or $hospedagem->status == 4)
                        <div class="form-group col-sm-12 col-md-12 col-lg-12">
                           

                            <label class="control-label">{{ __('Anexar Comprovante de Pagamento') }}</label>          
                            <div class="custom-file">
                                  <input type="file" class="custom-file-input" name="documento" id="documento" accept=".jpg,.png,.pdf" name="documento">
                                  <label class="custom-file-label" for="customFile">Escolha o arquivo</label>                      
                            </div>
                            <p class=""> <font style="color: red; font-size: 16px;"> <b>Comprovante de Pagamento no formato .JPG, .PNG, .PDF até 4MB ou PRINT do arquivo PDF</b></font></p>
                            @error('documento')
                                <span class="has-error" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                      </div>
                      @endif
                 </div>


                

            <!-- COMEÇO MOTIVO-->
             @if($hospedagem->status == 10)
             <div class="form-group row">
                    <div class="form-group col-sm-12 col-md-3 col-lg-3">
                        <label class="control-label">Motivo</label>   
                    </div>           

                    <div class="form-group col-sm-12 col-md-9 col-lg-9">
                    

                    </div>  
            </div>
            @endif
            <!-- FIM MOTIVO-->

         </div>

            <!-- LINHA DOS BOTOES -->
              <div class="row has-error">
                    <div class="form-group col-sm-12 col-md-12 col-lg-12">
                        
                    
                            <!--
                             <a href="javascript:;" data-toggle="modal" onclick="aprovapedido('{{Crypt::encrypt($hospedagem->id)}}')" class="btn btn-primary" data-target="#AprovaModal" title="Aprovar Pedido">
                                    <i class="fas @endrolefa-check-square" style="color: #ffffff"></i> Liberar Acesso!</a>
                            -->
                                    
                                    @if($hospedagem->status == 0)                             

                                    <a href="{{route ('hospede.meuspedidos') }}"  class="btn btn-danger" title="Voltar">
                                    <i class="fas fa-angle-left" ></i> Voltar </a>
                                     @role('administrador_geral')
                                     <a href="{{ route('envia.mail.espera', ['id' => Crypt::encrypt($hospedagem->id)])  }}" class="btn btn-success" style="color: white;" id="enviar_mensagem" title="Enviar Mensagem">
                                    <i class="fas fa-envelope" style="color: white;" ></i> Fila de Espera </a>
                                    @endrole
                                    @endif

                                    @if($hospedagem->status == 3 or $hospedagem->status == 5)
                                    
                                     <a class="btn btn-success" style="color: white;" id="realizarpagamento" target="_blank" title="Realizar Pagamento" onClick="myFunction()">
                                    <i class="fas fa-money-bill-alt" style="color: white;" ></i> Realizar Pagamento de 01 diária </a>
                                  
                                    @endif
                                  
                                    @if($hospedagem->status == 2)
                                    @if($hospedagem->checkin == 1)
                                    @if($valorPagarRestante > 0)
                                    @role('atendente|administrador_geral|auxiliar_administrador_geral')
                                    <a class="btn btn-success" style="color: white;" target="_blank" title="Realizar Pagamento Restante" onClick="myFunction2()">
                                    <i class="fas fa-money-bill-alt" style="color: white;" ></i> Realizar Pagamento Restante </a>
                                    @endrole
                                    @endif
                                    @endif
                                    @endif

                                    
                                    
                                    

                                    @if($hospedagem->status == 5 or $hospedagem->status == 4)

                                    <button type="submit" id="btnupload" class="btn btn-primary">
                                        <i class="fas fa-check-circle fa-sm"></i>  
                                            Fazer Upload!
                                    </button>
                                    <!--
                                    <a class="btn btn-success" href="{{ route('pagamento.consulta', ['id' => Crypt::encrypt($hospedagem->id)]) }}"  style="color: white;" target="_blank" title="Realizar Pagamento">
                                    <i class="fas fa-money-bill-alt" style="color: white;" ></i> Consultar Pagamento</a>
                                    -->
                                    @endif


                                    




                              



                                    @if($hospedagem->status == 2 or $hospedagem->status == 3 or $hospedagem->status == 5 or $hospedagem->status == 7 )
                                    @if($hospedagem->checkin == null)

                                    <a href="javascript:;" data-toggle="modal" onclick="CancelarReserva('{{ Crypt::encrypt($hospedagem->id) }}')" data-target="#ModalCancelar" style="background-color: red" class="btn btn-danger" title="Cancelar Reserva">
                                            <i class="fas fa-bed" ></i> Cancelar Reserva </a> 

                                    @endif
                                    @endif







                                    @if($hospedagem->status == 2)
                                      @if($hospedagem->checkin == null)
                                        @role('atendente|administrador_geral|auxiliar_administrador_geral')
                                      <a href="javascript:;" data-toggle="modal" onclick="checkin('{{ Crypt::encrypt($hospedagem->id) }}')" data-target="#checkinModal" style="background-color: green" class="btn btn-sucess" title="Check-In">
                                            <i class="fas fa-thin fa-check" ></i> Realizar Check-In </a>
                                        @endrole
                                      @endif
                                    @endif


                                    @if($hospedagem->checkin == 1)
                                      @role('atendente|administrador_geral|auxiliar_administrador_geral')


                                      <a href="{{ route('adicionar.produto.hospedagem', ['id' => Crypt::encrypt($hospedagem->id)]) }}" class="btn btn-success" title="Adicionar" style="color: white;">
                                            <i class="fas fa-plus" style="color: #fff;"></i> Adicionar Item Carrinho</a> 


                                      <a href="javascript:;" data-toggle="modal" onclick="checkout('{{ Crypt::encrypt($hospedagem->id) }}', '{{ Crypt::encrypt($hospedagem->user_id) }}')" data-target="#checkoutModal" style="background-color: green" class="btn btn-sucess" title="Check-Out" id="checkout22">
                                            <i class="fas fa-thin fa-check" ></i> Realizar Check-Out </a> 



                                            <a class="btn btn-success" data-toggle="modal" data-target="#checkoutModal" style="background-color: green; color: #fff;" title="Realizar Pagamento" id="checkout11" title="Check-Out" onclick="checkout('{{ Crypt::encrypt($hospedagem->id) }}', '{{ Crypt::encrypt($hospedagem->user_id) }}')">
                                      <i class="fas fa-thin fa-check" ></i> Realizar Check-Out </a> 

                                      
                                    @endrole
                                    @endif
                            




                        </div>
                    </div>
                    <!-- FIM LINHA DOS BOTOES -->


            
                
            <!-- Final do Print -->
            </div>

           
                             
                 

                    <hr>
                    




                </form>
             
             
                
                <p></p>
                <p></p>
                <p></p>
                <p></p>
                <p></p>
                <p>&nbsp;</p>
            </div>



                            <!-- The Modal -->
<div id="myModal" class="modals">

  <!-- The Close Button -->
  <span class="close">&times;</span>

  <!-- Modal Content (The Image) -->
  <img class="modals-content" id="img01">

  <!-- Modal Caption (Image Text) -->
  <div id="caption"></div>
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

                                <div class="modal fade" id="checkinModal">
                                    <div class="modal-dialog" role="document">
                                        <form action="" id="checkin" method="get">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><i class="fa fa-warning"></i> Atenção</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}  
                                                    {{ method_field('DELETE') }}
                                                    <p>Deseja realizar Check-in deste Hospede?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="formSubmit1()">Sim</button>
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

                                <div class="modal fade" id="checkoutModal">
                                    <div class="modal-dialog" role="document">
                                        <form action="" id="checkout" method="get">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><i class="fa fa-warning"></i> Atenção</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}  
                                                    {{ method_field('DELETE') }}
                                                    <p>Deseja realizar Check-Out deste Hospede?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="formSubmit2()">Sim</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="modal fade" id="ModalCancelar">
                                    <div class="modal-dialog" role="document">
                                        <form action="" id="cancelar" method="get">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><i class="fa fa-warning"></i> Atenção</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}  
                                                    {{ method_field('DELETE') }}
                                                    <p >Antes de cancelar sua reserva por motivo de <b style="color: red;">mudança de período ou Unidade Habitacional,<b> consulte a Seção FML para os ajustes necessários.</p><br><br>
                                                      <p style="color: red;"> Caso efetue o cancelamento da reserva aprovada e fizer nova soicitação, terá de pagar nova diária para aprovação.</p>
                                                    
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="formSubmitCancelar()">Sim</button>
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

$(document).ready(function(){

    $(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
    //$('#checkout11').hide();
    
});

    function btnUpload(){
    
    window.location.reload();
    document.getElementById('btnupload').disabled = "";
    
    }   

    
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

    function checkin(id)
    {
         var id = id;
         var url = '{{ route("hospede.checkin", ":id") }}';
         url = url.replace(':id', id);
         $("#checkin").attr('action', url);
    }

    function formSubmit1()
    {
         $("#checkin").submit();
    }


    function checkout(id, hospede)
    {
        var id = id;
        var hospede = hospede;

        var url = '{{ route("hospede.checkout", [ "id" => ":id", "hospede" => ":hospede" ]) }}';
        url = url.replace(':id', id);
        url = url.replace(':hospede', hospede);
        $("#checkout").attr('action', url); 
       
    }

    function formSubmit2()
    {
         $("#checkout").submit();
    }



    function CancelarReserva(id)
    {
         var id = id;
         var url = '{{ route("cancelar.hospedagem", ":id") }}';
         url = url.replace(':id', id);
         $("#cancelar").attr('action', url);
    }

    function formSubmitCancelar()
    {
         $("#cancelar").submit();
    }



    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img = document.getElementById("myImg");
    var modalImg = document.getElementById("img01");
    var captionText = document.getElementById("caption");
    img.onclick = function(){
    modal.style.display = "block";
    modalImg.src = this.src;
    captionText.innerHTML = this.alt;
    }

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
} 
</script>
<script>
let janelaPagamentoInicial = null;
let monitorPagamentoInicial = null;

function myFunction() {
    const urlPagamento = @json(route(
        'pagamento.processaRequisicao',
        ['id' => Crypt::encrypt($hospedagem->id)]
    ));

    const urlStatus = @json(route(
        'pagamento.inicial.status',
        ['id' => Crypt::encrypt($hospedagem->id)]
    ));

    janelaPagamentoInicial = window.open(
        urlPagamento,
        'pagamentoInicialPagTesouro',
        'width=760,height=760,scrollbars=yes,resizable=yes'
    );

    if (!janelaPagamentoInicial) {
        alert(
            'O navegador bloqueou a janela. Permita pop-ups e tente novamente.'
        );

        return;
    }

    janelaPagamentoInicial.focus();

    let consultaEmAndamento = false;

    const consultarPagamentoInicial = async function () {
        if (consultaEmAndamento) {
            return;
        }

        consultaEmAndamento = true;

        try {
            const resposta = await fetch(urlStatus, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (!resposta.ok) {
                throw new Error(
                    'Erro HTTP ' + resposta.status
                );
            }

            const dados = await resposta.json();

            if (dados.pagamento_confirmado === true) {
                clearInterval(monitorPagamentoInicial);

                if (
                    janelaPagamentoInicial &&
                    !janelaPagamentoInicial.closed
                ) {
                    janelaPagamentoInicial.close();
                }

                sessionStorage.setItem(
                    'pagamento_confirmado',
                    'Pagamento da diária inicial realizado com sucesso!'
                );

                window.location.reload();
            }
        } catch (erro) {
            console.error(
                'Erro ao consultar pagamento inicial:',
                erro
            );
        } finally {
            consultaEmAndamento = false;
        }
    };

    consultarPagamentoInicial();

    monitorPagamentoInicial = setInterval(
        consultarPagamentoInicial,
        5000
    );
}
function myFunction2() {
  window.open("{{ route('pagamento.processaPagamentoRestante', ['id' => Crypt::encrypt($hospedagem->id), 'restante' => $valorPagarRestante]) }}", "_blank", "status=no, location=no, menubar=no, fullscreen=no, toolbar=no,scrollbars=no,resizable=no,top=500,left=500,width=700,height=700");
 //$('#checkout11').show();
}




</script>
<script src="{{asset('lib/jquery-mask-plugin/dist/jquery.mask.min.js')}}"></script>

@endpush
@endsection