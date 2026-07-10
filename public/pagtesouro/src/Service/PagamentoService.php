<?php

namespace PagTesouro\Service;

use Illuminate\Contracts\Session\Session;
use PagTesouro\Model\Pagamento;
use PagTesouro\Helper\SessionHelper;
use PagTesouro\Service\Configuracoes;
use PagTesouro\Service\ValidadorPagamento;
use PagTesouro\Exception\FaltaCurlException;
use PagTesouro\Exception\ErroDeConexaoException;
use PagTesouro\Exception\ErroDeRequisicaoException;
use PagTesouro\Helper\InfraHelper;
use PagTesouro\Helper\NavigatorHelper;

class PagamentoService
{
    private $conf;
    private $pagamento;
    private $validador;

    public function __construct(Pagamento $pagamento)
    {
        $this->pagamento = $pagamento;
        SessionHelper::setPagamentoNaSession($this->pagamento);
        if (InfraHelper::faltaBibliotecaCurl()) throw new FaltaCurlException();
        $this->validador = new ValidadorPagamento();
        $this->conf = new Configuracoes();
    }

    function processaRequisicao()
    {
        if ($this->validador->isValid($this->pagamento) && !SessionHelper::temMensagemDeErro()) {

            $jsonEnvio = $this->prepareJson($this->pagamento);
            $data_string = json_encode($jsonEnvio);

            $ch = curl_init($this->conf->getUrl());
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->pagamento->getUg()->getToken()));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            if ($result === false) throw new ErroDeConexaoException();

            $result = json_decode($result);

            if ($this->resultadoTemErro($result)) {
                foreach ($result as $erro) {
                    throw new ErroDeRequisicaoException($erro->codigo, $erro->descricao);
                    die();
                }
            } else {
                $sessao = str_replace('https://pagtesouro.tesouro.gov.br/#/pagamento?idSessao=', '', $result->proximaUrl);
                SessionHelper::unsetPagamento();
                SessionHelper::setSessaoPagamentoOK($sessao);
                NavigatorHelper::voltarParaPrincipal();
            }
        }else{
            NavigatorHelper::voltarParaPrincipal();
        }
    }

    private function prepareJson($pagamento)
    {
        return array(
            "codigoServico" => $pagamento->getCodigoServico(),
            "referencia" => $pagamento->getReferencia(),
            "competencia" => str_replace("/", "", $pagamento->getCompetencia()),
            "vencimento" => str_replace("/", "", $pagamento->getVencimento()),
            "cnpjCpf" => $pagamento->getCleanCnpjCpf(),
            "nomeContribuinte" =>  $pagamento->getNomeContribuinte(),
            "valorPrincipal" =>  $this->preparaNumero($pagamento->getValorPrincipal()),
            "valorDescontos" => $this->preparaNumero($pagamento->getValorDescontos()),
            "valorOutrasDeducoes" => $this->preparaNumero($pagamento->getValorOutrasDeducoes()),
            "valorMulta" => $this->preparaNumero($pagamento->getValorMulta()),
            "valorJuros" => $this->preparaNumero($pagamento->getValorJuros()),
            "valorOutrosAcrescimos" => $this->preparaNumero($pagamento->getValorOutrosAcrescimos()),
            "modoNavegacao" => "2",
            "urlNotificacao" => "https://valpagtesouro.tesouro.gov.br/api/simulador/ug/notificacao"
        );
    }

    private function resultadoTemErro($result): bool
    {
        return is_array($result);
    }

    private function preparaNumero($numero)
    {
        return number_format($this->validador->toMoney($numero), 2, '.', '');
    }
}
