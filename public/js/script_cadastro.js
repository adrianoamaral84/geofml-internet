var $host = '';

/*
|--------------------------------------------------------------------------
| UF → CIDADES
|--------------------------------------------------------------------------
*/

function validarUF(ufId) {
    var $cidade = $('#cidade');
    var $om = $('#om');

    $cidade
        .prop('disabled', true)
        .empty()
        .append('<option value="">Carregando cidades...</option>');

    $om
        .prop('disabled', true)
        .empty()
        .append('<option value="">Selecione primeiro uma cidade</option>');

    if (!ufId) {
        $cidade
            .prop('disabled', false)
            .empty()
            .append('<option value="">Selecione Cidade</option>');

        return;
    }

    $.ajax({
        url: $host + '/cascade/carregarCidades/' + ufId,
        type: 'GET',
        dataType: 'json',

        success: function (data) {
            var cidadeSelecionada = $('#cidade_selecionada').val();

            $cidade
                .prop('disabled', false)
                .empty()
                .append('<option value="">Selecione Cidade</option>');

            $.each(data, function (index, cidade) {
                var selected = '';

                if (
                    cidadeSelecionada &&
                    String(cidadeSelecionada) === String(cidade.id)
                ) {
                    selected = ' selected';
                }

                $cidade.append(
                    '<option value="' + cidade.id + '"' + selected + '>' +
                        cidade.descricao +
                    '</option>'
                );
            });

            /*
             * Na edição, se o usuário já possui uma cidade,
             * carrega automaticamente as OMs daquela cidade.
             */
            if (cidadeSelecionada) {
                validaOM(cidadeSelecionada);
            }
        },

        error: function (xhr) {
            console.error(
                'Erro ao carregar cidades:',
                xhr.status,
                xhr.responseText
            );

            $cidade
                .prop('disabled', false)
                .empty()
                .append('<option value="">Erro ao carregar cidades</option>');
        }
    });
}


/*
|--------------------------------------------------------------------------
| CIDADE → ORGANIZAÇÕES MILITARES
|--------------------------------------------------------------------------
*/

function validaOM(cidadeId) {
    var $om = $('#om');

    $om
        .prop('disabled', true)
        .empty()
        .append('<option value="">Carregando OMs...</option>');

    if (!cidadeId) {
        $om
            .prop('disabled', false)
            .empty()
            .append('<option value="">Selecione primeiro uma cidade</option>');

        return;
    }

    $.ajax({
        url: $host + '/cascade/carregarOm/' + cidadeId,
        type: 'GET',
        dataType: 'json',

        success: function (data) {
            var omSelecionada = $('#om_selecionada').val();

            $om
                .prop('disabled', false)
                .empty()
                .append('<option value="">Selecione OM</option>');

            $.each(data, function (index, om) {
                var selected = '';

                if (
                    omSelecionada &&
                    String(omSelecionada) === String(om.id)
                ) {
                    selected = ' selected';
                }

                $om.append(
                    '<option value="' + om.id + '"' + selected + '>' +
                        om.sigla +
                    '</option>'
                );
            });

            if (!data || data.length === 0) {
                $om
                    .empty()
                    .append('<option value="">Nenhuma OM encontrada</option>');
            }
        },

        error: function (xhr) {
            console.error(
                'Erro ao carregar OMs:',
                xhr.status,
                xhr.responseText
            );

            $om
                .prop('disabled', false)
                .empty()
                .append('<option value="">Erro ao carregar OMs</option>');
        }
    });
}


/*
|--------------------------------------------------------------------------
| GRUPO DE DESTINAÇÃO → UNIDADES
|--------------------------------------------------------------------------
*/

