@component('mail::message')

<h1>Sr(a), {{ $posto }} {{ $nome }}</h1>
<br>
<center>
<div style="color: red; font-size: 16px;">
<b>{!!$cabecalho!!}</b>
</div>
</center>
<br>

@component('mail::button', ['url' => 'https://geofml.5rm.eb.mil.br', 'color' => 'green'])
Acessar o Sistema
@endcomponent

@component('mail::panel')

{!! $corpo !!}

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