@extends('layouts.app')

@section('content')
<article class="items-list-page">
    <div class="title-search-block">
        <div class="title-block">
            <h3 class="title">Aceitar Remanejamento</h3>
            <p class="title-description">
                Sua solicitação precisou de ajustes para respeitar a capacidade máxima das unidades.
                Confira abaixo as acomodações propostas.
            </p>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Solicitação</th>
                                <th>Tipo de UH</th>
                                <th>Período</th>
                                <th>Adultos</th>
                                <th>Crianças</th>
                                <th>Status do aceite</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hospedagens as $hospedagem)
                                <tr>
                                    <td>#{{ $hospedagem->id }}</td>
                                    <td>{{ optional($hospedagem->tipouh)->descricao }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($hospedagem->data_inicio)->format('d/m/Y') }}
                                        a
                                        {{ \Carbon\Carbon::parse($hospedagem->data_termino)->format('d/m/Y') }}
                                    </td>
                                    <td>{{ $hospedagem->adulto }}</td>
                                    <td>{{ $hospedagem->crianca }}</td>
                                    <td>{{ $hospedagem->remanejamento_status ?: 'pendente' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($hospedagens->contains(function ($item) { return $item->remanejamento_status !== 'aceito'; }))
                    <form method="POST" action="{{ route('hospede.remanejamento.aceitar', ['token' => $token]) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Aceitar Remanejamento
                        </button>
                    </form>
                @else
                    <div class="alert alert-success mb-0">
                        Este remanejamento já foi aceito.
                    </div>
                @endif
            </div>
        </div>
    </section>
</article>
@endsection
