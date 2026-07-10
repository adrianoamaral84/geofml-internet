<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class CpfInvalidoException extends PagTesouroException
{
    public function __construct()
    {
        parent::__construct("CPF Inválido", "Verifique os dados e tente novamente");
    }
}
