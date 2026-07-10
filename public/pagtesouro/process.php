<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'vendor/autoload.php';


use PagTesouro\Model\Pagamento;
use PagTesouro\Helper\SessionHelper;
use PagTesouro\Service\Configuracoes;
use PagTesouro\Helper\NavigatorHelper;
use PagTesouro\Service\PagamentoService;
use PagTesouro\Exception\PagTesouroException;

$conf = new Configuracoes();

try {
    $pagamento = new Pagamento();
    $_POST['ug'] = $conf->getUgByCode($_POST['codigoUg']);
    $pagamento->fromArray($_POST);

    $service = new PagamentoService($pagamento);
    $service->processaRequisicao();
} catch (PagTesouroException $pagueTesouroException) {
    SessionHelper::setMensagemDeErro($pagueTesouroException->getCodigo(), $pagueTesouroException->getMensagem());
    NavigatorHelper::voltarParaPrincipal();
} catch (Throwable $e) {
    SessionHelper::setMensagemDeErro("00000", "Erro na aplicação: (" . $e->getMessage() . ").");
    NavigatorHelper::voltarParaPrincipal();
}
