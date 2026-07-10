@component('mail::message')

<center>
<div style="color: red; font-size: 20px;">
<b>Unidade foi cancelada pelo usuário!</b>
</div>
</center>
<br>
<br><center>
<b>Dados do Pedido</b>
</center>
<br>

<center>

@component('mail::table')

| Data Entrada	| Data Saída	| Tipo Unidade  | Nr | Valor  |
|:-----------:|:-----------:|:------:|:------:|:------:|
|{{ $data_inicio }}	|{{ $data_termino }}	|{{ $unidade }}	  | {{ $sigla }}{{ $classe }} | R$ {{ number_format($valor, 2, ',', '.') }} |
@endcomponent


	

</center>


@component('mail::panel')
A unidade <b>{{ $unidade }}</b> acaba de ser cancelada pelo usuário {{ $posto }} {{ $nome }}.

@endcomponent

<center>
{{ config('app.name') }}
</center>
<br>
<div style="color: red; text-align: center;">
<small>Atenção! Não responder este e-mail!</small>
</div>
@endcomponent