<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class ValoresTotaisInvalidosException extends PagTesouroException
{
    public function __construct()
    {
        parent::__construct("Valores Inválidos", "A soma das deduções não pode ser maior do que os valoresa serem pagos");
    }
}
