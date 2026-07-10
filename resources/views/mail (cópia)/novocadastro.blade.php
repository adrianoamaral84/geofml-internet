@component('mail::message')

<h1>Olá {{ $user->name }}</h1>
<br>
<center>
<div style="color: green; font-size: 16px;">
Seus dados de acesso no Portal GEOFML!
</div>
</center>
<br>
<br><center>
Login: {{ $user->cpf }}
<br>
Senha: <b>{{ $user->cpf }}</b>
</center>
<br>
<center><small>Clique no botão abaixo para acessar o sistema!</small>
<br>
<b>
<a href="https://geofml.5rm.eb.mil.br">
Clique aqui para acessar o Portal
</a>
</b>

</center>


@component('mail::button', ['url' => 'https://geofml.5rm.eb.mil.br', 'color' => 'green'])
Acessar o Sistema
@endcomponent

@component('mail::panel')
Acesse o portal clicando no botão, e realize o login para acessar o sistema do Forte Marechal Luz.
@endcomponent
<center>

</center>
<br>
<div style="color: red; text-align: center;">
<small>Atenção! Não responder este e-mail!</small>
</div>
@endcomponent
