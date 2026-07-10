<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class ValorInvalidoException extends PagTesouroException
{
    public function __construct($nomeCampo)
    {
        parent::__construct("Valor Inválido ($nomeCampo)", "O valor deve ser positivo em R$. Verifique os dados e tente novamente");
    }
}
