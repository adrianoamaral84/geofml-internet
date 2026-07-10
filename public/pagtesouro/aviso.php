<?php
// NÃO MEXA NESTE ARQUIVO SE VOCÊ NÃO TIVER EXPERIÊNCIA COM DESENVOLVIMENTO DE SOFTWARE
// DÚVIDAS OU SUGESTÕES: rafael.87@gmail.com    
?>

<?php require_once('./inc/header_conf.php') ?>

<div class="container">


    <?php include_once('./inc/alerta_erro.php') ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">

        <h3>Para utilização do sistema é necessário o cadastramento de parâmetros</h3>
        <ol class="lista-parametros mt-4">
            <li>Nome da Organização</li>
            <li>Sigla da Organização</li>
            <li>Ambiente <span style="font-weight: normal">(se Produção ou Homologação)</span></li>
            <li>UGs (e para cada uma delas)
                <ul>
                    <li>Token de acesso gerado no SISGRU (Lembrando: Um token por UG)</li>
                    <li>Serviços vinculados àquela UG (com seu código e descrição)</li>
                </ul>
            </li>
        </ol>

        <hr>

        <h5> <b>Importante:</b> Após informar os dados baixe o arquivo de configuração (config.json) e solicite ao responsável técnico que coloque o arquivo na <b> pasta <u>config</u> </b>, sem renomeá-lo ou mudar seu conteúdo</h5>
        <div class="text-center mt-4 mb-4"> <a href="http://www.10rm.eb.mil.br/pagtesouro/config.php" class="btn btn-primary">Ir para a página de configuração</a> </div>
    </div>
</div>


<?php require_once('./inc/footer_conf.php') ?>