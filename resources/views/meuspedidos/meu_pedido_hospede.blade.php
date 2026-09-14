@extends('layouts.app')
@section('title', 'Dados do seu pedido')
@section('content')

<style>
    #modalComprovante .modal-dialog {
        max-width: 95vw;
        width: 95vw;
        margin: 2vh auto;
    }

    #modalComprovante .modal-content {
        height: 92vh;
    }

    #modalComprovante .modal-body {
        height: calc(92vh - 65px);
        padding: 0;
        overflow: hidden;
    }

    #modalComprovante iframe {
        width: 100%;
        height: 100%;
        border: 0;
        background: #fff;
    }
</style>

<div class="title-block">
    <h3 class="title">Dados do seu Pedido</h3>
    <p class="title-description">
        Verifique aqui os dados do seu pedido de inscrição para hospedagem no Forte Marechal Luz.
    </p>
</div>

<section class="section">
    <div class="row sameheight-container">
        <div class="col-12">
            <div class="card card-block sameheight-item">

                @if($hospedagem->status == 5)
                    <div class="alert alert-danger text-center" role="alert">
                        Faça o upload do comprovante de pagamento para confirmar a reserva.
                    </div>
                @endif

                <div class="row">
                    <div class="form-group col-sm-12 col-md-4">
                        <label class="control-label">Status</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->status_hospedagem->status }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-4">
                        <label class="control-label">Posto / Graduação</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->user->posto->sigla }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-4">
                        <label class="control-label">Nome</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->user->name }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">CPF</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->user_cpf }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">UF</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->user->uf->descricao }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">Cidade</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->user->cidade->descricao }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">OM</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->user->om->sigla }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">Adultos</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->adulto }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">Crianças</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->crianca }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">PNE</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->pne ? 'Sim' : 'Não' }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">PET</label>
                        <input type="text" class="form-control boxed" value="{{ $hospedagem->pet ? 'Sim' : 'Não' }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">Entrada</label>
                        <input type="text" class="form-control boxed" value="{{ \Carbon\Carbon::parse($hospedagem->data_inicio)->format('d-m-Y') }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">Saída</label>
                        <input type="text" class="form-control boxed" value="{{ \Carbon\Carbon::parse($hospedagem->data_termino)->format('d-m-Y') }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">Horário Entrada</label>
                        <input type="text" class="form-control boxed" value="{{ $horario ? \Carbon\Carbon::parse($horario->entrada)->format('H:i') : '-' }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-3">
                        <label class="control-label">Horário Saída</label>
                        <input type="text" class="form-control boxed" value="{{ $horario ? \Carbon\Carbon::parse($horario->saida)->format('H:i') : '-' }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-12 col-md-4">
                        <label class="control-label">Tipo de Unidade Habitacional</label>
                        <input type="text" class="form-control boxed" value="{{ optional($hospedagem->tipouh)->descricao }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-4">
                        <label class="control-label">Quantidade de Diárias</label>
                        <input type="text" class="form-control boxed" value="{{ $qntDiariasAtualizada }}" readonly>
                    </div>
                    <div class="form-group col-sm-12 col-md-4">
                        <label class="control-label">Valor Total</label>
                        <input type="text" class="form-control boxed" value="R$ {{ number_format((float) $valorAtualizado, 2, ',', '.') }}" readonly>
                    </div>
                </div>

                @if(!empty($hospedagem->observacao))
                    <div class="row">
                        <div class="form-group col-12">
                            <label class="control-label">Observação</label>
                            <textarea class="form-control boxed" rows="3" readonly>{{ $hospedagem->observacao }}</textarea>
                        </div>
                    </div>
                @endif

                <hr>

                <div class="row">
                    <div class="form-group col-12">
                        @if($hospedagem->status == 3 || $hospedagem->status == 5)
                            <form id="formPagamentoInicial"
                                  action="{{ route('pagamento.processaRequisicao', ['id' => Crypt::encrypt($hospedagem->id)]) }}"
                                  method="POST"
                                  style="display:inline;">
                                @csrf
                                <button type="button" class="btn btn-success" onclick="iniciarPagamentoInicial()">
                                    <i class="fas fa-money-bill-alt"></i> Realizar Pagamento de 01 diária
                                </button>
                            </form>
                        @endif

                        @if(in_array((int) $hospedagem->status, [2, 3, 5, 7], true) && $hospedagem->checkin === null)
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalCancelar">
                                <i class="fas fa-bed"></i> Cancelar Reserva
                            </button>
                        @endif
                    </div>
                </div>

                @if($hospedagem->status == 4 && $comprovante)
                    <div class="row mt-3">
                        <div class="form-group col-12">
                            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modalComprovante">
                                <i class="fas fa-file-alt"></i> Ver Comprovante de Pagamento
                            </button>
                        </div>
                    </div>
                @endif

                @if($hospedagem->status == 5 || $hospedagem->status == 4)
                    <hr>
                    <form action="{{ route('hospede.uploadrecibo') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="hospedagem_id" value="{{ $hospedagem->id }}">

                        <div class="form-group">
                            <label class="control-label">Anexar Comprovante de Pagamento</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="documento" id="documento" accept=".jpg,.jpeg,.png,.pdf" required>
                                <label class="custom-file-label" for="documento">Escolha o arquivo</label>
                            </div>
                            <p>
                                <font style="color: red; font-size: 16px;">
                                    <b>Comprovante de Pagamento no formato .JPG, .PNG, .PDF até 4MB ou PRINT do arquivo PDF</b>
                                </font>
                            </p>
                            @error('documento')
                                <span class="text-danger"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle"></i> Fazer Upload
                        </button>
                    </form>
                @endif

                <hr>

                <div class="row">
                    <div class="form-group col-12 mb-0">
                        <a href="{{ route('hospede.meuspedidos') }}" class="btn btn-secondary">
                            <i class="fas fa-angle-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($hospedagem->status == 4 && $comprovante)
