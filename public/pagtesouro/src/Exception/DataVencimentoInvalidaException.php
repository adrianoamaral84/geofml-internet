<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class DataVencimentoInvalidaException extends PagTesouroException
{
    public function __construct()
    {
        parent::__construct("Data de Vencimento Inválida", "A data deve ser uma data válida no futuro. Verifique os dados e tente novamente");
    }
}
