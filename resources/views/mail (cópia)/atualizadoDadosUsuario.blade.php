@component('mail::message')
<style type="text/css">
	
	ul {
  list-style: decimal; /* Remove default bullets */
}



</style>
<center>
<div style="color: red; font-size: 20px;">
<b>Alteração de dados!</b>
</div>
</center>
<br>
<br><center>
<b>Dados do Usuário</b>
</center>
<br>

<center>

@component('mail::table')

| P/G 		  |	   Nome		| CPF		  |
|:-----------:|:-----------:|:-----------:|
|{{ $posto}}  |	{{ $name }} | {{ $cpf }}  |

@endcomponent

@component('mail::table')

| Campos Alterados	|
|:-----------------:|
<ul>	
@forelse ($campos as $campo)
   <li>{{ $campo }}</li>
@empty
   <p>Sem Alteração</p>
@endforelse
</ul>


@endcomponent
	

</center>


<br><br>
<center>
{{ config('app.name') }}
</center>
<br>
<div style="color: red; text-align: center;">
<small>Atenção! Não responder este e-mail!</small>
</div>
@endcomponent