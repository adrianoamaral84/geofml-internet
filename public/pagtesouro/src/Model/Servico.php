<?php

namespace PagTesouro\Model;

class Servico
{
    private $codigoServico;
    private $descricao;

    public function getCodigoServico()
    {
        return $this->codigoServico;
    }

    public function setCodigoServico($codigoServico)
    {
        $this->codigoServico = $codigoServico;

        return $this;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    
}
