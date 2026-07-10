<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class CompetenciaInvalidaException extends PagTesouroException
{
    public function __construct()
    {
        parent::__construct("Data Competencia Inválida", "O valor competência deve ser um mês com 2 dígitos / um ano válido com 4 dígitos Verifique os dados e tente novamente");
    }
}
