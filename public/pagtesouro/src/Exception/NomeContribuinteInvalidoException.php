<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class NomeContribuinteInvalidoException extends PagTesouroException
{
    public function __construct()
    {
        parent::__construct("Nome do Contribuinte Inválido", "O nome não pode estar em branco e tem que ter no mínimo 2 caracteres (Ex.: Zé)");
    }
}
