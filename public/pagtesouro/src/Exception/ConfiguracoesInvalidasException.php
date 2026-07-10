<?php

namespace PagTesouro\Exception;

use PagTesouro\Helper\SessionHelper;
use PagTesouro\Exception\PagTesouroException;

class ConfiguracoesInvalidasException extends PagTesouroException
{
    public function __construct($mensagem, $porNaSession = true)
    {
        parent::__construct("Configurações Inválidas", $mensagem, $porNaSession);
        if ($porNaSession) {
            SessionHelper::setMensagemDeErro($this->getCodigo(), $this->getMensagem());
        }
    }
}
