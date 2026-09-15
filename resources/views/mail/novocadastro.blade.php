@component('mail::message')

<h1>Sr(a), {{ $posto }} {{ $user->name }}</h1>
<br>
<center>
<div style="color: green; font-size: 16px;">
Seu cadastro no Portal GEOFML foi realizado com sucesso!
</div>
</center>
<br>
<br>
<center>
Login: <b>{{ $user->cpf }}</b>
</center>
<br>

@component('mail::panel')
Por segurança, sua senha não é enviada por e-mail e não corresponde ao seu CPF.

Você receberá uma mensagem separada com um link seguro para criar sua senha de acesso. Esse link possui prazo de validade.
@endcomponent

<center><small>Após definir sua senha, utilize o botão abaixo para acessar o sistema.</small></center>

@component('mail::button', ['url' => 'https://geofml.5rm.eb.mil.br', 'color' => 'green'])
Acessar o Sistema
@endcomponent

@component('mail::panel')
Utilize seu CPF como login e a senha que você definiu pelo link seguro de criação de senha.
@endcomponent

<br>
<div style="color: red; text-align: center;">
<font size="5px;"><b>Atenção! Não responder este e-mail!</b></font>
</div>
@endcomponent
