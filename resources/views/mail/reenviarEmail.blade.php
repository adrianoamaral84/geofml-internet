@component('mail::message')

<h1>Sr(a), {{ $posto}} {{ $name }}, </h1>
<br>

<center>
<div style="color: red; font-size: 16px;">
<b>{!!$cabecalho!!}</b>

</div>

</center>
<br>
<div style="font-size: 17px;">

@component('mail::panel')
Sua Solicitação de hospedagem da data {{ $data_ini }} - {{ $data_final }} retornou para o reenvio do Comprovante.

<p>Motivo: <b>{{ $motivo }}</b></p>
@component('mail::button', ['url' => 'https://geofml.5rm.eb.mil.br', 'color' => 'green'])
Acessar o Sistema
@endcomponent
<p>{!! $corpo !!}
</p>
@endcomponent

</div>


<div style="color: red; text-align: center;">
<font size="5px;"><b>Atenção! Não responder este e-mail!</b></font>
</div>
@endcomponent