<?php

namespace App\Services;

use App\Hospede;
use App\Horario;
use Carbon\Carbon;
use RuntimeException;

class CalculoHospedagemService
{
    public function calcular(Hospede $hospedagem)
    {
        $timezone = 'America/Sao_Paulo';

        $horarios = Horario::first();

        if (!$horarios) {
            throw new RuntimeException(
                'Os horarios de entrada, saida e tolerancia nao estao configurados.'
            );
        }

        $valorTarifa = (float) $hospedagem->valorTarifaComDesconto();
        $tolerancia = (int) $horarios->tolerancia;

        $dataInicio = Carbon::parse(
            $hospedagem->data_inicio,
            $timezone
        )->startOfDay();

        $dataTermino = Carbon::parse(
            $hospedagem->data_termino,
            $timezone
        )->startOfDay();

        $diariasContratadas = $dataInicio->diffInDays($dataTermino);

        if ($diariasContratadas < 1) {
            $diariasContratadas = 1;
        }

        $extraEntrada = 0;
        $extraSaida = 0;

        $checkinAntecipado = false;
        $checkoutAtrasado = false;

        if ($hospedagem->checkin_at) {
            $checkinAt = Carbon::parse(
                $hospedagem->checkin_at,
                $timezone
            );

            $limiteEntrada = $dataInicio
                ->copy()
                ->setTimeFromTimeString($horarios->entrada)
                ->subHours($tolerancia);

            if ($checkinAt->copy()->startOfDay()->lt($dataInicio)) {
                $extraEntrada = $checkinAt
                    ->copy()
                    ->startOfDay()
                    ->diffInDays($dataInicio);

                $checkinAntecipado = true;
            } elseif (
                $checkinAt->isSameDay($dataInicio) &&
                $checkinAt->lt($limiteEntrada)
            ) {
                $extraEntrada = 1;
                $checkinAntecipado = true;
            }
        }

        $momentoFinal = $hospedagem->checkout_at
            ? Carbon::parse($hospedagem->checkout_at, $timezone)
            : Carbon::now($timezone);

        $limiteCheckoutReserva = $dataTermino
            ->copy()
            ->setTimeFromTimeString($horarios->saida)
            ->addHours($tolerancia);

        if ($momentoFinal->gt($limiteCheckoutReserva)) {
            $checkoutAtrasado = true;

            $extraSaida = $dataTermino->diffInDays(
                $momentoFinal->copy()->startOfDay()
            );

            $limiteCheckoutDoDia = $momentoFinal
                ->copy()
                ->startOfDay()
                ->setTimeFromTimeString($horarios->saida)
                ->addHours($tolerancia);

            if ($momentoFinal->gt($limiteCheckoutDoDia)) {
                $extraSaida++;
            }
        }

        $dias = $diariasContratadas + $extraEntrada + $extraSaida;

        $valorTotal = round($valorTarifa * $dias, 2);

        $valorPago = (float) ($hospedagem->valor_pago ?? 0);

        $valorRestante = round($valorTotal - $valorPago, 2);

        if ($valorRestante < 0) {
            $valorRestante = 0;
        }

        return [
            'dias' => $dias,
            'diarias_contratadas' => $diariasContratadas,
            'diarias_extra_entrada' => $extraEntrada,
            'diarias_extra_saida' => $extraSaida,
            'valor_tarifa' => $valorTarifa,
            'valor_total' => $valorTotal,
            'valor_pago' => $valorPago,
            'valor_restante' => $valorRestante,
            'checkin_antecipado' => $checkinAntecipado,
            'checkout_atrasado' => $checkoutAtrasado,
        ];
    }
}
