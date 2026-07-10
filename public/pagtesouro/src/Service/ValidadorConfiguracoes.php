<?php

namespace PagTesouro\Service;

use PagTesouro\Service\Configuracoes;



class ValidadorConfiguracoes
{

    private $mensagens = [];

    public function isValid(Configuracoes $conf): bool
    {
        $hasError = false;
        if (empty($conf->getNomeOm())) {
            $mensagem = "Nome da Organização não foi informado.";
            array_push($this->mensagens, $mensagem);
            $hasError = true;
        }
        if (empty($conf->getSiglaOm())) {
            $mensagem = "Sigla da Organização não foi informada.";
            array_push($this->mensagens, $mensagem);
            $hasError = true;
        }
        if (empty($conf->getUgs())) {
            $mensagem = "UGs da Organização não foram cadastradas.";
            array_push($this->mensagens, $mensagem);
            $hasError = true;
        }
        return !$hasError;
    }


    public function isNotValid(Configuracoes $conf): bool
    {
        return !$this->isValid($conf);
    }





    public function getMensagens()
    {
        return $this->mensagens;
    }

    public function setMensagens($mensagens)
    {
        $this->mensagens = $mensagens;

        return $this;
    }
}
