<?php

namespace PagTesouro\Model;

use JsonSerializable;
use PagTesouro\Model\Servico;

class Ug implements JsonSerializable
{
    private $codigoUg;
    private $token;
    private $servicos = [];

    public function addServico(Servico $s)
    {
        array_push($this->servicos, $s);
    }

    public function getCodigoUg()
    {
        return $this->codigoUg;
    }

    public function setCodigoUg($codigoUg)
    {
        $this->codigoUg = $codigoUg;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getServicos()
    {
        return $this->servicos;
    }

    public function setServicos($servicos)
    {
        $this->servicos = $servicos;

        return $this;
    }

    public function __toString()
    {
        return $this->getCodigoUg();
    }

    public function jsonSerialize()
    {
        return [
            "codigo" => $this->codigoUg,
        ];
    }
}
