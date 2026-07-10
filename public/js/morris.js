function getQtdeInscritos() {
    $.ajax({
        url: $host+'/qtdeInscritosChart/',
        type: "GET",
        dataType: "json",
        success:function(data) {
            $("#morris-qtdeInscritos-chart").empty();
            var count = Object.keys(data).length;

            if(count > 0) {
                Morris.Donut({
                    element:"morris-qtdeInscritos-chart",
                    data:[
                        {label: data[0].sigla+'|'+data[0].descricao ,value:data[0].inscricoes_count},
                        {label: data[1].sigla+'|'+data[1].descricao ,value:data[1].inscricoes_count},
                        {label: data[2].sigla+'|'+data[2].descricao ,value:data[2].inscricoes_count}
                    ],
                    resize:!0,
                    colors:[
                        tinycolor(config.chart.colorPrimary.toString()).lighten(10).toString(),tinycolor(config.chart.colorPrimary.toString()).darken(10).toString(),config.chart.colorPrimary.toString()
                    ]
                });
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("Erro ao carregar gráfico de qtde de inscritos. Status: " + textStatus); alert("Error: " + errorThrown); 
        }
    });
}

function getQtdeFichasAvaliadas() {
    $.ajax({
        url: $host+'/qtdeFichasAvaliadasChart/',
        type: "GET",
        dataType: "json",
        success:function(data) {
            $("#morris-qtdeFichasAvaliadasChart-chart").empty();
            var count = Object.keys(data).length;

            if(count > 0) {
                Morris.Bar({
                    element:"morris-qtdeFichasAvaliadasChart-chart",
                    data:[
                        {y:data[0].sigla,a:data[0].avaliado,b:data[0].naoAvaliado},
                        {y:data[1].sigla,a:data[1].avalido,b:data[1].naoAvaliado},
                        {y:data[2].sigla,a:data[2].avaliado,b:data[2].naoAvaliado}
                    ],
                    xkey:"y",
                    ykeys:["a","b"],
                    labels:["Avaliadas","Não Avaliadas"],
                    hideHover:"auto",
                    resize:!0,barColors:[config.chart.colorPrimary.toString(),tinycolor(config.chart.colorPrimary.toString()).darken(30).toString()]
                });
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("Erro ao carregar gráfico de qtde de inscritos. Status: " + textStatus); alert("Error: " + errorThrown); 
        }
    });
}

function getQtdePorDia() {
    $.ajax({
        url: $host+'/getQtdePorDia/',
        type: "GET",
        dataType: "json",
        success:function(data) {
            $("#morris-getQtdePorDia-chart").empty();
            var count = Object.keys(data).length;

            if(count > 0) {
                $chart = Morris.Line({
                    element:"morris-getQtdePorDia-chart",
                    data:[],
                    xkey:"period",ykeys:["value"],resize:!0,lineWidth:4,labels:["Qtde"],lineColors:[config.chart.colorPrimary.toString()],pointSize:5
                });

                $data = [];

                for(var i = 0; i < count; i++) {
                    $data.push({'period': data[i].date, 'value': data[i].qtde});
                }

                $chart.setData($data);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("Erro ao carregar gráfico de qtde de inscritos. Status: " + textStatus); alert("Error: " + errorThrown); 
        }
    });
}

$(document).ready(function() {
    $protocol = window.location.protocol;
    $host = $protocol + '//'+$(location).attr('host')+'/sismilAdmin2020';

    getQtdeInscritos();
    getQtdeFichasAvaliadas();
    getQtdePorDia();
});
