<?php

use PagTesouro\Helper\SessionHelper;

if (SessionHelper::temMensagemDeSucesso()) { 
    $mensagemSucesso = SessionHelper::getMensagemDeSucesso();
    ?>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="alert-heading"><?= $mensagemSucesso['codigo'] ?></h4>
        <hr>
        <p><?= $mensagemSucesso['mensagem']; ?></p>
    </div>

    <?php
    unset($mensagemSucesso);
    SessionHelper::unsetMensagemDeSucesso();
}
?>

