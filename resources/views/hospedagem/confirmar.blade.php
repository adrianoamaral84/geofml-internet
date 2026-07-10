@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<article class="items-list-page">
    <div class="title-search-block">
        <div class="title-block">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="title"> Confirmar dados da Inscrição </h3>
                     <small>Confirme os dados antes de continuar.</small>
                </div>
            </div>
        </div>
        <div class="items-search">
            
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-block">
                        <section class="example">
                            <div class="table-flip-scroll">
                                <form id="password-form" action="{{ route('hospede.store') }}" method="POST">
                                    @csrf
                                 
        <div class="row has-error">
            <div class="form-group col-sm-12 col-md-12 col-lg-12">
                            <label class="control-label">{{ __('Tipo Unidade Habitacional') }}</label>
               
                                <select name="tipo" id="tipo" required class="custom-select mr-sm-2 @error('tipo') is-invalid @enderror" readonly="readonly" autocomplete="off">
                                     <option value="">Selecione</option>
                                
                                    @foreach($tipos as $tipo)

                                         <option value="{{$tipo->id}}" @if($consulta->tipo == $tipo->id)selected @endif>{{$tipo->descricao}}</option>

                                    @endforeach
                            </select>
                            @error('tipo')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
            </div>



           
           
        </div>

        <div class="row has-error">

            <div class="form-group col-sm-4 col-md-12 col-lg-12">
                 
            </div>
         </div>
        <div class="row form-group has-error">
            
             <div class="form-group col-sm-4 col-md-12 col-lg-6">
               
                 <label class="control-label">{{ __('Período Entrada') }}</label>
                

                 
                 <input type="text" name="peridoinicial" id="peridoinicial" value="{{ $consulta->peridoinicial }}" class="form-control boxed @error('peridoinicial') is-invalid @enderror" readonly="" required onpaste="return false;" style="touch-action: none; pointer-events: none;"> 
                
                 <small>Período máximo é de 10 dias!</small>
                            @error('peridoinicial')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

            </div>


            <div class="form-group col-sm-4 col-md-12 col-lg-6">
               
                 <label class="control-label">{{ __('Período Saída') }}</label>
                

                 
                  
                 <input type="text" name="final" id="final" class="form-control boxed @error('final') is-invalid @enderror" readonly="" value="{{ $consulta->final }}" onpaste="return false;" style="touch-action: none; pointer-events: none;" >
                 <small>Período máximo é de 10 dias!</small>
                            @error('final')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

            </div>


       
        </div>

         <div class="row form-group has-error">
            
             <div class="form-group col-sm-4 col-md-12 col-lg-6">
               
                 <label class="control-label">{{ __('Diárias') }}</label>
                
                
                 <input type="text" name="diarias" id="diarias" class="form-control boxed @error('diarias') is-invalid @enderror" min="" value="{{ $diasHospedagem }}" readonly="" required/>
                 <small></small>
                            @error('diarias')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

            </div>

                  <div class="form-group col-sm-4 col-md-12 col-lg-6">
               
                 <label class="control-label">{{ __('Valor') }}</label>
                
                 <input type="text" name="valor" id="valor" class="form-control boxed @error('valor') is-invalid @enderror" min="" value="{{ number_format($totalValor, 2, ',', '.') }}" readonly="" required/>
                 <small></small>
                            @error('valor')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

            </div>
        </div>
       
        

        <div class="row form-group has-error">

             <div class="form-group col-sm-12 col-md-12 col-lg-6">
                            <label class="control-label">{{ __('Adultos') }}</label>
                            <input type="text" class="form-control boxed @error('adultos') is-invalid @enderror" value="{{ $consulta->adultos }}" name="adultos" id="adultos" required maxlength="10" readonly="" style="touch-action: none; pointer-events: none;">
                            @error('adultos')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
            </div>

            <div class="form-group col-sm-12 col-md-12 col-lg-6">
                            <label class="control-label">{{ __('Crianças') }}</label>
                            <input type="text" class="form-control boxed @error('criancas') is-invalid @enderror" value="{{ $consulta->criancas }}" name="criancas" id="criancas" autofocus required maxlength="10" readonly="" style="touch-action: none; pointer-events: none;">
                            @error('criancas')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
            </div>
            
            

            


        </div>

     

        <div class="row has-error">
            
            <div class="form-group col-sm-12 col-md-12 col-lg-12">
                            <label class="control-label">{{ __('Hospedará Portador de Necessidade Especial (PNE)?') }}</label>
                
                                <select name="pne" id="pne" required class="custom-select mr-sm-2 @error('pne') is-invalid @enderror" readonly autocomplete="off">
                                     <option value="">Selecione Sala</option>
                                        @if($consulta->pne == 1) 
                                            <option value="1" selected="">Sim</option>
                                            <option value="0">Não</option>
                                        @else
                                        <option value="1">Sim</option>
                                        <option value="0" selected="">Não</option>
                                        @endif
                            </select>
                            @error('pne')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
            </div>
           </div>
            
             <div class="row has-error">
            
            <div class="form-group col-sm-12 col-md-12 col-lg-12">
                            <label class="control-label">{{ __('Hospedará PET?') }}</label>
                
                                <select name="pet" id="pet" required class="custom-select mr-sm-2 @error('pet') is-invalid @enderror" readonly autocomplete="off">
                                     <option value="">Selecione Sala</option>
                                        @if($consulta->pet == 1) 
                                        <option value="1" selected="">Sim</option>
                                        <option value="0">Não</option>
                                        
                                        @else
                                        <option value="1">Sim</option>
                                        <option value="0" selected="">Não</option>
                                        
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
            <div class="form-group col-sm-12 col-md-12 col-lg-12">
                            <label class="control-label">{{ __('Observação') }}</label>
                            <input type="text" class="form-control boxed @error('observacao') is-invalid @enderror" value="{{ $consulta->observacao }}" readonly="" name="observacao" id="observacao" autofocus maxlength="50" onpaste="return false;">
                            @error('observacao')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
            </div>

<i class="fa-solid fa-angle-right"></i>

        </div>

                                    <hr>
                                    <div class="form-group row">
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-s"><i class="fas fa-check-circle btn-sm "></i>d Confirmar Pedido! </button>
                                        </div>
                                    </div>
                                </form>
                               
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-10 col-sm-offset-2">
                                   
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </section>
</article>
@push('javascript')
<!--
<script src="{{ asset('daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>

<script>


   
  
  $('#peridoinicial').daterangepicker({
    "minYear": 2021,
    "maxYear": 2022,
    "maxSpan": {
        "days": 10
    },
    "locale": {
        "format": "DD/MM/YYYY",
        "separator": " - ",
        "applyLabel": "Aplicar",
        "cancelLabel": "Cancelar",
        "fromLabel": "De",
        "toLabel": "Para",
        "customRangeLabel": "Custom",
        "weekLabel": "W",
        "daysOfWeek": [
            "DOM",
            "SEG",
            "TER",
            "QUA",
            "QUI",
            "SEX",
            "SAB"
        ],
        "monthNames": [
            "Janeiro",
            "Fevereiro",
            "Março",
            "Abril",
            "Maio",
            "Junho",
            "Julho",
            "Agosto",
            "Setembro",
            "Outubro",
            "Novembro",
            "Dezembro"
        ],
        "firstDay": 1
    },
    
   
    "opens": "center",
    "drops": "auto"
});

 

  

</script>
-->

@endpush
@endsection