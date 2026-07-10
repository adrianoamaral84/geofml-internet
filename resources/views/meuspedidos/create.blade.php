@extends('layouts.app')

@section('content')
<article class="items-list-page">
    <div class="title-search-block">
        <div class="title-block">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="title"> Cadastra o período a ser Bloqueado </h3>
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
                                <form id="password-form" action="{{ route('configurarhospedagem.store') }}" method="POST">
                                    @csrf
                                 

                                    <div class="row form-group has-error">
                                        <div class="form-group col-sm-12 col-md-12 col-lg-6">
                                             <label class="control-label">{{ __('Motivo') }}</label>
                
                               
                               <input type="text" name="motivo" id="motivo" class="form-control boxed" value="{{ old('motivo') }}" maxlength="100" minlength="2" onpaste="return false;" required="" autofocus>
                            @error('motivo')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                                        </div>
                                
                        

                        <div class="form-group col-sm-12 col-md-12 col-lg-6">
                            <label class="control-label">{{ __('Tipo') }}</label>
                
                                <select name="situacao" id="situacao" required class="custom-select mr-sm-2 @error('situacao') is-invalid @enderror" onpaste="return false;">
                                     <option value="">Selecione Tipo</option>
                                
                                    

                                         <option value="1">Período</option>
                                         <option value="2">Data</option>

                                  

                            </select>
                            @error('situacao')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                             <div class="row form-group has-error">

                                        <div class="form-group col-sm-12 col-md-12 col-lg-6 dataini">
                                            <label class="control-label" id="texto1">Data Início</label>
                                            <input type="date" name="dataini" id="dataini" class="form-control boxed" value="{{ old('dataini') }}" maxlength="50" minlength="2" onpaste="return false;" required="" autofocus>
                                            <small> Se for um único dia coloca a mesma data nos dois campos.</small>
                                            @error('dataini')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror 
                                        </div>
                                    


                                           <div class="form-group col-sm-12 col-md-12 col-lg-6 datater">
                                            <label class="control-label" id="texto2">Data Término</label>
                                            <input type="date" name="datater" id="datater" class="form-control boxed" value="{{ old('datater') }}" maxlength="50" minlength="2" onpaste="return false;" required="" autofocus>
                                            @error('datater')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                                        </div>


                                    </div>

                                    <hr>
                                    <div class="form-group row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-s"><i class="fas fa-check-circle btn-sm fa-sm"></i>  Cadastrar </button>
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
<script>
    function hiddenFields(){
       

    $('.dataini').hide();
    $('.datater').hide();
   

    
    $('#dataini').prop('required', null);
    //$('#nomeguerra').prop('required', null);
    $('#datater').prop('required', null);
   
    
    //$('#cidade').prop('readonly', true);
    //$('#idtMil').prop('required', null);
    //$('#forca_id').prop('required', null);
    /*  
    $('.milReserva').css('display', 'none');
    $('.militarAtiva').css('display', 'none');
    $('.siape').css('display', 'none');
    $('.ForcaOmPosto').css('display', 'none');
    $('.siape').css('display', 'none');
    */
 
    //Não tornar obrigatório os campos ocultos
    /*
    requiredField('#pttc', 'FALSE');
    requiredField('#nomeguerra', 'FALSE');
    requiredField('#dtUltPromo', 'FALSE');
    requiredField('#forca', 'FALSE');
    requiredField('#om', 'FALSE');
    requiredField('#posto', 'FALSE');
    requiredField('#siape', 'FALSE');
   */
}

function changeFields(id_situacao){
    //Mostrar/Ocultar campos de acordo com a situação

    if( id_situacao == 0 ){

    $('.dataini').hide();
    $('.datater').hide();



    /*
        $('.milReserva').css('display', 'none');
        $('.militarAtiva').css('display', 'none');
        $('.ForcaOmPosto').css('display', 'none');
        $('.siape').css('display', 'none');
    */  
    }
    //Militar da Ativa
    else if( id_situacao == 1 ){
        //Oculta campos: PTTC, Nível de Ensino, Matricula, RG
        
        $('.dataini').show();
        $('.datater').show();
        $("#texto1").text(' Data Início'); 
        $("#texto2").text(' Data Final'); 
        //$("#datainis").css("d-none","");
        //alert('okk');
        //$('.dataini').css('d-none', 'none');
        //$('.ForcaOmPosto').css('display', 'block');
        
       periodo();
    }

    //Militar da Reserva 
    else if(id_situacao == 2 ){
        //Oculta campos: Nível de Ensino, Matricula, RG, Ultima Promoção
        //$('.servidor-civil').css('display', 'none');
        
        //Mostra Campo PTTC
        //$('.milReserva').hide();
        $('.dataini').show();
        $('.datater').hide();
        $("#texto1").text('Data'); 


        data();
    }

   
}

function periodo(){
    //Campos Obrigatórios
    //requiredField('#siape', 'TRUE');
    $('#dataini').attr('required', true);
    $('#datater').attr('required', true);
  
}
function data(){
    //Campos Obrigatórios
    //requiredField('#siape', 'TRUE');
    $('#dataini').attr('required', true);
    $('#datater').prop('required', null);
}

$(document).ready(function(){

     hiddenFields();
    

     if($('select[name="situacao"] option:selected').val() != '') {
        var situacao = $('select[name="situacao"] option:selected').val();
        //changeFields(situacao);
        //validarUF(situacao, 'cidadeEndereco_id');
        //alert(situacao);
        //validaPostoSituacao(situacao, 'posto');
    }
    



    $('#situacao').on('change', ()=>{
        id_situacao = $('#situacao').val();
        changeFields(id_situacao);
        //alert(id_situacao);
        //validaPostoSituacao(situacao, 'posto');

    });

});
















</script>
@endpush
@endsection