function validaGrupo(grupoId, campoDestino) {
    var $campo = $('select[name="' + campoDestino + '"]');

    if (!$campo.length) {
        return;
    }

    $campo
        .empty()
        .append('<option value="">Carregando...</option>');

    if (!grupoId) {
        $campo
            .empty()
            .append('<option value="">Selecione</option>');

        return;
    }

    $.ajax({
        url: $host + '/cascade/carregarUnidades/' + grupoId,
        type: 'GET',
        dataType: 'json',

        success: function (data) {
            var unidadeSelecionada = $(
                '#unidade_selecionada'
            ).val();

            $campo
                .empty()
                .append('<option value="">Selecione</option>');

            $.each(data, function (index, unidade) {
                var selected = '';

                if (
                    unidadeSelecionada &&
                    String(unidadeSelecionada) === String(unidade.id)
                ) {
                    selected = ' selected';
                }

                $campo.append(
                    '<option value="' + unidade.id + '"' + selected + '>' +
                        unidade.sigla +
                    '</option>'
                );
            });

            if (!data || data.length === 0) {
                $campo
                    .empty()
                    .append('<option value="">Nenhuma unidade encontrada</option>');
            }
        },

        error: function (xhr) {
            console.error(
                'Erro ao carregar unidades:',
                xhr.status,
                xhr.responseText
            );

            $campo
                .empty()
                .append('<option value="">Erro ao carregar unidades</option>');
        }
    });
}


function validaPostoSituacao(situacaoId) {
    var $posto = $('#posto');

    $posto
        .prop('disabled', true)
        .empty()
        .append('<option value="">Carregando...</option>');

    if (!situacaoId) {
        $posto
            .prop('disabled', false)
            .empty()
            .append('<option value="">Selecione Posto / Graduação</option>');

        return;
    }

    $.ajax({
        url: $host + '/cascade/carregarPostoSituacao/all/' + situacaoId,
        type: 'GET',
        dataType: 'json',

        success: function (data) {
            var postoSelecionado = $('#posto_selecionado').val();

            $posto
                .prop('disabled', false)
                .empty()
                .append('<option value="">Selecione</option>');

            $.each(data, function (index, posto) {
                var selected = '';

                if (
                    postoSelecionado &&
                    String(postoSelecionado) === String(posto.id)
                ) {
                    selected = ' selected';
                }

                $posto.append(
                    '<option value="' + posto.id + '"' + selected + '>' +
                        posto.sigla +
                    '</option>'
                );
            });

            if (!data || data.length === 0) {
                $posto
                    .empty()
                    .append('<option value="">Nenhum posto encontrado</option>');
            }

            // Mantém bloqueado para situação 2, caso essa seja a regra.
            if (String($('#situacao').val()) === '2') {
                $posto.css('pointer-events', 'none');
            } else {
                $posto.css('pointer-events', '');
            }
        },

        error: function (xhr) {
            console.error(
                'Erro ao carregar posto/graduação:',
                xhr.status,
                xhr.responseText
            );

            $posto
                .prop('disabled', false)
                .empty()
                .append('<option value="">Erro ao carregar</option>');
        }
    });
}

function validaPostoSituacaoTodos(situacaoId) {
    validaPostoSituacao(situacaoId);
}


/*
|--------------------------------------------------------------------------
| FORÇA → POSTO
|--------------------------------------------------------------------------
*/

function validaPosto(forcaId, campoDestino) {
    var $campo = $('select[name="' + campoDestino + '"]');

    if (!$campo.length) {
        return;
    }

    $campo
        .empty()
        .append('<option value="">Carregando...</option>');

    if (!forcaId) {
        $campo
            .empty()
            .append('<option value="">Selecione</option>');

        return;
    }

    $.ajax({
        url: $host + '/cascade/carregarPosto/' + forcaId,
        type: 'GET',
        dataType: 'json',

        success: function (data) {
            var postoSelecionado = $('input[name="postos"]').val();

            $campo
                .empty()
                .append('<option value="">Selecione</option>');

            $.each(data, function (index, posto) {
                var selected = '';

                if (
                    postoSelecionado &&
                    String(postoSelecionado) === String(posto.id)
                ) {
                    selected = ' selected';
                }

                $campo.append(
                    '<option value="' + posto.id + '"' + selected + '>' +
                        posto.sigla +
                    '</option>'
                );
            });
        },

        error: function (xhr) {
            console.error(
                'Erro ao carregar postos:',
                xhr.status,
                xhr.responseText
            );

            $campo
                .empty()
                .append('<option value="">Erro ao carregar postos</option>');
        }
    });
}


