@component('mail::message')

<h1>Sr(a), {{ $user->name }}</h1>
<br>
<center>
<div style="color: green; font-size: 16px;">
Solicitação de acesso ao Portal GEOFML
</div>
</center>
<br>

<p>
Por segurança, o GEOFML não envia senhas por e-mail e não utiliza o CPF como senha.
</p>

<p>
Seu login é o CPF cadastrado. Defina ou recupere sua senha utilizando o fluxo seguro do portal.
</p>

@component('mail::button', ['url' => url('/password/reset'), 'color' => 'green'])
Definir / Recuperar Senha
@endcomponent

@component('mail::panel')
Após definir sua senha, acesse o portal e conclua os dados necessários para utilização do sistema do Forte Marechal Luz.
@endcomponent

<br>
<div style="color: red; text-align: center;">
<font size="5px;"><b>Atenção! Não responder este e-mail!</b></font>
</div>
@endcomponent
