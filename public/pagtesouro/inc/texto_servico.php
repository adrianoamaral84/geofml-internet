<?php

use PagTesouro\Helper\UrlHelper;

$codJaSelecionado = "";
if (UrlHelper::temServico($ug->getServicos())) {
    $codJaSelecionado = UrlHelper::getServico();
}


foreach ($ug->getServicos() as $servico) {
    if ($servico->getCodigoServico() == $codJaSelecionado) {
    ?>
        <input name="codigoServico" id="codigoServico" type="hidden" value="<?= $servico->getCodigoServico() ?>" />
        <h5><?= $servico->getCodigoServico() ?> - <?= $servico->getDescricao() ?></h5>
    <?php
    }
}
