<?php

namespace PagTesouro\Service;

use PagTesouro\Model\Pagamento;
use PagTesouro\Helper\SessionHelper;
use PagTesouro\Helper\NavigatorHelper;
use PagTesouro\Exception\PagTesouroException;
use PagTesouro\Exception\ConfiguracoesInvalidasException;

class Starter
{


    public function carregaArquivoDeConfiguracao(): Configuracoes
    {
        try {
            $conf = new Configuracoes();
            return $conf;
        } catch (ConfiguracoesInvalidasException $e) {
            NavigatorHelper::vaiParaAviso();
        }
    }

    public function getUgSelecionada(Configuracoes $conf)
    {
        try {
            $ug = $conf->getUgSelecionada();
            return $ug;
        } catch (PagTesouroException $e) {
            SessionHelper::setMensagemDeErro($e->getCodigo(), $e->getMensagem());
            NavigatorHelper::voltarParaPrincipal();
        }
    }

    public function getPagamento($ug)
    {
        if (SessionHelper::existePagamentoPreenchido()) {
            $pagamento = SessionHelper::extraiPagamentoDaSession($ug);
        } else {
            $pagamento = new Pagamento();
            $pagamento->setUg($ug);
        }

        return $pagamento;
    }
}
