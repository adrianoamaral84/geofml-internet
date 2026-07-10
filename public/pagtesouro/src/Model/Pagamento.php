<?php

namespace PagTesouro\Model;

use \Waavi\Sanitizer\Sanitizer;
use JsonSerializable;

class Pagamento implements JsonSerializable
{

    private $ug;
    private $codigoServico = "";
    private $referencia = "";
    private $competencia = "";
    private $vencimento = "";
    private $cnpjCpf = "";
    private $tipoPessoa = "PF";
    private $nomeContribuinte = "";
    private $valorPrincipal = "";
    private $valorDescontos = "";
    private $valorOutrasDeducoes = "";
    private $valorMulta = "";
    private $valorJuros = "";
    private $valorOutrosAcrescimos = "";


    public function jsonSerialize()
    {
        return [
            "ug" => $this->ug,
            "codigoServico" => $this->codigoServico,
            "referencia" => $this->referencia,
            "competencia" => $this->competencia,
            "vencimento" => $this->vencimento,
            "tipoPessoa" => $this->tipoPessoa,
            "cnpjCpf" => $this->cnpjCpf,
            "nomeContribuinte" => $this->nomeContribuinte,
            "valorPrincipal" => $this->valorPrincipal,
            "valorDescontos" => $this->valorDescontos,
            "valorOutrasDeducoes" => $this->valorOutrasDeducoes,
            "valorMulta" => $this->valorMulta,
            "valorJuros" => $this->valorJuros,
            "valorOutrosAcrescimos" => $this->valorOutrosAcrescimos,
        ];
    }

    public function fromArray($post)
    {
        $post = $this->sanitize($post);
        $this->ug = $post['ug'];
        $this->codigoServico = $post['codigoServico'];
        $this->referencia = $post['referencia'] ?: "";
        $this->competencia = $post['competencia'] ?: "";
        $this->vencimento = $post['vencimento'] ?: "";
        $this->cnpjCpf = $post['cnpjCpf'] ?: "";
        $this->tipoPessoa = $post['tipoPessoa'] ?:  "PF";
        $this->nomeContribuinte = preg_replace('/[0-9]+/', '', $post['nomeContribuinte']);;
        $this->valorPrincipal = $post['valorPrincipal'] ?:  "0,00";
        $this->valorDescontos = $post['valorDescontos'] ?:  "0,00";
        $this->valorOutrasDeducoes = $post['valorOutrasDeducoes'] ?:  "0,00";
        $this->valorMulta = $post['valorMulta'] ?:  "0,00";
        $this->valorJuros = $post['valorJuros'] ?:  "0,00";
        $this->valorOutrosAcrescimos = $post['valorOutrosAcrescimos'] ?:  "0,00";
    }

    public function fromObject($obj)
    {
        $this->fromArray((array) $obj);
    }




    public function sanitize($data)
    {

        $filters = [
            'codigoServico' => 'trim|escape|digit',
            'referencia' => 'trim|escape',
            'competencia' => 'trim|escape',
            'vencimento' => 'trim|escape',
            'cnpjCpf' => 'trim|escape',
            'tipoPessoa' => 'trim|escape',
            'nomeContribuinte' => 'trim|escape|capitalize',
            'valorPrincipal' => 'trim|escape',
            'valorDescontos' => 'trim|escape',
            'valorOutrasDeducoes' => 'trim|escape',
            'valorMulta' => 'trim|escape',
            'valorJuros' => 'trim|escape',
            'valorOutrosAcrescimos' => 'trim|escape',
        ];

        $sanitizer  = new Sanitizer($data, $filters);
        return $sanitizer->sanitize();
    }

   


    public function getUg()
    {
        return $this->ug;
    }

    public function setUg($ug)
    {
        $this->ug = $ug;
        return $this;
    }

    public function getCodigoServico()
    {
        return $this->codigoServico;
    }

    public function setCodigoServico($codigoServico)
    {
        $this->codigoServico = $codigoServico;

        return $this;
    }

    public function getReferencia()
    {
        return $this->referencia;
    }

    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;

        return $this;
    }

    public function getCompetencia()
    {
        return $this->competencia;
    }

    public function setCompetencia($competencia)
    {
        $this->competencia = $competencia;

        return $this;
    }

    public function getVencimento()
    {
        return $this->vencimento;
    }

    public function setVencimento($vencimento)
    {
        $this->vencimento = $vencimento;

        return $this;
    }

    public function getCleanCnpjCpf()
    {
        $retorno = str_replace("/", "", $this->getCnpjCpf());
        $retorno = str_replace(".", "", $retorno);
        $retorno = str_replace("-", "", $retorno);
        return $retorno;
    }
    public function getCnpjCpf()
    {
        return $this->cnpjCpf;
    }

    public function setCnpjCpf($cnpjCpf)
    {
        $this->cnpjCpf = $cnpjCpf;

        return $this;
    }

    public function getNomeContribuinte()
    {
        return $this->nomeContribuinte;
    }

    public function setNomeContribuinte($nomeContribuinte)
    {
        $this->nomeContribuinte = $nomeContribuinte;

        return $this;
    }

    public function getValorPrincipal()
    {
        
        return $this->valorPrincipal;
    }


    public function setValorPrincipal($valorPrincipal)
    {
        $this->valorPrincipal = $valorPrincipal;

        return $this;
    }

    public function getValorDescontos()
    {
        return $this->valorDescontos;
    }


    public function setDescontos($valorDescontos)
    {
        $this->valorDescontos = $valorDescontos;

        return $this;
    }

    public function getValorOutrasDeducoes()
    {
        return $this->valorOutrasDeducoes;
    }


    public function setOutrasDeducoes($valorOutrasDeducoes)
    {
        $this->valorOutrasDeducoes = $valorOutrasDeducoes;

        return $this;
    }

    public function getValorMulta()
    {

        return $this->valorMulta;
    }


    public function setMulta($valorMulta)
    {
        $this->valorMulta = $valorMulta;

        return $this;
    }

    public function getValorJuros()
    {
        return $this->valorJuros;
    }


    public function setJuros($valorJuros)
    {
        $this->valorJuros = $valorJuros;

        return $this;
    }

    public function getValorOutrosAcrescimos()
    {
        return $this->valorOutrosAcrescimos;
    }


    public function setOutrosAcrescimos($valorOutrosAcrescimos)
    {
        $this->valorOutrosAcrescimos = $valorOutrosAcrescimos;

        return $this;
    }

    public function getTipoPessoa()
    {
        return $this->tipoPessoa;
    }
}
