@component('mail::message')

<h1>Sr(a), {{ $posto}} {{ $user->name }}, </h1>
<br>

<center>
<div style="color: red; font-size: 16px;">
<b>Seu pedido foi negado!</b>
<br>
</div>
</center>
<br>
<div style="font-size: 17px;">

@component('mail::panel')
Sua Solicitação de acesso ao Sistema foi negada.<br>
Motivo: <b>{{ $user->motivo }}</b>
<br>

Favor entrar no sistema para corrigir seus dados ou entrar em contato com a seção do FML do Forte do Pinheirinho para maiores informações!
@endcomponent



</div>
<br>
<br>
<br>
<div style="color: red; text-align: center;">
<font size="5px;"><b>Atenção! Não responder este e-mail!</b></font>
</div>
@endcomponent