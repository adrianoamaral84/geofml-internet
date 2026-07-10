<select name="codigoServico" id="codigoServico" class="form-control" required>
    <option value="">Selecione um Serviço</option>
    <?php
    foreach ($ug->getServicos() as $servico) {
        $cod = $servico->getCodigoServico();
        $descricao = strtoupper($servico->getDescricao());
    ?>
        <option value="<?= $servico->getCodigoServico() ?>" <?= $pagamento->getCodigoServico() == $servico->getCodigoServico() ? 'selected' : '' ?>> <?= $servico->getCodigoServico() ?> - <?= $servico->getDescricao()  ?>
        </option>
    <?php
    }
    ?>
</select>