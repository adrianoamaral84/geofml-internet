<?php

namespace App\Services;

use App\Hospede;
use Carbon\Carbon;

class LimiteMensalPedidos
{
    /**
     * Valida separadamente UHs comuns e Camping/Motorhome.
     * Os pedidos negados (1) e cancelados pelo usuario (6) nao consomem cota.
     * A competencia e determinada pelo mes da data de entrada.
     */
    public static function mensagem(int $usuarioId, Carbon $entrada, int $tipoUh, bool $altaTemporada, int $limiteAlta, ?int $ignorarPedidoId = null): ?string
    {
        $consulta = Hospede::where('user_id', $usuarioId)
            ->whereYear('data_inicio', $entrada->year)
            ->whereMonth('data_inicio', $entrada->month)
            ->whereNotIn('status', [1, 6]);

        if ($ignorarPedidoId !== null) {
            $consulta->where('id', '<>', $ignorarPedidoId);
        }

        if (in_array($tipoUh, [11, 12], true)) {
            $total = (clone $consulta)->whereIn('tipo_und_id', [11, 12])->count();
            return $total >= 2
                ? 'Voce ja alcancou o limite de 2 pedidos para Camping e/ou Motor-Home neste mes.'
                : null;
        }

        $total = (clone $consulta)->whereNotIn('tipo_und_id', [11, 12])->count();
        $limite = $altaTemporada ? $limiteAlta : 2;

        return $total >= $limite
            ? 'Voce ja alcancou o limite de ' . $limite . ' pedido(s) de apartamento/casa neste mes.'
            : null;
    }
}
