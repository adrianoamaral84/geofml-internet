<?php

namespace App\Services;

use App\Hospede;
use Carbon\Carbon;

class LimiteMensalPedidos
{
    /**
     * Limite unico por usuario e mes da entrada, independente do tipo de UH.
     * Alta temporada: 1 pedido. Baixa temporada: 2 pedidos.
     * Negados (1) e cancelados (6) nao consomem cota.
     * O proprio pedido e excluido quando se trata de edicao.
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

        $limite = $altaTemporada ? 1 : 2;

        return $consulta->count() >= $limite
            ? 'Você já alcançou o limite de ' . $limite . ' pedido(s) neste mês, independentemente do tipo de hospedagem.'
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
