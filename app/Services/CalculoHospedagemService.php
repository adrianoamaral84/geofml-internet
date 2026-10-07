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
                'Os horários de entrada, saída e tolerância não estão configurados.'
            );
        }

        $valorTarifa = (float) $hospedagem->valorTarifaComDesconto();
        $tolerancia = (int) $horarios->tolerancia;

        /*
         * Datas oficiais da reserva.
         *
         * Estas datas determinam a quantidade mínima de
         * diárias contratadas.
         */
        $dataInicio = Carbon::parse(
            $hospedagem->data_inicio,
            $timezone
        )->startOfDay();

        $dataTermino = Carbon::parse(
            $hospedagem->data_termino,
            $timezone
        )->startOfDay();

        /*
         * 05/07 até 07/07 = 2 diárias.
         *
         * Não utiliza checkin_at aqui, porque chegar atrasado
         * não reduz as diárias contratadas.
         */
        $diariasContratadas = $dataInicio->diffInDays($dataTermino);

        if ($diariasContratadas < 1) {
            $diariasContratadas = 1;
        }

        $extraEntrada = 0;
        $extraSaida = 0;

        $checkinAntecipado = false;
        $checkoutAtrasado = false;

        /*
         * ----------------------------------------------------
         * ENTRADA ANTECIPADA
         * ----------------------------------------------------
         */

        if ($hospedagem->checkin_at) {
            $checkinAt = Carbon::parse(
                $hospedagem->checkin_at,
                $timezone
            );

            /*
             * Exemplo:
             *
             * Entrada normal: 15:00
             * Tolerância: 2 horas
             * Entrada permitida: a partir das 13:00
             */
            $limiteEntrada = $dataInicio
                ->copy()
                ->setTimeFromTimeString($horarios->entrada)
                ->subHours($tolerancia);

            /*
             * Caso 1:
             * entrou em uma data anterior à data da reserva.
             *
             * Exemplo:
             * reserva começa em 05/07;
             * entrou em 04/07;
             * cobra 1 diária adicional.
             */
            if ($checkinAt->copy()->startOfDay()->lt($dataInicio)) {
                $extraEntrada = $checkinAt
                    ->copy()
                    ->startOfDay()
                    ->diffInDays($dataInicio);

                $checkinAntecipado = true;
            }

            /*
             * Caso 2:
             * entrou no mesmo dia da reserva, mas antes do
             * horário permitido com tolerância.
             *
             * Exemplo:
             * limite de entrada: 13:00;
             * entrou às 10:56;
             * cobra 1 diária adicional.
             */
            elseif (
                $checkinAt->isSameDay($dataInicio) &&
                $checkinAt->lt($limiteEntrada)
            ) {
                $extraEntrada = 1;
                $checkinAntecipado = true;
            }

            /*
             * Caso 3:
             * reserva começou em 05/07 e entrou em 06/07.
             *
             * Isso é chegada atrasada.
             * Não adiciona e não remove diárias.
             */
        }

        /*
         * ----------------------------------------------------
         * SAÍDA ATRASADA
         * ----------------------------------------------------
         */

        /*
         * Quando houver checkout realizado, usa checkout_at.
         * Enquanto a hospedagem estiver aberta, usa o horário atual.
         */
        $momentoFinal = $hospedagem->checkout_at
            ? Carbon::parse($hospedagem->checkout_at, $timezone)
            : Carbon::now($timezone);

        /*
         * Exemplo:
         *
         * Saída normal: 12:00
         * Tolerância: 2 horas
         * Limite final: 14:00
         */
        $limiteCheckoutReserva = $dataTermino
            ->copy()
            ->setTimeFromTimeString($horarios->saida)
            ->addHours($tolerancia);

        if ($momentoFinal->gt($limiteCheckoutReserva)) {
            $checkoutAtrasado = true;

            /*
             * Conta quantas datas foram ultrapassadas.
             *
             * Saída prevista: 07/07
             * Continua até 08/07 antes das 14h
             * = 1 diária adicional.
             */
            $extraSaida = $dataTermino->diffInDays(
                $momentoFinal->copy()->startOfDay()
            );

            /*
             * Se também passou das 14h no dia atual,
             * acrescenta mais uma diária.
             *
             * 07/07 às 14:01 = 1 extra
             * 08/07 às 13:00 = 1 extra
             * 08/07 às 14:01 = 2 extras
             */
            $limiteCheckoutDoDia = $momentoFinal
                ->copy()
                ->startOfDay()
                ->setTimeFromTimeString($horarios->saida)
                ->addHours($tolerancia);

            if ($momentoFinal->gt($limiteCheckoutDoDia)) {
                $extraSaida++;
            }
        }

        /*
         * Resultado final.
         */
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