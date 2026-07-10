<?php

namespace PagTesouro\Helper;

use PagTesouro\Model\Pagamento;

class SessionHelper
{
    public static function startSession()
    {
        session_start();
    }

    public static function existePagamentoPreenchido(): bool
    {
        return isset($_SESSION['pagamento']);
    }

    public static function extraiPagamentoDaSession($ugSelecionada): Pagamento
    {
        $obj = json_decode($_SESSION['pagamento']);
        $pagamento = new Pagamento();
        $pagamento->fromObject($obj);

        $pagamento->setUg($ugSelecionada);

        return $pagamento;
    }

    public static function setPagamentoNaSession($pagamento)
    {
        $_SESSION['pagamento'] = json_encode($pagamento);
    }

    public static function unsetPagamento()
    {
        unset($_SESSION['pagamento']);
    }

    public static function getCodigoUg()
    {
        return $_SESSION['ug'];
    }

    public static function getCodigoUgOuVazio()
    {
        if (!isset($_SESSION['ug'])) {
            $_SESSION['ug'] = "";
        }
        return $_SESSION['ug'];
    }


    public static function setCodigoUg($codigoUg)
    {
        $_SESSION['ug'] = $codigoUg;
    }

    public static function jaTemUgNaSession()
    {
        return isset($_SESSION['ug']) && !empty($_SESSION['ug']);
    }

    public static function temMensagemDeErro(): bool
    {
        return isset($_SESSION['errors']);
    }

    public static function setMensagemDeErro($codigo, $mensagem)
    {
        if (!isset($_SESSION['errors'])) {
            $_SESSION['errors'] = [];
        }
        $error = array(
            'codigo' => $codigo,
            'mensagem' => $mensagem
        );

        array_push($_SESSION['errors'], $error);
    }

    public static function getMensagensDeErro()
    {
        return $_SESSION['errors'];
        
    }

    public static function unsetMensagensDeErro()
    {
        unset($_SESSION['errors']);
    }

    public static function setSessaoPagamentoOK($sessao)
    {
        $_SESSION['sessao'] = $sessao;
    }

    public static function unsetSessaoPagamentoOK()
    {
        unset($_SESSION['sessao']);
    }

    public static function getSessaoPagamentoOK()
    {
        return $_SESSION['sessao'];
    }

    public static function temSessaoPagamentoOK(): bool
    {
        return isset($_SESSION['sessao']);
    }

    public static function temMensagemDeSucesso(): bool
    {
        return isset($_SESSION['success_code']);
    }

    public static function setMensagemDeSucesso($codigo, $mensagem)
    {
        $_SESSION['success_code'] = $codigo;
        $_SESSION['success_msg'] = $mensagem;
    }


    public static function getMensagemDeSucesso()
    {
        $mensagem = [];
        $mensagem['codigo'] = $_SESSION['success_code'];
        $mensagem['mensagem'] = $_SESSION['success_msg'];
        return $mensagem;
    }

    public static function unsetMensagemDeSucesso()
    {
        unset($_SESSION['success_code']);
        unset($_SESSION['success_msg']);
    }
}
