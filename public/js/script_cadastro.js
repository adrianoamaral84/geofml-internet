var $host = '';

/*
|--------------------------------------------------------------------------
| UF → CIDADES
|--------------------------------------------------------------------------
*/

function validarUF(ufId) {
    var $cidade = $('#cidade');
    var $om = $('#om');

    if (!$cidade.length) {
        return;
    }

    $cidade
        .prop('disabled', true)
        .empty()
        .append('<option value="">Carregando cidades...</option>');

    if ($om.length) {
        $om
            .prop('disabled', true)
            .empty()
            .append(
                '<option value="">' +
                    'Selecione primeiro uma cidade' +
                '</option>'
            );
    }

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

            if (!data || data.length === 0) {
                $cidade
                    .empty()
                    .append(
                        '<option value="">' +
                            'Nenhuma cidade encontrada' +
                        '</option>'
                    );

                return;
            }

            $.each(data, function (index, cidade) {
                var selected = '';

                if (
                    cidadeSelecionada &&
                    String(cidadeSelecionada) === String(cidade.id)
                ) {
                    selected = ' selected';
                }

                $cidade.append(
                    '<option value="' +
                        cidade.id +
                        '"' +
                        selected +
                    '>' +
                        cidade.descricao +
                    '</option>'
                );
            });

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
                .append(
                    '<option value="">' +
                        'Erro ao carregar cidades' +
                    '</option>'
                );
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

    if (!$om.length) {
        return;
    }

    $om
        .prop('disabled', true)
        .empty()
        .append('<option value="">Carregando OMs...</option>');

    if (!cidadeId) {
        $om
            .prop('disabled', false)
            .empty()
            .append(
                '<option value="">' +
                    'Selecione primeiro uma cidade' +
                '</option>'
            );

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

            if (!data || data.length === 0) {
                $om
                    .empty()
                    .append(
                        '<option value="">' +
                            'Nenhuma OM encontrada' +
                        '</option>'
                    );

                return;
            }

            $.each(data, function (index, om) {
                var selected = '';

                if (
                    omSelecionada &&
                    String(omSelecionada) === String(om.id)
                ) {
                    selected = ' selected';
                }

                $om.append(
                    '<option value="' +
                        om.id +
                        '"' +
                        selected +
                    '>' +
                        om.sigla +
                    '</option>'
                );
            });
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
                .append(
                    '<option value="">' +
                        'Erro ao carregar OMs' +
                    '</option>'
                );
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

    if (!grupoId) {
        $campo
            .empty()
            .append('<option value="">Selecione</option>');

        return;
    }

    $campo
        .empty()
        .append('<option value="">Carregando...</option>');

    $.ajax({
        url: $host + '/cascade/carregarUnidades/' + grupoId,
        type: 'GET',
        dataType: 'json',

        success: function (data) {
            var unidadeSelecionada = $('#unidade_selecionada').val();

            $campo
                .empty()
                .append('<option value="">Selecione</option>');

            if (!data || data.length === 0) {
                $campo
                    .empty()
                    .append(
                        '<option value="">' +
                            'Nenhuma unidade encontrada' +
                        '</option>'
                    );

                return;
            }

            $.each(data, function (index, unidade) {
                var selected = '';

                if (
                    unidadeSelecionada &&
                    String(unidadeSelecionada) === String(unidade.id)
                ) {
                    selected = ' selected';
                }

                $campo.append(
                    '<option value="' +
                        unidade.id +
                        '"' +
                        selected +
                    '>' +
                        unidade.sigla +
                    '</option>'
                );
            });
        },

        error: function (xhr) {
            console.error(
                'Erro ao carregar unidades:',
                xhr.status,
                xhr.responseText
            );

            $campo
                .empty()
                .append(
                    '<option value="">' +
                        'Erro ao carregar unidades' +
                    '</option>'
                );
        }
    });
}


/*
|--------------------------------------------------------------------------
| SITUAÇÃO → POSTO/GRADUAÇÃO
|--------------------------------------------------------------------------
|
| A situação é enviada diretamente para o controller:
|
| 1 = Militar da Ativa
| 2 = Militar da Reserva Remunerado
| 3 = Servidor Civil
|
|--------------------------------------------------------------------------
*/

function validaPostoSituacao(situacaoId) {
    var $posto = $('#posto');

    if (!$posto.length) {
        return;
    }

    /*
     * Garante que o select esteja desbloqueado.
     * Isso é necessário principalmente para situacao_id = 2.
     */
    $posto
        .css('pointer-events', '')
        .removeAttr('readonly')
        .removeAttr('aria-disabled')
        .removeAttr('tabindex');

    if (!situacaoId) {
        $posto
            .prop('disabled', false)
            .empty()
            .append(
                '<option value="">' +
                    'Selecione Posto / Graduação' +
                '</option>'
            );

        return;
    }

    var postoSelecionado = $('#posto_selecionado').val();

    $posto
        .prop('disabled', true)
        .empty()
        .append('<option value="">Carregando postos...</option>');

    $.ajax({
        url:
            $host +
            '/cascade/carregarPostoSituacao/all/' +
            encodeURIComponent(situacaoId),

        type: 'GET',
        dataType: 'json',
        cache: false,

        success: function (data) {
            /*
             * Algumas versões do Laravel podem devolver um objeto.
             * Converte para array quando necessário.
             */
            if (data && !Array.isArray(data)) {
                data = Object.keys(data).map(function (chave) {
                    return data[chave];
                });
            }

            $posto
                .prop('disabled', false)
                .css('pointer-events', '')
                .removeAttr('readonly')
                .removeAttr('aria-disabled')
                .removeAttr('tabindex')
                .empty()
                .append(
                    '<option value="">' +
                        'Selecione Posto / Graduação' +
                    '</option>'
                );

            if (!data || data.length === 0) {
                $posto
                    .empty()
                    .append(
                        '<option value="">' +
                            'Nenhum posto encontrado' +
                        '</option>'
                    );

                console.warn(
                    'Nenhum posto retornado para situacao_id:',
                    situacaoId
                );

                return;
            }

            $.each(data, function (index, posto) {
                var selected = '';

                if (
                    postoSelecionado &&
                    String(postoSelecionado) === String(posto.id)
                ) {
                    selected = ' selected';
                }

                $posto.append(
                    '<option value="' +
                        posto.id +
                        '"' +
                        selected +
                    '>' +
                        posto.sigla +
                    '</option>'
                );
            });
        },

        error: function (xhr) {
            console.error(
                'Erro ao carregar Posto/Graduação:',
                xhr.status,
                xhr.responseText
            );

            $posto
                .prop('disabled', false)
                .css('pointer-events', '')
                .removeAttr('readonly')
                .removeAttr('aria-disabled')
                .removeAttr('tabindex')
                .empty()
                .append(
                    '<option value="">' +
                        'Erro ao carregar postos' +
                    '</option>'
                );

            alert(
                'Não foi possível carregar os postos/graduações.'
            );
        }
    });
}


/*
 * Mantida para compatibilidade com chamadas antigas.
 */
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

    if (!forcaId) {
        $campo
            .empty()
            .append('<option value="">Selecione</option>');

        return;
    }

    $campo
        .empty()
        .append('<option value="">Carregando...</option>');

    $.ajax({
        url: $host + '/cascade/carregarPosto/' + forcaId,
        type: 'GET',
        dataType: 'json',

        success: function (data) {
            var postoSelecionado =
                $('#posto_selecionado').val() ||
                $('input[name="postos"]').val();

            $campo
                .empty()
                .append('<option value="">Selecione</option>');

            if (!data || data.length === 0) {
                $campo
                    .empty()
                    .append(
                        '<option value="">' +
                            'Nenhum posto encontrado' +
                        '</option>'
                    );

                return;
            }

            $.each(data, function (index, posto) {
                var selected = '';

                if (
                    postoSelecionado &&
                    String(postoSelecionado) === String(posto.id)
                ) {
                    selected = ' selected';
                }

                $campo.append(
                    '<option value="' +
                        posto.id +
                        '"' +
                        selected +
                    '>' +
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
                .append(
                    '<option value="">' +
                        'Erro ao carregar postos' +
                    '</option>'
                );
        }
    });
}


/*
|--------------------------------------------------------------------------
| EXIBIÇÃO DOS CAMPOS CONFORME A SITUAÇÃO
|--------------------------------------------------------------------------
*/

function changeFields(idSituacao) {
    idSituacao = String(idSituacao || '');

    if (idSituacao === '' || idSituacao === '0') {
        hiddenFields();

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | MILITAR DA ATIVA
    |--------------------------------------------------------------------------
    */

    if (idSituacao === '1') {
        $('.milReserva').hide();
        $('.siape').hide();

        $('.militarAtiva').show();
        $('.ForcaOmPosto').show();
        $('.identidade').show();

        $('#nivel').text('Posto / Graduação');
        $('#texto').text('Identidade Militar');

        $('#pttc').prop('checked', false);

        $('#mesAnoFinal')
            .prop('required', false)
            .val('');

        if ($.fn.mask) {
            $('#idtMil').mask('000.000.000-00');
        }

        militarAtiva();
        atualizarObrigatoriedadeMesAno();

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | MILITAR DA RESERVA REMUNERADO — ID 2
    |--------------------------------------------------------------------------
    */

    if (idSituacao === '2') {
        $('.milReserva').show();
        $('.militarAtiva').show();
        $('.ForcaOmPosto').show();
        $('.identidade').show();
        $('.siape').hide();

        $('#nivel').text('Posto / Graduação');
        $('#texto').text('Identidade Militar');

        /*
         * Remove qualquer bloqueio colocado anteriormente no posto.
         */
        $('#posto')
            .prop('disabled', false)
            .css('pointer-events', '')
            .removeAttr('readonly')
            .removeAttr('aria-disabled')
            .removeAttr('tabindex');

        if ($.fn.mask) {
            $('#idtMil').mask('000.000.000-00');
        }

        militarReserva();
        atualizarObrigatoriedadeMesAno();

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | SERVIDOR CIVIL
    |--------------------------------------------------------------------------
    */

    if (idSituacao === '3') {
        $('.milReserva').hide();
        $('.militarAtiva').hide();
        $('.siape').show();
        $('.identidade').show();
        $('.ForcaOmPosto').show();

        $('#texto').text('Identidade Civil');
        $('#nivel').text('Nível');

        $('#pttc').prop('checked', false);

        $('#mesAnoFinal')
            .prop('required', false)
            .val('');

        if ($.fn.mask) {
            $('#idtMil').mask('00.000.000-0');
        }

        servidorCivil();
        atualizarObrigatoriedadeMesAno();

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | PENSIONISTA OU OUTRAS SITUAÇÕES
    |--------------------------------------------------------------------------
    */

    $('.milReserva').hide();
    $('.siape').hide();
    $('.militarAtiva').hide();
    $('.ForcaOmPosto').show();
    $('.identidade').show();

    $('#nivel').text('Posto / Graduação');
    $('#texto').text('Identidade Militar');

    $('#pttc').prop('checked', false);

    $('#mesAnoFinal')
        .prop('required', false)
        .val('');

    if ($.fn.mask) {
        $('#idtMil').mask('000.000.000-00');
    }

    pensionista();
    atualizarObrigatoriedadeMesAno();
}


/*
|--------------------------------------------------------------------------
| OCULTAR CAMPOS
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
    $('#mesAnoFinal').prop('required', false);
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

    atualizarObrigatoriedadeMesAno();
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
    var $input = $(field);

    if (
        state === true ||
        String(state).toUpperCase() === 'TRUE'
    ) {
        $input.prop('required', true);
    } else {
        $input.prop('required', false);
    }
}


/*
|--------------------------------------------------------------------------
| PTTC E MÊS/ANO FINAL
|--------------------------------------------------------------------------
*/

function atualizarObrigatoriedadeMesAno() {
    var situacaoId = String($('#situacao').val() || '');
    var pttcMarcado = $('#pttc').is(':checked');

    var $mesAnoFinal = $('#mesAnoFinal');
    var $label = $('label[for="mesAnoFinal"]');

    if (situacaoId === '2' && pttcMarcado) {
        $mesAnoFinal.prop('required', true);

        $label.html(
            'Mês/Ano Final ' +
            '<span class="text-danger">*</span>'
        );
    } else {
        $mesAnoFinal.prop('required', false);
        $label.text('Mês/Ano Final');
    }
}


/*
|--------------------------------------------------------------------------
| INICIALIZAÇÃO
|--------------------------------------------------------------------------
*/

$(document).ready(function () {
    /*
     * Primeiro tenta utilizar a meta tag do Laravel.
     */
    $host = $('meta[name="app-url"]').attr('content');

    /*
     * Caso a meta tag não exista, usa protocolo e domínio atuais.
     */
    if (!$host) {
        $host =
            window.location.protocol +
            '//' +
            window.location.host;
    }

    /*
     * Remove a barra final para evitar URLs com duas barras.
     */
    $host = String($host).replace(/\/+$/, '');

    hiddenFields();


    /*
    |--------------------------------------------------------------------------
    | UPLOAD DE ARQUIVOS
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
    | MÁSCARA MÊS/ANO
    |--------------------------------------------------------------------------
    */

    if ($.fn.mask) {
        $('#mesAnoFinal').mask('00/0000');
    }


    /*
    |--------------------------------------------------------------------------
    | SITUAÇÃO INICIAL E POSTO/GRADUAÇÃO
    |--------------------------------------------------------------------------
    */

    var situacaoInicial = $('#situacao').val();

    if (situacaoInicial) {
        changeFields(situacaoInicial);
        validaPostoSituacao(situacaoInicial);
    }


    /*
    |--------------------------------------------------------------------------
    | ALTERAÇÃO DA SITUAÇÃO
    |--------------------------------------------------------------------------
    */

    $('#situacao').on('change', function () {
        var situacaoId = $(this).val();

        /*
         * Limpa o posto antigo, pois pode não pertencer
         * à situação recém-selecionada.
         */
        $('#posto_selecionado').val('');

        /*
         * Libera o select para todas as situações,
         * inclusive Militar da Reserva, ID 2.
         */
        $('#posto')
            .prop('disabled', false)
            .css('pointer-events', '')
            .removeAttr('readonly')
            .removeAttr('aria-disabled')
            .removeAttr('tabindex');

        changeFields(situacaoId);
        validaPostoSituacao(situacaoId);
        atualizarObrigatoriedadeMesAno();
    });


    /*
    |--------------------------------------------------------------------------
    | ALTERAÇÃO DO PTTC
    |--------------------------------------------------------------------------
    */

    $('#pttc').on('change', function () {
        atualizarObrigatoriedadeMesAno();

        /*
         * Se PTTC estiver marcado, libera a data de promoção.
         */
        if ($(this).is(':checked')) {
            $('#dtUltPromo')
                .prop('readonly', false)
                .removeAttr('tabindex')
                .removeAttr('aria-disabled')
                .css('pointer-events', '');
        } else {
            $('#dtUltPromo')
                .prop('readonly', true)
                .attr('tabindex', '-1')
                .attr('aria-disabled', 'true');
        }
    });


    /*
    |--------------------------------------------------------------------------
    | CARREGAMENTO INICIAL: UF → CIDADE → OM
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
                '<option value="">' +
                    'Selecione primeiro uma cidade' +
                '</option>'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ALTERAÇÃO DA UF
    |--------------------------------------------------------------------------
    */

    $('#uf').on('change', function () {
        $('#cidade_selecionada').val('');
        $('#om_selecionada').val('');

        validarUF($(this).val());
    });


    /*
    |--------------------------------------------------------------------------
    | ALTERAÇÃO DA CIDADE
    |--------------------------------------------------------------------------
    */

    $('#cidade').on('change', function () {
        $('#cidade_selecionada').val($(this).val());
        $('#om_selecionada').val('');

        validaOM($(this).val());
    });


    /*
    |--------------------------------------------------------------------------
    | ALTERAÇÃO DA OM
    |--------------------------------------------------------------------------
    */

    $('#om').on('change', function () {
        $('#om_selecionada').val($(this).val());
    });


    /*
    |--------------------------------------------------------------------------
    | GRUPO DE DESTINAÇÃO
    |--------------------------------------------------------------------------
    */

    var grupoInicial =
        $('select[name="grupodestinacao"]').val();

    if (
        grupoInicial &&
        $('select[name="unidade"]').length
    ) {
        validaGrupo(grupoInicial, 'unidade');
    }

    $('select[name="grupodestinacao"]').on(
        'change',
        function () {
            if ($('select[name="unidade"]').length) {
                validaGrupo(
                    $(this).val(),
                    'unidade'
                );
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | ALTERAÇÃO MANUAL DO POSTO
    |--------------------------------------------------------------------------
    */

    $('#posto').on('change', function () {
        $('#posto_selecionado').val($(this).val());

        if ($('#dtUltPromo').length) {
            $('#dtUltPromo').val('');
        }

        $('#documento').prop('required', true);
        $('#documento_verso').prop('required', true);

        /*
         * Evita mostrar o alerta no carregamento automático.
         */
        if ($(this).val()) {
            alert('É necessário anexar um novo documento.');
        }
    });


    /*
    |--------------------------------------------------------------------------
    | VALIDADE DO DOCUMENTO
    |--------------------------------------------------------------------------
    */

    $('#validade').on('change', function () {
        $('#documento').prop('required', true);
        $('#documento_verso').prop('required', true);
    });


    /*
    |--------------------------------------------------------------------------
    | VALIDADE INDETERMINADA
    |--------------------------------------------------------------------------
    */

    $('#indeterminado').on('change', function () {
        var marcado = $(this).is(':checked');

        $('#validade').prop('readonly', marcado);
        $('#documento').prop('required', true);
        $('#documento_verso').prop('required', true);

        alert(
            'Confirma a data de validade da sua identidade militar? ' +
            'Atualmente a validade é de 10 anos.'
        );
    });


    /*
    |--------------------------------------------------------------------------
    | DATA DA ÚLTIMA PROMOÇÃO
    |--------------------------------------------------------------------------
    */

    $('#dtUltPromo').on('change', function () {
        $('#documento').prop('required', true);
        $('#documento_verso').prop('required', true);
    });


    /*
    |--------------------------------------------------------------------------
    | ESTADO INICIAL DOS CAMPOS PTTC E MÊS/ANO
    |--------------------------------------------------------------------------
    */

    atualizarObrigatoriedadeMesAno();

    /*
     * Dispara a regra do PTTC na abertura sem gerar o evento change.
     */
    if ($('#pttc').is(':checked')) {
        $('#dtUltPromo')
            .prop('readonly', false)
            .removeAttr('tabindex')
            .removeAttr('aria-disabled')
            .css('pointer-events', '');
    }
});