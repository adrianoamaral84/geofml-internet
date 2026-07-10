<!--
    NÃO MEXA NESTE ARQUIVO SE VOCÊ NÃO TIVER EXPERIÊNCIA COM DESENVOLVIMENTO DE SOFTWARE
    DÚVIDAS OU SUGESTÕES: rafael.87@gmail.com
-->

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';

use PagTesouro\Model\Pagamento;
use PagTesouro\Service\Starter;
use PagTesouro\Helper\SessionHelper;
use PagTesouro\Service\Configuracoes;

SessionHelper::startSession();
$starter = new Starter();

$conf = $starter->carregaArquivoDeConfiguracao();
$ug = $starter->getUgSelecionada($conf);
$pagamento = $starter->getPagamento($ug);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <title><?= Configuracoes::$nomeSoftware ?> - v<?= Configuracoes::$versaoSoftware; ?> - <?= $conf->getSiglaOm(); ?></title>
</head>

<body>

    <div class="container">
        <div class="jumbotron">
            <div class="d-flex align-items-center">
                <div id="select-ugs">
                    <?php include_once('select_ugs.php'); ?>
                </div>
                <div class="mr-4">
                    <img src="./img/logo.png" alt="">
                </div>
                <div>
                    <h6>PagueTesouro <sup>(v<?= $conf::$versaoSoftware ?>)</sup></h6>
                    <h1><?= $conf->getNomeOm() ?></h1>
                    <h3><b>UG: </b><?= $ug->getCodigoUg() ?></h3>
                </div>
            </div>
        </div>

        <?php include_once('./inc/mensagem_de_erro.php'); ?>
        <?php include_once('./inc/mensagem_de_sucesso.php'); ?>
        <?php include_once('./inc/mensagem_pagamento_ok.php'); ?>