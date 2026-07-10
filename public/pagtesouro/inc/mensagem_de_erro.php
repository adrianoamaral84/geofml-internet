<?php

use PagTesouro\Helper\SessionHelper;

if (SessionHelper::temMensagemDeErro()) {
    $errors = SessionHelper::getMensagensDeErro();
    if (sizeof($errors) > 0) {

?>

        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="alert-heading">Alguns ajustes são necessários...</h4>
            <hr>
            <?php
            foreach ($errors as $erro) {
            ?>
                <p>⦁ <b><?= $erro['codigo']; ?></b> - <?= $erro['mensagem']; ?></p>
            <?php
            }
            ?>
        </div>

<?php
        unset($mensagemErro);
        SessionHelper::unsetMensagensDeErro();
    }
}
?>