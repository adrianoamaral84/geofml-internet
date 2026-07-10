<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class ErroDeRequisicaoException extends PagTesouroException
{
    public function __construct($codigo, $mensagem)
    {
        parent::__construct("API ($codigo)", $mensagem);
    }
}
