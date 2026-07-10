<?php

namespace PagTesouro\Helper;

use Illuminate\Contracts\Session\Session;

class UrlHelper
{

    public static function temUg()
    {
        return isset($_GET['ug']) && !empty($_GET['ug']);
    }

    public static function getUg()
    {
        return $_GET['ug'];
    }


    public static function getUgOuVazio()
    {
        return  isset($_GET['ug']) ? $_GET['ug'] : "";
    }

    public static function temServico($listaServicos)
    {
        if (isset($_GET['servico']) && !empty($_GET['servico'])) {
            $codigoServicoDaUrl = UrlHelper::getServico();
            foreach ($listaServicos as $servico) {
                if ($servico->getCodigoServico() == $codigoServicoDaUrl) {
                    return true;
                }
            }
            SessionHelper::setMensagemDeErro("Serviço informado na URL não encontrada", "Mostrando a lista.");

        }

        return false;
    }

    public static function getServico()
    {
        return $_GET['servico'];
    }


    public static function temValor()
    {
        return isset($_GET['valor']) && !empty($_GET['valor']);
    }

    public static function getValor()
    {
        return $_GET['valor'];
    }

    public static function getValorOuVazio()
    {
        if (isset($_GET['valor'])) {
            return str_replace(".", ",", $_GET['valor']);
        }
        return "";
    }
}
