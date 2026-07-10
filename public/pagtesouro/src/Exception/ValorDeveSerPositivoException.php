<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class ValorDeveSerPositivoException extends PagTesouroException
{
    public function __construct($nomeCampo)
    {
        parent::__construct("Valor Inválido ($nomeCampo)", "O valor deve ser maior do que 0,00. Verifique os dados e tente novamente");
    }
}
