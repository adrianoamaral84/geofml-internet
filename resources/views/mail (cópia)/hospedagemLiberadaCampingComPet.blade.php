@component('mail::message')

<h1>Olá, {{ $nome }}</h1>
<br>
<center>
<div style="color: green; font-size: 16px;">
<b>Sua Unidade foi Liberada para Uso!</b>
</div>
</center>
<br>
<br><center>
<b>Dados do Pedido</b>
</center>
<br>

<center>

@component('mail::table')

| Data Entrada	| Data Saída	| Tipo Unidade  | Nº | Valor  |
|:-----------:|:-----------:|:------:|:------:|:------:|
|{{ $data_inicio }}	|{{ $data_termino }}	|{{ $unidade }}	 |{{ $uh }}{{ $classe->classe }}| R$ {{ number_format($valor, 2, ',', '.') }} |
@endcomponent


	
@component('mail::button', ['url' => 'https://geofml.5rm.eb.mil.br', 'color' => 'green'])
Acessar o Sistema
@endcomponent

</center>


@component('mail::panel')

Informo que foi distribuído ao Senhor(a) a(o) {{ $unidade }} Nº {{$uh}}{{$classe->classe}}, no período das 15:00 horas do dia {{ $data_inicio }} às 12:00 do {{ $data_termino }}.
<br>
<br>
Solicitamos ao senhor(a) que a capacidade do BOX Camping não seja excedida, a fim de não gerar problemas de acomodação ao próprio usuário.
<br>
<br>
Caso seja necessário realizar o cancelamento da hospedagem, solicito ao senhor(a) que informe em até <b>4 dias de antecedência </b>do período previsto para ocupação.
<br>
<br>
A Área de Lazer do Forte Marechal Luz possui <b>link de internet de fibra óptica para os hóspedes.</b>
<br>
<br>
<b>O Camping possui uma área de cozinha coletiva mobiliada com uma geladeira individual por Box.</b>
<br>
<br>
<b>Conforme NGA de utilização do Forte Marechal Luz, informo ao senhor(a) que as roupas de cama, mesa e banho, alimentação, utensílios de praia e limpeza (produtos) do BOX Camping deverão ser providênciadas ou realizadas por conta do usuário.</b>
<br>
<br>
Em relação ao pagamento da hospedagem, informo ao senhor(a) que o pagamento através do PAGTESOURO deve ser realizado somente pelo PIX ou Cartão de Crédito cadastrado no mercado pago no Check-Out, e que o titular da reserva deve estar presente.
<br>
<br>
Conforme Normas Gerais de Ação do Forte Marechal Luz, informo ainda ao senhor(a) que <b><font color=red>está autorizada</font></b> a entrada e permanência de animais na UH que foi distribuída, <b>no limite de 02 (dois) animais.</b>
<br>
<br>
Que o Senhor(a) e sua família sejam bem-vindos e tenham uma excelente estadia no Forte Marechal Luz.
<br>
<br>
<br>
Att,
<br>
<br>
Cel AGNELO - Cmt B Adm Ap / 5ª RM - PD Cap R1 JUAREZ - Gestor de Reservas do FML.

@endcomponent

<center>
{{ config('app.name') }}
</center>
<br>
<div style="color: red; text-align: center;">
<small>Atenção! Não responder este e-mail!</small>
</div>
@endcomponent
