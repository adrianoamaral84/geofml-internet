@component('mail::message')

<h1>Sr(a), {{ $posto}} {{ $nome }}</h1>
<br>
<center>
<div style="color: green; font-size: 16px;">
<b>{!!$cabecalho!!}</b>
</div>
</center>
<br>
<br><center>
<b>Dados do Pedido</b>
</center>
<br>

<center>

@component('mail::table')

| Data Entrada	| Data Saída	| Tipo Unidade  | Nº | Valor  |
|:-----------:|:-----------:|:------:|:------:|:------:|
|{{ $data_inicio }}	|{{ $data_termino }}	|{{ $unidade }}	 |{{ $uh }}{{ $classe->classe }}| R$ {{ number_format($valor, 2, ',', '.') }} |
@endcomponent


	
@component('mail::button', ['url' => 'https://geofml.5rm.eb.mil.br', 'color' => 'green'])
Acessar o Sistema
@endcomponent

</center>


@component('mail::panel')

Informo que foi distribuído ao Senhor(a) a(o) {{ $unidade }} Nº {{$uh}}{{$classe->classe}}, no período das 15:00 horas do dia {{ $data_inicio }} às 12:00 do {{ $data_termino }}.
<br>
<br>
{!!$corpo!!}
<br>
<br>
<br>
Att,
<br>
<br>
{{ $assinaturaCMT->posto->sigla }} {{ $assinaturaCMT->nome }} - {{ $assinaturaCMT->funcao }}
<br>
{{ $assinaturaGestor->posto->sigla }} {{ $assinaturaGestor->nome }} - {{ $assinaturaGestor->funcao }}
@endcomponent

<center>
{{ config('app.name') }}
</center>
<br>
<div style="color: red; text-align: center;">
<font size="5px;"><b>Atenção! Não responder este e-mail!</b></font>
</div>
@endcomponent
