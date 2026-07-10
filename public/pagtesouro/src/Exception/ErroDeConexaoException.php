<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class ErroDeConexaoException extends PagTesouroException
{
    public function __construct()
    {
        parent::__construct("Erro ao acessar o site do PagTesouro", "Entre em contato com o administrador.");
    }
}
