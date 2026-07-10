<?php

namespace PagTesouro\Exception;

use PagTesouro\Exception\PagTesouroException;

class FaltaCurlException extends PagTesouroException
{
    public function __construct()
    {
        parent::__construct("Biblioteca Ausente", "Por favor verifique se sua infraestrutura possui a biblioteca <b>cURL</b> instalada e ativa");
    }
}
