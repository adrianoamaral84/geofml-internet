
function validarUF(ufId, cidadeFieldName){
   
   //alert(cidadeFieldName);
    if(ufId) {
        $.ajax({
            url: $host+'/cascade/carregarCidades/'+ufId,
            type: "GET",
            dataType: "json",
            success:function(data) {
                
                $('select[name="'+cidadeFieldName+'"]').empty();
                var count = Object.keys(data).length;
               
                if(count > 0) {
                    var cidadeSelectedId = $('input:hidden[name='+cidadeFieldName+']').val();
                    $('select[name="'+cidadeFieldName+'"]').append('<option value="" disabled selected>Cidade</option>');
                    for(var i = 0; i < count; i++) {
                        if(cidadeSelectedId == data[i].id) {
                             //alert(data[i]);
                            $('select[name="'+cidadeFieldName+'"]').append('<option value="'+ data[i].id +'" selected>'+ data[i].descricao +'</option>');
                        } else {
                            $('select[name="'+cidadeFieldName+'"]').append('<option value="'+ data[i].id +'">'+ data[i].descricao +'</option>');
                        
                        }
                    }
                } else {
                    $('select[name="'+cidadeFieldName+'"]').append('<option value="" disabled selected>Selecione</option>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert("Erro ao carregar cidades. Status: " + textStatus); alert("Error: " + errorThrown);
            }
        });
    }else{
        $('select[name="'+cidadeFieldName+'"]').empty();
    }
    
}

function validaOM(ufId, cidadeFieldName){
   
   //alert(cidadeFieldName);
    if(ufId) {
        $.ajax({
            url: $host+'/cascade/carregarOm/'+ufId,
            type: "GET",
            dataType: "json",
            success:function(data) {
                
                $('select[name="'+cidadeFieldName+'"]').empty();
                var count = Object.keys(data).length;
               
                if(count > 0) {
                    var cidadeSelectedId = $('input:hidden[name=oms]').val();
                    //var cidadeSelectedId = $('select[name='+cidadeFieldName+']').val();
                    //alert(cidadeSelectedId);
                    $('select[name="'+cidadeFieldName+'"]').append('<option value="" disabled selected>OM</option>');
                    for(var i = 0; i < count; i++) {
                        if(cidadeSelectedId == data[i].id) {
                             //alert(data[i]);
                            $('select[name="'+cidadeFieldName+'"]').append('<option value="'+ data[i].id +'" selected>'+ data[i].sigla +'</option>');
                        } else {
                            $('select[name="'+cidadeFieldName+'"]').append('<option value="'+ data[i].id +'">'+ data[i].sigla +'</option>');
                        
                        }
                    }
                } else {
                    $('select[name="'+cidadeFieldName+'"]').append('<option value="" disabled selected>Selecione</option>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert("Erro ao carregar cidades. Status: " + textStatus); alert("Error: " + errorThrown);
            }
        });
    }else{
        $('select[name="'+cidadeFieldName+'"]').empty();
    }
    
}

function validaPosto(ufId, cidadeFieldName){
   
   //alert(ufId);
    if(ufId) {
        $.ajax({
            url: $host+'/cascade/carregarPosto/'+ufId,
            type: "GET",
            dataType: "json",
            success:function(data) {
                
                $('select[name="'+cidadeFieldName+'"]').empty();
                var count = Object.keys(data).length;
               
                if(count > 0) {
                    var cidadeSelectedId = $('input:hidden[name=postos]').val();
                    //$('select[name="'+cidadeFieldName+'"]').append('<option value="">Posto</option>');
                    for(var i = 0; i < count; i++) {
                        if(cidadeSelectedId == data[i].id) {
                             //alert(data[i]);
                            $('select[name="'+cidadeFieldName+'"]').append('<option value="'+ data[i].id +'" selected>'+ data[i].sigla +'</option>');
                        } else {
                            $('select[name="'+cidadeFieldName+'"]').append('<option value="'+ data[i].id +'">'+ data[i].sigla +'</option>');
                        
                        }
                    }
                } else {
                    $('select[name="'+cidadeFieldName+'"]').append('<option value="" disabled selected>Selecione</option>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert("Erro ao carregar cidades. Status: " + textStatus); alert("Error: " + errorThrown);
            }
        });
    }else{
        $('select[name="'+cidadeFieldName+'"]').empty();
    }
    
}




// INICIA SCRIPT
$(document).ready(function(){

    $protocol = window.location.protocol;
    $host = $protocol+ '//'+$(location).attr('host')+'/GEOFML2.1/public';
    //alert($host);

    //Ocultar campos Situação
    hiddenFields();

    //////////////////////////////////////////////////
    /* UF */
    var ufEndId = $('select[name="uf"] option:selected').val();
    if (ufEndId != '') {
        validarUF(ufEndId, 'cidade');
    }
    
    
    if($('select[name="uf"] option:selected').val() != '') {
        var ufId = $('select[name="uf"] option:selected').val();
        //alert(ufId);
        validarUF(ufId, 'cidade');
    }
    

    $('select[name="uf"]').on('change', function() {
        var ufId = $(this).val();
        //$('#cidade').prop('readonly', null);
        validarUF(ufId, 'cidade');
    });
    /* */
    //////////////////////////////////////////////

    /* OM */
   var OMid = $('select[name="forca"] option:selected').val();
    if (OMid != '') {
        validaOM(OMid, 'om');
        validaPosto(OMid, 'posto');
    }
    
    
    if($('select[name="forca"] option:selected').val() != '') {
        var Om_id = $('select[name="forca"] option:selected').val();
        //alert(ufId);
        validaOM(Om_id, 'om');
        validaPosto(Om_id, 'posto');
    }
    

    $('select[name="forca"]').on('change', function() {
        var Om_id = $(this).val();
        //$('#cidade').prop('readonly', null);
        validaOM(Om_id, 'om');
        validaPosto(Om_id, 'posto');
    });
    /* */
    
    ///////////////////////////////////////////////////////////////
   
    if($('select[name="situacao"] option:selected').val() != '') {
        var situacao = $('select[name="situacao"] option:selected').val();
        changeFields(situacao);
        //validarUF(situacao, 'cidadeEndereco_id');
    }
    //Carregar Estados e campos de acordo com a situação do militar
    $('#situacao').on('change', ()=>{
        id_situacao = $('#situacao').val();
        changeFields(id_situacao);

});

//////////////////////////////////////////////////////////////////
    

});
// FINAL DE INICIA SCRIPT









function changeFields(id_situacao){
    //Mostrar/Ocultar campos de acordo com a situação

    if( id_situacao == 0 ){

    $('.milReserva').hide();
	$('.militarAtiva').hide();
	$('.siape').hide();
	$('.ForcaOmPosto').hide();
    $('.identidade').hide();


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
        $('.milReserva').hide();
        $('.siape').hide();
		

        //$('.milReserva').css('display', 'none');
       
        //Mostra Apenas data da ultima promoção e Posto Grad, Idt Mil
        $('.militarAtiva').show();
        $('.ForcaOmPosto').show();
        
        
        //$('.militarAtiva').css('display', 'block');
        //$('.ForcaOmPosto').css('display', 'block');
        
       
        militarAtiva();
    }

    //Militar da Reserva 
    else if(id_situacao == 2 ){
        //Oculta campos: Nível de Ensino, Matricula, RG, Ultima Promoção
        //$('.servidor-civil').css('display', 'none');
        
        //Mostra Campo PTTC
        //$('.milReserva').hide();
        $('.milReserva').show();
        $('.militarAtiva').show();
        $('.ForcaOmPosto').show();
		
		$('.siape').hide();
		

        /*
        $('.milReserva').css('display', 'block');
        $('.militarAtiva').css('display', 'block');
        $('.ForcaOmPosto').css('display', 'block');
        */

        militarReserva();
    }

    //Servidor Civil
    else if(id_situacao == 3){
        //Oculta campos: PTTC, Data Ultima Promoção, Posto/Grad, Id Militar
        /*
        $('.milReserva').css('display', 'none');
        $('.militarAtiva').css('display', 'none');
        $('.ForcaOmPosto').css('display', 'none');
        $('.siape').css('display', 'block');
		*/
      	$('.milReserva').hide();
        $('.siape').show();
		

        //$('.milReserva').css('display', 'none');
       
        //Mostra Apenas data da ultima promoção e Posto Grad, Idt Mil
        $('.militarAtiva').hide();
        $('.ForcaOmPosto').hide();
        servidorCivil();
    }

    //Pensionista
    else {
        //Oculta campos: PTTC, Data Ultima Promoção, Nivel de Ensino, Matricula, RG
        $('.milReserva').hide();
        $('.siape').hide();
		

        $('.militarAtiva').hide();
        $('.ForcaOmPosto').hide();
        /*
      	$('.milReserva').css('display', 'none');
        $('.militarAtiva').css('display', 'none');
        $('.ForcaOmPosto').css('display', 'none');
        $('.siape').css('display', 'none'); 
       */

        pensionista();
    }
}

function hiddenFields(){
	$('.milReserva').hide();
	$('.militarAtiva').hide();
	$('.siape').hide();
	$('.ForcaOmPosto').hide();
    $('.identidade').hide();
	
	$('#pttc').prop('required', null);
	$('#nomeguerra').prop('required', null);
	$('#dtUltPromo').prop('required', null);
	$('#forca').prop('required', null);
	$('#om').prop('required', null);
	$('#posto').prop('required', null);
	$('#siape').prop('required', null);
    $('#idtMil').prop('required', null);

    
	
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

function militarAtiva(){

    //Campos Obrigatórios
    $('#nomeguerra').attr('required', true);
    $('#dtUltPromo').attr('required', true);
    $('#forca').attr('required', true);
    $('#om').attr('required', true);
    $('#posto').attr('required', true);

    /*
    requiredField('#dtUltPromo', 'TRUE');
    requiredField('#nomeguerra', 'TRUE');
    requiredField('#forca', 'TRUE');
    requiredField('#om', 'TRUE');
    requiredField('#posto', 'TRUE');
  	*/
  	$('#pttc').prop('required', null);
	$('#siape').prop('required', null);

	/*
    //Campos Ocultos
    requiredField('#pttc', 'FALSE');
    requiredField('#siape', 'FALSE');
    */
}

function militarReserva(){
	/*
    //Campos Obrigatórios
    requiredField('#dtUltPromo', 'TRUE');
    requiredField('#nomeguerra', 'TRUE');
    requiredField('#forca', 'TRUE');
    requiredField('#om', 'TRUE');
    requiredField('#posto', 'TRUE');
    requiredField('#pttc', 'TRUE');
	*/
	$('#nomeguerra').attr('required', true);
    $('#dtUltPromo').attr('required', true);
    $('#forca').attr('required', true);
    $('#om').attr('required', true);
    $('#posto').attr('required', true);
    //$('#pttc').attr('required', true);

	$('#siape').prop('required', null);

    //Campos Não Obrigatórios
    //requiredField('#pttc', 'FALSE');
    
}

function pensionista(){
	/*
    //Campos Obrigatórios
    requiredField('#dtUltPromo', 'FALSE');
    requiredField('#nomeguerra', 'FALSE');
    requiredField('#forca', 'FALSE');
    requiredField('#om', 'FALSE');
    requiredField('#posto', 'FALSE');
    requiredField('#pttc', 'FALSE');
    requiredField('#siape', 'FALSE');
	*/

    //Campos Ocultos
    $('#pttc').prop('required', null);
	$('#nomeguerra').prop('required', null);
	$('#dtUltPromo').prop('required', null);
	$('#forca').prop('required', null);
	$('#om').prop('required', null);
	$('#posto').prop('required', null);
	$('#siape').prop('required', null);
}

function servidorCivil(){
    //Campos Obrigatórios
    //requiredField('#siape', 'TRUE');
	$('#siape').attr('required', true);
    
    /*
    requiredField('#dtUltPromo', 'FALSE');
    requiredField('#nomeguerra', 'FALSE');
    requiredField('#forca', 'FALSE');
    requiredField('#om', 'FALSE');
    requiredField('#posto', 'FALSE');
    requiredField('#pttc', 'FALSE');
	*/

    //requiredField('#forca', 'FALSE');
    //alert('campo');
    $('#pttc').prop('required', null);
    $('#nomeguerra').prop('required', null);
    $('#dtUltPromo').prop('required', null);
    $('#forca').prop('required', null);
    $('#om').prop('required', null);
    $('#posto').prop('required', null);
    //$('#siape').prop('required', null);
}
//requiredField('#campo', TRUE/FALSE)
function requiredField(field, state){
    input = $(field);

    if( state == 'TRUE' ){
        input.attr('required', true);
    }else {
        input.removeAttr('required');
    }
}