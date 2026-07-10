@component('mail::message')

<h1>Olá, {{ $nome }}</h1>
<br>
<center>
<div style="color: green; font-size: 16px;">
<b>Sua Unidade foi Liberada para Pagamento!</b>
</div>
</center>
<br>
<br><center>
<b>Dados do Pedido</b>
</center>
<br>

<center>

@component('mail::table')

| Data Entrada	| Data Saída	| Tipo Unidade  |  Diária  |  Valor  |
|:-------------:|:-------------:|:-------------:|:--------:|:-------:|
|{{ $data_inicio }}	|{{ $data_termino }}|{{ $unidade }}|R$ {{ number_format($valortarifa, 2, ',', '.') }}|R$ {{ number_format($valor, 2, ',', '.') }}|

@endcomponent


	
@component('mail::button', ['url' => 'https://geofml.5rm.eb.mil.br', 'color' => 'green'])
Acessar o Sistema
@endcomponent

</center>


@component('mail::panel')
Seu pedido foi atendido no período solicitado. <font color=red><b>O Sr(a) terá que efetuar o pagamento de 01 (uma) diária até 96 horas para confirmar
a sua reserva.</b></font>
Caso não efetue o pagamento sua solicitação será cancelada.
Consulte seu "STATUS" no perfil no sistema.
@endcomponent

<center>
{{ config('app.name') }}
</center>
<br>
<div style="color: red; text-align: center;">
<small>Atenção! Não responder este e-mail!</small>
</div>
@endcomponent
