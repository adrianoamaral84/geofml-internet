<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class CnpjInvalidoException extends PagTesouroException
{
    public function __construct()
    {
        parent::__construct("CNPJ Inválido", "Verifique os dados e tente novamente");
    }
}