<div class="modal fade" id="modalComprovante" tabindex="-1" role="dialog" aria-labelledby="modalComprovanteTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalComprovanteTitulo">Comprovante de Pagamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe
                    src="{{ route('documentos.verdocumento', ['id' => Crypt::encrypt($comprovante->id), 'doc' => Crypt::encrypt($comprovante->tipo_doc), 'tipo' => '3']) }}"
                    title="Comprovante de Pagamento">
                </iframe>
            </div>
        </div>
    </div>
</div>
@endif

@if(in_array((int) $hospedagem->status, [2, 3, 5, 7], true) && $hospedagem->checkin === null)
<div class="modal fade" id="modalCancelar" tabindex="-1" role="dialog" aria-labelledby="modalCancelarTitulo" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCancelarTitulo">Cancelar Reserva</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Deseja realmente cancelar esta reserva?</p>
                <p class="text-danger">
                    Se o cancelamento ocorrer por mudança de período ou unidade habitacional, consulte a Seção FML antes de realizar uma nova solicitação.
                </p>
            </div>
            <div class="modal-footer">
                <form action="{{ route('cancelar.hospedagem', ['id' => Crypt::encrypt($hospedagem->id)]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sim, cancelar</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
            </div>
        </div>
    </div>
</div>
@endif

@push('javascript')
<script>
$(document).ready(function () {
    $('.custom-file-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });
});

let janelaPagamentoInicial = null;
let monitorPagamentoInicial = null;

function iniciarPagamentoInicial() {
    const form = document.getElementById('formPagamentoInicial');
    if (!form) {
        return;
    }

    const nomeJanela = 'pagamentoInicialPagTesouro';
    janelaPagamentoInicial = window.open('', nomeJanela, 'width=760,height=760,scrollbars=yes,resizable=yes');

    if (!janelaPagamentoInicial) {
        alert('O navegador bloqueou a janela. Permita pop-ups e tente novamente.');
        return;
    }

    form.target = nomeJanela;
    form.submit();
    janelaPagamentoInicial.focus();

    const urlStatus = @json(route('pagamento.inicial.status', ['id' => Crypt::encrypt($hospedagem->id)]));
    let consultaEmAndamento = false;

    const consultarPagamento = async function () {
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
                return;
            }

            const dados = await resposta.json();

            if (dados.pagamento_confirmado === true) {
                clearInterval(monitorPagamentoInicial);

                if (janelaPagamentoInicial && !janelaPagamentoInicial.closed) {
                    janelaPagamentoInicial.close();
                }

                window.location.reload();
            }
        } finally {
            consultaEmAndamento = false;
        }
    };

    consultarPagamento();
    monitorPagamentoInicial = setInterval(consultarPagamento, 5000);
}
</script>
@endpush
@endsection
