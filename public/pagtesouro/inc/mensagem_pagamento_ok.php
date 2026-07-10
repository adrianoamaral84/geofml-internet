<?php

use PagTesouro\Helper\SessionHelper;
?>

<?php if (SessionHelper::temSessaoPagamentoOK()) { ?>
    <div class="alert alert-success fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="alert-heading">Solicitação Gerada Com Sucesso!</h4>
        <hr>
        <p><a class="btn btn-primary btn-lg" href="https://pagtesouro.tesouro.gov.br/#/pagamento?idSessao=<?= SessionHelper::getSessaoPagamentoOK() ?>" target="_blank">Ir para a Página de Pagamento</a></p>
    </div>
<?php
    SessionHelper::unsetSessaoPagamentoOK();
}
