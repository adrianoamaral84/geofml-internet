function getUFGuarnicaoByArea($area_id){
    if($area_id) {
        $.ajax({
            url: $host+'/getUFGuarnicaoByArea/'+$area_id,
            type: "GET",
            dataType: "json",
            success:function(data) {
                $('select[name="estado_id"]').empty();
                var count = Object.keys(data).length;

                if(count > 0) {
                    $('select[name="estado_id"]').append('<option value="" disabled selected>Selecione</option>');
                    for(var i = 0; i < count; i++) {
                        $('select[name="estado_id"]').append('<option value="'+ data[i].id +'">'+ data[i].sigla +'</option>');
                    }
                } else {
                    $('select[name="estado_id"]').append('<option value="" disabled selected>Selecione</option>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert("Erro ao carregar estados. Status: " + textStatus); alert("Error: " + errorThrown);
            }
        });
    }else{
        $('select[name="estado_id"]').empty();
    }
}

function getCidadesGuarnicaoByUFId($estado_id, $area_id){
    if($estado_id && $area_id) {
        $.ajax({
            url: $host+'/getCidadesGuarnicaoByUFId/'+$estado_id+'/'+$area_id,
            type: "GET",
            dataType: "json",
            success:function(data) {
                $('select[name="guarnicao_id"]').empty();
                var count = Object.keys(data).length;

                if(count > 0) {
                    $('select[name="guarnicao_id"]').append('<option value="" disabled selected>Selecione</option>');
                    for(var i = 0; i < count; i++) {
                        $('select[name="guarnicao_id"]').append('<option value="'+ data[i].id +'">'+ data[i].nome +'</option>');
                    }
                } else {
                    $('select[name="guarnicao_id"]').append('<option value="" disabled selected>Selecione</option>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert("Erro ao carregar guarnições. Status: " + textStatus); alert("Error: " + errorThrown);
            }
        });
    }else{
        $('select[name="guarnicao_id"]').empty();
    }
}

$(document).ready(function() {
    $protocol = window.location.protocol;
    $host = $protocol+ '//'+$(location).attr('host')+'/sismilAdmin2020';

    $("#estado_id").empty();
    $("#estado_id").append('<option value="" disabled selected>Selecione</option>');
    $("#estado_id").prop('disabled', true);

    $("#guarnicao_id").empty();
    $("#guarnicao_id").append('<option value="" disabled selected>Selecione</option>');
    $("#guarnicao_id").prop('disabled', true);

    // Áreas
    if($('select[name="area_id"] option:selected').val() != '') {
        $area_id = $('select[name="area_id"] option:selected').val();
        getUFGuarnicaoByArea($area_id);
        $("#estado_id").prop('disabled', false);
    }

    $('select[name="area_id"]').on('change', function() {
        $area_id = $(this).val();
        getUFGuarnicaoByArea($area_id);
        $("#estado_id").prop('disabled', false);

        $("#guarnicao_id").empty();
        $("#guarnicao_id").append('<option value="" disabled selected>Selecione</option>');
        $("#guarnicao_id").prop('disabled', true);
    }); 

    // Estado
    if($('select[name="estado_id"] option:selected').val() != '') {
        $estado_id = $('select[name="estado_id"] option:selected').val();
        $area_id = $('select[name="area_id"] option:selected').val();
        getCidadesGuarnicaoByUFId($estado_id, $area_id);
        $("#guarnicao_id").prop('disabled', false);
    }

    $('select[name="estado_id"]').on('change', function() {
        $estado_id = $(this).val();
        $area_id = $('select[name="area_id"] option:selected').val();
        getCidadesGuarnicaoByUFId($estado_id, $area_id);
        $("#guarnicao_id").prop('disabled', false);
    }); 
});
