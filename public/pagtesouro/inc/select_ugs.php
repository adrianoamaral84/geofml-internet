<?php
if (sizeof($conf->getUgs()) > 1) {
?>
    <div id="div-select-ug" class="form-group">
    <label for="exampleFormControlSelect1">UGs Disponíveis:</label>
        <select data-current="<?= $ug->getCodigoUg() ?>" >
            <?php
            foreach ($conf->getUgs() as $ugLoop) {
            ?>
                <option value="<?= $ugLoop->getCodigoUg() ?>" <?= $ug->getCodigoUg() == $ugLoop->getCodigoUg() ? 'selected' : '' ?>>
                    <?= $ugLoop->getCodigoUg() ?>
                </option>

            <?php
            }
            ?>
        </select><button class="btn btn-sm btn-primary" id="btnMudarUg">Mudar</button>
    </div>
<?php
}
