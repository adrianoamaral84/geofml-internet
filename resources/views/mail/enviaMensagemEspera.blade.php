@component('mail::message')

<h1>Sr(a), {{ $posto }} {{ $nome }}</h1>
<br>
<center>
<div style="color: green; font-size: 16px;">
<b> Seu pedido de Hospedagem está na Fila de Espera!</b>
</div>
</center>
<br>
<br><center>
<b>Dados do Pedido</b>
</center>
<br>

<center>

@component('mail::table')

| Data Entrada	| Data Saída	| Tipo Unidade  | Diárias | Valor Diária |
|:-----------:|:-----------:|:------:|:------:|:------:|
|{{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}	|{{ \Carbon\Carbon::parse($data_termino)->format('d/m/Y') }}	|{{ $unidade }}	 | {{ $diarias }}	 |R$ {{ number_format($valortarifa, 2, ',', '.') }} |
@endcomponent


	
@component('mail::button', ['url' => 'https://geofml.5rm.eb.mil.br', 'color' => 'green'])
Acessar o Sistema
@endcomponent

</center>


@component('mail::panel')
{!! $corpo !!}
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