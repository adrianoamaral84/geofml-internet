@component('mail::message')

<h1>Sr(a), {{ $posto}} {{ $user->name }}</h1>
<br>
<center>
<div style="color: green; font-size: 17px;">
{!!$cabecalho!!}
<br>

</div>
<b>Agora </b> Você pode acessar o sistema para realizar sua reserva!
</center>
<br>
<br><center>
Login: {{ $user->cpf }}
<br>

</center>
<br>
<center><small>Clique no botão abaixo acessar o sistema!</small></center>


@component('mail::button', ['url' => 'https://geofml.5rm.eb.mil.br', 'color' => 'green'])
Acessar Portal
@endcomponent

@component('mail::panel')
{!!$corpo!!}
@endcomponent
<center>

</center>
<br>
<div style="color: red; text-align: center;">
<font size="5px;"><b>Atenção! Não responder este e-mail!</b></font>
</div>
@endcomponent
