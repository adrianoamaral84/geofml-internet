@extends('layouts.app')
@section('title', 'Central do Hóspede')
@section('content')
<style>
.gh-dashboard .gh-hero {background:linear-gradient(110deg,#1b5064,#237e89);color:#fff;border-radius:12px;padding:26px;margin-bottom:20px}
.gh-dashboard .gh-hero h2{color:#fff;margin:7px 0}
.gh-dashboard .gh-stat{background:#fff;border:1px solid #e8edf0;border-radius:12px;padding:19px;min-height:135px;margin-bottom:17px}
.gh-dashboard .gh-stat i{font-size:22px;color:#287c87}
.gh-dashboard .gh-stat strong{display:block;font-size:30px;line-height:1.4;color:#263b47}
.gh-dashboard .gh-panel{background:#fff;border:1px solid #e8edf0;border-radius:12px;padding:22px;margin-bottom:20px}
.gh-dashboard .gh-panel h4{font-size:18px;margin-bottom:16px}
.gh-dashboard .gh-entry{border-bottom:1px solid #edf1f4;padding:13px 0}
.gh-dashboard .gh-entry:last-child{border-bottom:0}
.gh-dashboard .gh-actions .btn{margin:0 8px 10px 0}
.gh-dashboard .gh-meter{height:9px;background:#edf1f4;border-radius:10px;overflow:hidden}
.gh-dashboard .gh-meter > span{height:100%;display:block;background:#268a78;border-radius:10px}
.gh-dashboard .gh-muted{color:#667781}
.gh-dashboard .gh-month{margin-bottom:20px}
.gh-dashboard .gh-label{display:flex;justify-content:space-between;gap:8px;margin:8px 0 5px}
@media(max-width:767px){.gh-dashboard .gh-hero{padding:20px}.gh-dashboard .gh-panel{padding:16px}}
</style>
<article class="items-list-page gh-dashboard">
    <div class="gh-hero">
        <small>GEOFML · ÁREA DO HÓSPEDE</small>
        <h2>Olá, {{ $usuario->name }}!</h2>
        <p style="margin-bottom:0">Acompanhe suas solicitações de hospedagem e confira as próximas etapas.</p>
    </div>
    <div class="row">
        @foreach ([
            ['icon'=>'fa-clipboard-list','title'=>'Total de pedidos','value'=>$resumo['total']],
            ['icon'=>'fa-clock','title'=>'Aguardando aprovação','value'=>$resumo['aguardando']],
            ['icon'=>'fa-check-circle','title'=>'Aprovados','value'=>$resumo['aprovados']],
            ['icon'=>'fa-users','title'=>'Fila de espera','value'=>$resumo['fila']]
        ] as $item)
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="gh-stat">
                <i class="fas {{ $item['icon'] }}" aria-hidden="true"></i>
                <strong>{{ $item['value'] }}</strong>
                <span class="gh-muted">{{ $item['title'] }}</span>
            </div>
        </div>
        @endforeach
    </div>
    <div class="gh-panel gh-actions">
        <h4><i class="fa fa-calendar-plus text-info"></i> O que deseja fazer?</h4>
        <a class="btn btn-primary" href="{{ route('hospede.solicitarinscricao') }}"><i class="fa fa-plus"></i> Nova solicitação</a>
        <a class="btn btn-secondary" href="{{ route('hospede.meuspedidos') }}"><i class="fa fa-list"></i> Meus pedidos</a>
        <a class="btn btn-secondary" href="{{ route('profile') }}"><i class="fa fa-user"></i> Meu cadastro</a>
    </div>
    <div class="row">
        <div class="col-lg-7">
            <div class="gh-panel">
                <h4><i class="fa fa-calendar-alt text-info"></i> Próximas hospedagens e solicitações</h4>
                @forelse ($proximosPedidos as $pedido)
                <div class="gh-entry">
                    <div class="d-flex justify-content-between flex-wrap">
                        <strong>{{ optional($pedido->tipouh)->descricao ?? 'Unidade habitacional' }}</strong>
                        <span class="badge badge-info">{{ optional($pedido->status_hospedagem)->status ?? 'Status ' . $pedido->status }}</span>
                    </div>
                    <div class="gh-muted" style="margin-top:6px">
                        <i class="fa fa-calendar"></i>
                        {{ \Carbon\Carbon::parse($pedido->data_inicio)->format('d/m/Y') }}
                        a {{ \Carbon\Carbon::parse($pedido->data_termino)->format('d/m/Y') }}
                        · {{ (int) $pedido->adulto }} adulto(s) · {{ (int) $pedido->crianca }} criança(s)
                    </div>
                </div>
                @empty
                <p class="gh-muted">Você ainda não possui hospedagens futuras ou em andamento.</p>
                @endforelse
                <a href="{{ route('hospede.meuspedidos') }}" class="btn btn-link" style="padding-left:0">Ver todos os pedidos</a>
            </div>
            <div class="gh-panel">
                <h4><i class="fa fa-exclamation-circle text-warning"></i> Pendências e próximas etapas</h4>
                @forelse($pendencias as $pedido)
                <div class="gh-entry">
                    <strong>{{ optional($pedido->tipouh)->descricao ?? 'Hospedagem' }}</strong>
                    <div class="gh-muted">{{ optional($pedido->status_hospedagem)->status ?? 'Aguardando andamento' }} · Entrada {{ \Carbon\Carbon::parse($pedido->data_inicio)->format('d/m/Y') }}</div>
                    <small>Consulte os detalhes do pedido para verificar se existe alguma ação necessária.</small>
                </div>
                @empty
                <p class="gh-muted">Nenhuma solicitação aguardando pagamento, liberação ou comprovante no momento.</p>
                @endforelse
                <a href="{{ route('hospede.meuspedidos') }}" class="btn btn-link" style="padding-left:0">Acompanhar solicitações</a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="gh-panel">
                <h4><i class="fa fa-chart-bar text-info"></i> Seus limites mensais</h4>
                <p class="gh-muted">Pedidos registrados por mês de entrada. Negados e cancelados não são contabilizados.</p>
                @foreach($meses as $mes)
                <div class="gh-month">
                    <strong style="text-transform:capitalize">{{ $mes['titulo'] }}</strong>
                    @if(!$mes['temporada_definida'])
                    <small class="d-block gh-muted">Temporada não identificada no primeiro dia do mês; o limite exibido é uma referência.</small>
                    @elseif($mes['alta'])
                    <small class="d-block gh-muted">Alta temporada · limite de apartamentos conforme configuração</small>
                    @else
                    <small class="d-block gh-muted">Baixa temporada</small>
                    @endif
                    <div class="gh-label"><span>Apartamento / Casa</span><strong>{{ $mes['comuns'] }} / {{ $mes['limite_comum'] ?? '—' }}</strong></div>
                    <div class="gh-meter"><span style="width:{{ $mes['limite_comum'] ? min(100, ($mes['comuns'] / max(1,$mes['limite_comum'])) * 100) : 0 }}%"></span></div>
                    <div class="gh-label"><span>Camping / Motorhome</span><strong>{{ $mes['camping'] }} / 2</strong></div>
                    <div class="gh-meter"><span style="width:{{ min(100,($mes['camping'] / 2) * 100) }}%"></span></div>
                </div>
                @endforeach
                <small class="gh-muted">Em períodos que atravessam temporadas, o limite aplicável é definido pelas diárias do pedido. A consulta acima é informativa.</small>
            </div>
            <div class="gh-panel">
                <h4><i class="fa fa-bullhorn text-info"></i> Avisos e mensagens</h4>
                @if($count > 0)
                <div class="alert alert-info"><i class="fa fa-envelope"></i> Você possui {{ $count }} mensagem(ns) não lida(s).</div>
                @else
                <p class="gh-muted">Não há mensagens novas.</p>
                @endif
                <p class="gh-muted" style="margin-bottom:0">Fique atento aos comunicados da administração e acompanhe o status dos seus pedidos.</p>
            </div>
            <div class="gh-panel">
                <h4><i class="fa fa-info-circle text-info"></i> Informações importantes</h4>
                <p>Na baixa temporada, você pode solicitar até <strong>2 apartamentos/casas</strong> e <strong>2 Camping/Motorhome</strong> por mês.</p>
                <p>Datas iguais ou sobrepostas são permitidas na baixa temporada, respeitando os limites e as demais condições.</p>
                <p style="margin-bottom:0">A edição de solicitações está disponível somente enquanto estiverem <strong>Aguardando Aprovação</strong>.</p>
            </div>
        </div>
    </div>
</article>
@endsection
