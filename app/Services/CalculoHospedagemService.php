<?php

namespace App\Services;

use App\Hospede;
use App\Horario;
use Carbon\Carbon;
use DateTime;
use DateInterval;

class CalculoHospedagemService
{
    public function calcular(Hospede $hospedagem)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $horarios = Horario::first();

        $valorTarifa = $hospedagem->valorTarifaComDesconto();

        $checkinAt = $hospedagem->checkin_at
            ? Carbon::parse($hospedagem->checkin_at)
            : Carbon::parse($hospedagem->data_inicio);

        $agora = Carbon::now();

        $dias = $checkinAt->diffInDays($agora);

        if ($dias == 0) {
            $dias = 1;
        }

        $checkinAntecipado = false;
        $checkoutAtrasado = false;

        $horaCheckin = $checkinAt->format('H:i:s');
        $horaAgora = $agora->format('H:i:s');

        $entrada = Carbon::parse($horarios->entrada);
        $saida = Carbon::parse($horarios->saida);

        $entradaComTolerancia = $entrada->copy()->subHours($horarios->tolerancia);
        $saidaComTolerancia = $saida->copy()->addHours($horarios->tolerancia);

       $dataInicio = Carbon::parse($hospedagem->data_inicio);
$dataCheckin = Carbon::parse($hospedagem->checkin_at);

// Só é check-in antecipado se entrou NO MESMO DIA da reserva
// antes do horário permitido.
if (
    $dataCheckin->toDateString() == $dataInicio->toDateString() &&
    $dataCheckin->format('H:i:s') < $entradaComTolerancia->format('H:i:s')
) {
    $checkinAntecipado = true;
    $dias++;
}

       $dataTermino = Carbon::parse($hospedagem->data_termino);

       // Só é check-out atrasado se saiu NO MESMO DIA da reserva
/*
if (
    $agora->gte($dataTermino->copy()->startOfDay()) &&
    $agora->format('H:i:s') > $saidaComTolerancia->format('H:i:s')
) {
    $checkoutAtrasado = true;
    $dias++;
}
*/


// Cobra smepre que o checkout for feito depois do horário permitido, mesmo que seja no dia seguinte.
if ($agora->format('H:i:s') > $saidaComTolerancia->format('H:i:s')) {
    $checkoutAtrasado = true;
    $dias++;
}

        $valorTotal = round($valorTarifa * $dias, 2);

        $valorPago = $hospedagem->valor_pago ?? 0;

        $valorRestante = round($valorTotal - $valorPago, 2);

        if ($valorRestante < 0) {
            $valorRestante = 0;
        }

        return [
            'dias' => $dias,
            'valor_tarifa' => $valorTarifa,
            'valor_total' => $valorTotal,
            'valor_pago' => $valorPago,
            'valor_restante' => $valorRestante,
            'checkin_antecipado' => $checkinAntecipado,
            'checkout_atrasado' => $checkoutAtrasado,
        ];
    }
}