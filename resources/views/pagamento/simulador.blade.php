@extends('layouts.app')

@section('content')

<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Simulador de Pagamento</strong>
        </div>

        <div class="card-body">
            <div class="alert alert-warning">
                Ambiente de teste. Nenhum valor real será cobrado.
            </div>

            <p>
                <strong>Pagamento:</strong>
                {{ $pagamento->idPagamento }}
            </p>

            <p>
                <strong>Tipo:</strong>
                {{ $pagamento->tipo }}
            </p>

            <p>
                <strong>Valor:</strong>
                R$ {{ number_format(
                    $pagamento->valor,
                    2,
                    ',',
                    '.'
                ) }}
            </p>

            <p>
                <strong>Situação:</strong>
                {{ $pagamento->situacao }}
            </p>

            <div class="d-flex" style="gap: 10px;">
                <form
                    method="POST"
                    action="{{ route(
                        'pagamento.simulador.aprovar',
                        [
                            'id' => Crypt::encrypt(
                                $pagamento->id
                            )
                        ]
                    ) }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="btn btn-success"
                    >
                        Aprovar pagamento
                    </button>
                </form>

                <form
                    method="POST"
                    action="{{ route(
                        'pagamento.simulador.cancelar',
                        [
                            'id' => Crypt::encrypt(
                                $pagamento->id
                            )
                        ]
                    ) }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        Cancelar pagamento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection