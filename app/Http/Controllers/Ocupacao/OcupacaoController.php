<?php

namespace App\Http\Controllers\Ocupacao;

use App\Http\Controllers\Controller;

class OcupacaoController extends Controller
{
    /**
     * Rota legada mantida apenas para compatibilidade do roteamento.
     * O GeoFML Internet não oferece o módulo de ocupação.
     */
    public function listaPorMes($mes)
    {
        abort(404);
    }
}
