<?php

namespace PagTesouro\Exception;

use Exception;

abstract class PagTesouroException extends Exception
{
    protected $codigo;
    protected $mensagem;
    protected $porNaSession;


    public function __construct($codigo, $mensagem, $porNaSession = true)
    {
        $this->codigo = $codigo;
        $this->mensagem = $mensagem;
        $this->porNaSession = $porNaSession;
    }


    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getMensagem()
    {
        return $this->mensagem;
    }
}
