function carregarAreas($processo_id){
    if($processo_id) {
        $.ajax({
            url: $host+'/getAreasByProcesso/'+$processo_id,
            type: "GET",
            dataType: "json",
            success:function(data) {
                $('select[name="area_id"]').empty();
                var count = Object.keys(data).length;

                if(count > 0) {
                    $('select[name="area_id"]').append('<option value="" disabled selected>Selecione</option>');
                    for(var i = 0; i < count; i++) {
                        $('select[name="area_id"]').append('<option value="'+ data[i].id +'">'+ data[i].nome +'</option>');
                    }
                } else {
                    $('select[name="area_id"]').append('<option value="" disabled selected>Selecione</option>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert("Erro ao carregar áreas. Status: " + textStatus); alert("Error: " + errorThrown);
            }
        });
    }else{
        $('select[name="area_id"]').empty();
    }
}

function carregarSubAreas($area_id){
    if($area_id) {
        $.ajax({
            url: $host+'/getSubareasByArea/'+$area_id,
            type: "GET",
            dataType: "json",
            success:function(data) {
                $('select[name="subarea_id"]').empty();
                var count = Object.keys(data).length;

                if(count > 0) {
                    $('select[name="subarea_id"]').append('<option value="" selected>Selecione</option>');
                    for(var i = 0; i < count; i++) {
                        $('select[name="subarea_id"]').append('<option value="'+ data[i].id +'">'+ data[i].nome +'</option>');
                    }
                } else {
                    $('select[name="subarea_id"]').append('<option value="" selected>Selecione</option>');
                    $("#subarea_id").prop('disabled', true);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert("Erro ao carregar subáreas. Status: " + textStatus); alert("Error: " + errorThrown);
            }
        });
    }else{
        $('select[name="subarea_id"]').empty();
        $("#subarea_id").prop('disabled', true);
    }
}

$(document).ready(function() {
    $protocol = window.location.protocol;
    $host = $protocol+ '//'+$(location).attr('host')+'/sismil/www/sismilAdmin2020';

    // Processos
    $processo_id = $("#processo_id").val();
    carregarAreas($processo_id);
    $("#subarea_id").empty();
    $("#subarea_id").append('<option value="" disabled selected>Selecione</option>');
    $("#subarea_id").prop('disabled', true);

    $('select[name="processo_id"]').on('change', function() {
        $processo_id = $(this).val();
        carregarAreas($processo_id);

        $("#subarea_id").empty();
        $("#subarea_id").append('<option value="" disabled selected>Selecione</option>');
        $("#subarea_id").prop('disabled', true);
    }); 

    // Áreas
    if($('select[name="area_id"] option:selected').val() != '') {
        $area_id = $('select[name="area_id"] option:selected').val();
        carregarSubAreas($area_id);
        $("#subarea_id").prop('disabled', false);
    }

    $('select[name="area_id"]').on('change', function() {
        $area_id = $(this).val();
        carregarSubAreas($area_id);
        $("#subarea_id").prop('disabled', false);
    }); 
});