/*
|--------------------------------------------------------------------------
| INICIALIZAÇÃO
|--------------------------------------------------------------------------
*/


$(document).ready(function () {
    $host = $('meta[name="app-url"]').attr('content');

    if (!$host) {
        console.error(
            'A meta tag <meta name="app-url"> não foi encontrada.'
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Primeiro oculta e remove obrigatoriedades
    |--------------------------------------------------------------------------
    */

    hiddenFields();


    /*
    |--------------------------------------------------------------------------
    | Upload de arquivos
    |--------------------------------------------------------------------------
    */

    $('.custom-file-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();

        $(this)
            .siblings('.custom-file-label')
            .addClass('selected')
            .html(fileName);
    });


    /*
    |--------------------------------------------------------------------------
    | Situação inicial e posto/graduação
    |--------------------------------------------------------------------------
    */

    var situacaoInicial = $('#situacao').val();

    if (situacaoInicial) {
        changeFields(situacaoInicial);
        validaPostoSituacao(situacaoInicial);
    }


    /*
    |--------------------------------------------------------------------------
    | Alteração da situação
    |--------------------------------------------------------------------------
    */

    $('#situacao').on('change', function () {
        var situacaoId = $(this).val();

        /*
         * Ao trocar a situação, elimina o posto anterior,
         * pois ele pode não pertencer à nova situação.
         */
        $('#posto_selecionado').val('');

        changeFields(situacaoId);
        validaPostoSituacao(situacaoId);
    });


    /*
    |--------------------------------------------------------------------------
    | Carregamento inicial: UF → Cidade → OM
    |--------------------------------------------------------------------------
    */

    var ufInicial = $('#uf').val();

    if (ufInicial) {
        validarUF(ufInicial);
    } else {
        $('#cidade')
            .empty()
            .append('<option value="">Selecione Cidade</option>');

        $('#om')
            .empty()
            .append(
                '<option value="">Selecione primeiro uma cidade</option>'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Alteração da UF
    |--------------------------------------------------------------------------
    */

    $('#uf').on('change', function () {
        $('#cidade_selecionada').val('');
        $('#om_selecionada').val('');

        validarUF($(this).val());
    });


    /*
    |--------------------------------------------------------------------------
    | Alteração da cidade
    |--------------------------------------------------------------------------
    */

    $('#cidade').on('change', function () {
        $('#om_selecionada').val('');

        validaOM($(this).val());
    });


    /*
    |--------------------------------------------------------------------------
    | Alteração da OM
    |--------------------------------------------------------------------------
    */

    $('#om').on('change', function () {
        $('#om_selecionada').val($(this).val());
    });


    /*
    |--------------------------------------------------------------------------
    | Validade do documento
    |--------------------------------------------------------------------------
    */

    $('#validade').on('change', function () {
        $('#documento').prop('required', true);
        $('#documento_verso').prop('required', true);
    });


    /*
    |--------------------------------------------------------------------------
    | Grupo de destinação
    |--------------------------------------------------------------------------
    */

    var grupoInicial = $('select[name="grupodestinacao"]').val();

    if (grupoInicial && $('select[name="unidade"]').length) {
        validaGrupo(grupoInicial, 'unidade');
    }

    $('select[name="grupodestinacao"]').on('change', function () {
        if ($('select[name="unidade"]').length) {
            validaGrupo($(this).val(), 'unidade');
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Alteração manual do posto
    |--------------------------------------------------------------------------
    */

    $('#posto').on('change', function () {
        /*
         * Atualiza o posto selecionado para evitar perder
         * a seleção caso o campo seja recarregado.
         */
        $('#posto_selecionado').val($(this).val());

        if ($('#dtUltPromo').length) {
            $('#dtUltPromo').val('');
        }

        $('#documento').prop('required', true);
        $('#documento_verso').prop('required', true);

        alert('É necessário anexar um novo documento.');
    });
});




/*
|--------------------------------------------------------------------------
| EXIBIÇÃO DOS CAMPOS CONFORME A SITUAÇÃO
|--------------------------------------------------------------------------
*/

function changeFields(idSituacao) {
    if (!idSituacao || Number(idSituacao) === 0) {
        $('.milReserva').hide();
        $('.militarAtiva').hide();
        $('.siape').hide();
        $('.ForcaOmPosto').hide();
        $('.identidade').hide();

        removerObrigatoriedadeCampos();

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Militar da ativa
    |--------------------------------------------------------------------------
    */

    if (Number(idSituacao) === 1) {
        $('.milReserva').hide();
        $('.siape').hide();
        $('.militarAtiva').show();
        $('.ForcaOmPosto').show();
        $('.identidade').show();

        $('#nivel').text('Posto / Graduação');
        $('#texto').text('Identidade Militar');

        if ($.fn.mask) {
            $('#idtMil').mask('000.000.000-00');
        }

        militarAtiva();

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Militar da reserva
    |--------------------------------------------------------------------------
    */

    if (Number(idSituacao) === 2) {
        $('.milReserva').show();
        $('.militarAtiva').show();
        $('.ForcaOmPosto').show();
        $('.identidade').show();
        $('.siape').hide();

        $('#nivel').text('Posto / Graduação');
        $('#texto').text('Identidade Militar');

        if ($.fn.mask) {
            $('#idtMil').mask('000.000.000-00');
        }

        militarReserva();

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Servidor civil
    |--------------------------------------------------------------------------
    */

    if (Number(idSituacao) === 3) {
        $('.milReserva').hide();
        $('.militarAtiva').hide();
        $('.siape').show();
        $('.identidade').show();
        $('.ForcaOmPosto').show();

        $('#texto').text('Identidade Civil');
        $('#nivel').text('Nível');

        if ($.fn.mask) {
            $('#idtMil').mask('000.000.000-00');
        }

        servidorCivil();

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Pensionista ou outras situações
    |--------------------------------------------------------------------------
    */

    $('.milReserva').hide();
    $('.siape').hide();
    $('.militarAtiva').hide();
    $('.ForcaOmPosto').show();
    $('.identidade').show();

    $('#nivel').text('Posto / Graduação');
    $('#texto').text('Identidade Militar');

    if ($.fn.mask) {
        $('#idtMil').mask('000.000.000-00');
    }

    pensionista();
}


/*
|--------------------------------------------------------------------------
| OCULTAR CAMPOS INICIALMENTE
|--------------------------------------------------------------------------
*/

function hiddenFields() {
    $('.milReserva').hide();
    $('.militarAtiva').hide();
    $('.siape').hide();
    $('.ForcaOmPosto').hide();
    $('.identidade').hide();
    $('.nivelescola').hide();

    removerObrigatoriedadeCampos();
}


/*
|--------------------------------------------------------------------------
| OBRIGATORIEDADE DOS CAMPOS
|--------------------------------------------------------------------------
*/

function removerObrigatoriedadeCampos() {
    $('#pttc').prop('required', false);
    $('#dtUltPromo').prop('required', false);
    $('#forca').prop('required', false);
    $('#posto').prop('required', false);
    $('#siape').prop('required', false);
    $('#idtMil').prop('required', false);
}

function militarAtiva() {
    removerObrigatoriedadeCampos();

    $('#dtUltPromo').prop('required', true);
    $('#forca').prop('required', true);
    $('#posto').prop('required', true);
    $('#idtMil').prop('required', true);
}

function militarReserva() {
    removerObrigatoriedadeCampos();

    $('#dtUltPromo').prop('required', true);
    $('#forca').prop('required', true);
    $('#posto').prop('required', true);
    $('#idtMil').prop('required', true);
}

function pensionista() {
    removerObrigatoriedadeCampos();

    $('#forca').prop('required', true);
    $('#posto').prop('required', true);
    $('#idtMil').prop('required', true);
}

function servidorCivil() {
    removerObrigatoriedadeCampos();

    $('#siape').prop('required', true);
    $('#idtMil').prop('required', true);
}

function requiredField(field, state) {
    var input = $(field);

    if (state === true || state === 'TRUE') {
        input.prop('required', true);
    } else {
        input.prop('required', false);
    }
}
