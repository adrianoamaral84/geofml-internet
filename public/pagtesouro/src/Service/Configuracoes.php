<?php

namespace PagTesouro\Service;

use Exception;
use PagTesouro\Model\Ug;
use PagTesouro\Model\Servico;
use PagTesouro\Helper\UrlHelper;
use PagTesouro\Helper\SessionHelper;
use PagTesouro\Helper\NavigatorHelper;
use PagTesouro\Service\ValidadorConfiguracoes;
use PagTesouro\Exception\UgNaoEncontradaException;
use PagTesouro\Exception\ConfiguracoesInvalidasException;

class Configuracoes
{

    public static $nomeSoftware = "PagTesouro";
    public static $versaoSoftware = "1.1.0";
    private $nomeOm = "";
    private $siglaOm = "";
    private $ambiente = "";
    private $ugs = [];

    public function __construct()
    {
        try {
            $filePath = './config/config.json';
            if (!file_exists($filePath)) {
                $mensagem = "Arquivo de Configuração não encontrado. Por favor gere o arquivo de configuração, de acordo com as orientações no repositório github do projeto";
                throw new ConfiguracoesInvalidasException($mensagem);
            }
            $str = file_get_contents($filePath);
            $json = json_decode($str, true);
            $this->nomeOm = trim($json['nomeOm']) ?: "";
            $this->siglaOm =  trim($json['siglaOm']);
            $this->ambiente =  trim($json['ambiente']);
            $this->ugs = $this->loadUgs($json['ugs']);
        } catch (Exception $e) {
            $mensagem = "Não foi possível interpretar o arquivo de configuração. Por favor tente gerá-lo novamente";
            throw new ConfiguracoesInvalidasException($mensagem);
        }

        $validador = new ValidadorConfiguracoes();
        if ($validador->isNotValid($this)) {
            $mensagem = implode(" / ", $validador->getMensagens());
            throw new ConfiguracoesInvalidasException($mensagem, false);
        }
    }

    public function getUrl()
    {
        if ($this->ambiente == 'Produção') {
            return  'https://pagtesouro.tesouro.gov.br/api/gru/solicitacao-pagamento';
        }

        return 'https://valpagtesouro.tesouro.gov.br/api/gru/solicitacao-pagamento';
    }

    public function getNomeOm()
    {
        return $this->nomeOm;
    }

    public function getSiglaOm()
    {
        return $this->siglaOm;
    }

    public function getAmbiente()
    {
        return $this->ambiente;
    }

    public function getCodigosDeServico()
    {
        return $this->codigosDeServico;
    }

    public function getUgs()
    {
        return $this->ugs;
    }

    public function getUgSelecionada(): Ug
    {
        if (UrlHelper::temUg()) {
            return $this->getUgDaUrl();
        }

        if (SessionHelper::jaTemUgNaSession()) {
            return $this->getUgDaSession();
        }

        $primeiraUg = $this->ugs[0];
        $this->updateSessionUg($primeiraUg->getCodigoUg());
        return $primeiraUg;
    }


    public function getUgDaUrl()
    {
        $codUgSelecionada = UrlHelper::getUg();
        foreach ($this->ugs as $ug) {
            if ($ug->getCodigoUg() == $codUgSelecionada) {
                if (SessionHelper::getCodigoUg() != $ug->getCodigoUg()) {
                    $this->updateSessionUg($ug->getCodigoUg());
                }
                return $ug;
            }
        }

        SessionHelper::setMensagemDeErro("UG informada na URL não encontrada", "Utilizando a UG padrão.");

        if (SessionHelper::jaTemUgNaSession()) {
            return $this->getUgDaSession();
        }

        $primeiraUg = $this->ugs[0];
        $this->updateSessionUg($primeiraUg->getCodigoUg());
        return $primeiraUg;
    }


    public function getUgDaSession()
    {
        $codUgNaSession = SessionHelper::getCodigoUg();
        $ug = $this->getUgByCode($codUgNaSession);
        if ($codUgNaSession != $ug->getCodigoUg()) {
            $this->updateSessionUg($ug->getCodigoUg());
        }
        return $ug;
    }

    public function getUgByCode($codigo)
    {
        foreach ($this->ugs as $ug) {
            if ($ug->getCodigoUg() == $codigo) {
                if (SessionHelper::getCodigoUg() != $ug->getCodigoUg()) {
                    $this->updateSessionUg($ug->getCodigoUg());
                }
                return $ug;
            }
        }
        if (SessionHelper::getCodigoUg() != $this->ugs[0]->getCodigoUg()) {
            $this->updateSessionUg($ug->getCodigoUg());
        }
        return $this->ugs[0];
    }


    public function loadUgs($ugsDoJson)
    {
        foreach ($ugsDoJson as $ugJson) {
            $ug = new Ug();
            $ug->setCodigoUg($ugJson['codigo']);
            $ug->setToken($ugJson['token']);

            foreach ($ugJson['servicos'] as $servicoJson) {
                $servico = new Servico();
                $servico->setCodigoServico($servicoJson['codigo']);
                $servico->setDescricao($servicoJson['descricao']);

                $ug->addServico($servico);
            }

            array_push($this->ugs, $ug);
        }
        return $this->ugs;
    }

    private function updateSessionUg($codUgSelecionada)
    {
        if (SessionHelper::jaTemUgNaSession()) {
            SessionHelper::setMensagemDeSucesso($codUgSelecionada, "UG alterada com sucesso");
        }

        SessionHelper::setCodigoUg($codUgSelecionada);
        NavigatorHelper::voltarParaPrincipal();
    }
}
