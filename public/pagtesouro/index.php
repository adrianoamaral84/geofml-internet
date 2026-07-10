<?php
// NÃO MEXA NESTE ARQUIVO SE VOCÊ NÃO TIVER EXPERIÊNCIA COM DESENVOLVIMENTO DE SOFTWARE
// DÚVIDAS OU SUGESTÕES: rafael.87@gmail.com    

use PagTesouro\Helper\UrlHelper;



!empty($_GET['nome'])? $nome = $_GET['nome'] : $nome = '';
!empty($_GET['cpf'])? $cpf = $_GET['cpf'] : $cpf = '';
!empty($_GET['valor'])? $valor = $_GET['valor'] : $valor = '';
!empty($_GET['data'])? $data = $_GET['data'] : $data = '';

/*
if(isset($_GET['nome']) or !empty($_GET['nome']) and $_GET['cpf'] and $_GET['valor']){
$nome = $_GET['nome'];
$cpf = $_GET['cpf']; 
$valor = $_GET['valor'];
}else{
    $nome = '';
    $cpf = '';
    $valor = '';
}
*/
if($_POST){
    var_dump($_POST);
}
?>

<?php require_once('./inc/header.php') ?>
<form action="process.php" method="post">

    <input name="codigoUg" id="codigoUg" type="hidden" value="<?= $ug->getCodigoUg() ?>" />
    <div class="row">
        <div class="col col-md-6">
            <div class="form-group">
                <label for="codigoServico">Código do Serviço</label>
                <?php
                if (UrlHelper::temServico($ug->getServicos())) {
                    include_once('./inc/texto_servico.php');
                } else {
                    include_once('./inc/select_servicos.php');
                }

                ?>
            </div>
            <div class="form-group">
                <label for="nomeContribuinte">Nome do Contribuinte</label>
                <input name="nomeContribuinte" value="<?= $nome; ?>" id="nomeContribuinte" class="form-control" type="text" maxlength="45" size="45" required>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoPessoa" id="radioPF" value="PF" <?= $pagamento->getTipoPessoa() == "PF" ? 'checked' : '' ?>>
                <label class="form-check-label" for="radioPF">Pessoa Física</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoPessoa" id="radioPJ" value="PJ" <?= $pagamento->getTipoPessoa() == "PJ" ? 'checked' : '' ?>>
                <label class="form-check-label" for="radioPJ">Pessoa Jurídica</label>
            </div>
            <div class="form-group">
                <input name="cnpjCpf" value="<?= $cpf; ?>" id="cnpjCpf" class="form-control cpfcnpj" type="text" required>
            </div>
            <div class="form-group">
                <label for="referencia">Referência (Numérico)</label>
                <input name="referencia" value="<?= $pagamento->getReferencia() ?>" id="referencia" class="form-control" min="0" type="text" maxlength="20">
            </div>
            <div class="form-group">
                <label for="competencia">Competência</label>
                <input name="competencia" value="<?= $pagamento->getCompetencia() ?>" id="competencia" class="form-control" type="text" placeholder="MM/AAAA" maxlength="7">
            </div>
            <div class="form-group">
                <label for="vencimento">Vencimento</label>
                <input name="vencimento" value="<?= $data; ?>" id="vencimento" class="form-control" type="text" placeholder="DD/MM/AAAA" maxlength="10">
            </div>

        </div> <!-- fim primeira coluna -->
        <div class="col-md-6">

            <div class="form-group">
                <label for="valorPrincipal">Valor Principal</label>
                <input name="valorPrincipal" value="<?= $valor; ?>" id="valorPrincipal" class="form-control currency" type="text" placeholder="0,00" required>
            </div>
            <div class=" form-group">
                <label for="valorDescontos">Descontos</label>
                <input name="valorDescontos" value="<?= $pagamento->getValorDescontos() ?>" id="valorDescontos" class="form-control currency" type="text" placeholder="0,00">
            </div>
            <div class=" form-group">
                <label for="valorOutrasDeducoes">Outras Deduções</label>
                <input name="valorOutrasDeducoes" value="<?= $pagamento->getValorOutrasDeducoes() ?>" id="valorOutrasDeducoes" class="form-control currency" type="text" placeholder="0,00">
            </div>
            <div class=" form-group">
                <label for="valorMulta">Multa</label>
                <input name="valorMulta" value="<?= $pagamento->getValorMulta() ?>" id="valorMulta" class="form-control currency" type="text" placeholder="0,00">
            </div>
            <div class=" form-group">
                <label for="valorJuros">Juros</label>
                <input name="valorJuros" value="<?= $pagamento->getValorJuros() ?>" id="valorJuros" class="form-control currency" type="text" placeholder="0,00">
            </div>
            <div class=" form-group">
                <label for="valorOutrosAcrescimos">Outros Acréscimos</label>
                <input name="valorOutrosAcrescimos" value="<?= $pagamento->getValorOutrosAcrescimos() ?>" id="valorOutrosAcrescimos" class="form-control currency" type="text" placeholder="0,00">
            </div>
        </div>

    </div>
    <div class="d-flex justify-content-center">
        <button type="submit" class="btn btn-lg btn-primary">Gerar Solicitação</button>
    </div>
</form>

<?php require_once('./inc/footer.php') ?>