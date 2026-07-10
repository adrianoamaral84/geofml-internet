@component('mail::message')

<h1>Olá, {{ $nome }}</h1>
<br>
<center>
<div style="color: red; font-size: 16px;">
<b> Seu pedido de Hospedagem foi Negado!</b>
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
O seu pedido de hospedagem foi negado, para maiores informações entre em contato com a Seção do FML.
@endcomponent

<center>
{{ config('app.name') }}
</center>
<br>
<div style="color: red; text-align: center;">
<small>Atenção! Não responder este e-mail!</small>
</div>
@endcomponent
