<?php

namespace App\Http\Controllers\CheckIn;

use App\Http\Controllers\Controller;

class CheckInController extends Controller
{
    private function legacyDisabled()
    {
        abort(404);
    }

    public function atendente()
    {
        return $this->legacyDisabled();
    }

    public function checkIn()
    {
        return $this->legacyDisabled();
    }

    public function checkOut()
    {
        return $this->legacyDisabled();
    }

    public function buscar()
    {
        return $this->legacyDisabled();
    }

    public function buscahospedagem()
    {
        return $this->legacyDisabled();
    }

    public function cadastrarProdutoCarrinho()
    {
        return $this->legacyDisabled();
    }

    public function adicionarProdutoHospedagem()
    {
        return $this->legacyDisabled();
    }

    public function deletarItem()
    {
        return $this->legacyDisabled();
    }

    public function finalizarCarrinho()
    {
        return $this->legacyDisabled();
    }
}
