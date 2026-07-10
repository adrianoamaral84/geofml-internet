<!--

    NÃO MEXA NESTE ARQUIVO SE VOCÊ NÃO TIVER EXPERIÊNCIA COM DESENVOLVIMENTO DE SOFTWARE
    DÚVIDAS OU SUGESTÕES: rafael.87@gmail.com

    
-->

<?php

use PagTesouro\Service\Configuracoes;

require 'vendor/autoload.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/config.css">
    <title><?= Configuracoes::$nomeSoftware ?> - v<?= Configuracoes::$versaoSoftware; ?>  - Configuração</title>
</head>

<body>

    <div class="container">
        <div class="jumbotron">
            <div class="d-flex align-items-center">
                <div class="mr-4">
                    <img src="./img/logo.png" alt="">
                </div>
                <div>
                    <h1><?= Configuracoes::$nomeSoftware ?> - Configuração</h1>
                </div>
            </div>
        </div>
