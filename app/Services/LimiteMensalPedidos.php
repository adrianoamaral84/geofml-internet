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
                ? 'Você já alcançou o limite de 2 pedidos de Camping e/ou Motorhome neste mês.'
                : null;
        }

        $total = (clone $consulta)->whereNotIn('tipo_und_id', [11, 12])->count();
        $limite = $altaTemporada ? $limiteAlta : 2;

        return $total >= $limite
            ? 'Você já alcançou o limite de ' . $limite . ' pedido(s) de apartamento/casa neste mês.'
            : null;
    }
    /**
     * Na alta temporada, outro pedido ativo com as mesmas datas
     * impede nova inscrição ou edição, independente da categoria.
     * Períodos parcialmente sobrepostos seguem a regra já existente.
     */
    public static function mensagemDuplicidadeAlta(int $usuarioId, Carbon $entrada, Carbon $saida, ?int $ignorarPedidoId = null): ?string
    {
        $consulta = Hospede::where('user_id', $usuarioId)
            ->whereDate('data_inicio', $entrada->format('Y-m-d'))
            ->whereDate('data_termino', $saida->format('Y-m-d'))
            ->whereNotIn('status', [1, 6]);

        if ($ignorarPedidoId !== null) {
            $consulta->where('id', '<>', $ignorarPedidoId);
        }

        return $consulta->exists()
            ? 'Você já possui outro pedido ativo para esse mesmo período na alta temporada.'
            : null;
    }

}
