<?php

namespace PagTesouro\Service;

use DateTimeImmutable;
use PagTesouro\Model\Pagamento;
use Bissolli\ValidadorCpfCnpj\CPF;
use Bissolli\ValidadorCpfCnpj\CNPJ;
use PagTesouro\Helper\SessionHelper;
use PagTesouro\Exception\CpfInvalidoException;
use PagTesouro\Exception\CnpjInvalidoException;
use PagTesouro\Exception\ValorInvalidoException;
use PagTesouro\Exception\CompetenciaInvalidaException;
use PagTesouro\Exception\ValorDeveSerPositivoException;
use PagTesouro\Exception\DataVencimentoInvalidaException;
use PagTesouro\Exception\ValoresTotaisInvalidosException;
use PagTesouro\Exception\NomeContribuinteInvalidoException;


class ValidadorPagamento
{

    private $pagamento;

    public function isValid(Pagamento $pagamento)
    {
        $this->pagamento = $pagamento;
        if ($pagamento->getTipoPessoa() == "PF") {
            $this->validaCPF($pagamento->getCnpjCpf());
        }

        if ($pagamento->getTipoPessoa() == "PJ") {
            $this->validaCNPJ($pagamento->getCnpjCpf());
        }

        $this->validaNomeContribuinte($pagamento->getNomeContribuinte());
        $this->validaCompetencia($pagamento->getCompetencia());
        $this->validaVencimento($pagamento->getVencimento());
        $this->validaValorMaiorQueZero($pagamento->getValorPrincipal(), "ValorPrincipal");
        $this->validaValor($pagamento->getValorDescontos(), "Descontos");
        $this->validaValor($pagamento->getValorOutrasDeducoes(), "OutrasDeducoes");
        $this->validaValor($pagamento->getValorMulta(), "Multa");
        $this->validaValor($pagamento->getValorJuros(), "Juros");
        $this->validaValor($pagamento->getValorOutrosAcrescimos(), "OutrosAcrescimos");
        $this->validaValoresTotais($pagamento);

        return true;
    }

    private function validaCPF($cpfEmTexto)
    {
        $document = new CPF($cpfEmTexto);
        try {
            if (!$document->isValid()) {
                SessionHelper::setPagamentoNaSession($this->pagamento);
                throw new CpfInvalidoException();
            }
        } catch (CpfInvalidoException $e) {
            SessionHelper::setMensagemDeErro($e->getCodigo(), $e->getMensagem());
        }
    }

    private function validaCNPJ($cnpjEmTexto)
    {

        try {
            $document = new CNPJ($cnpjEmTexto);
            if (!$document->isValid()) {
                SessionHelper::setPagamentoNaSession($this->pagamento);
                throw new CnpjInvalidoException();
            }
        } catch (CnpjInvalidoException $e) {
            SessionHelper::setMensagemDeErro($e->getCodigo(), $e->getMensagem());
        }
    }

    private function validaCompetencia($competencia)
    {
        try {
            if (empty($competencia)) {
                return true;
            }
            $competenciaArray = explode("/", $competencia);
            if (floor($competenciaArray[0]) < 1 || floor($competenciaArray[0]) > 12) {
                SessionHelper::setPagamentoNaSession($this->pagamento);
                throw new CompetenciaInvalidaException();
            }
        } catch (CompetenciaInvalidaException $e) {
            SessionHelper::setMensagemDeErro($e->getCodigo(), $e->getMensagem());
        }
    }

    private function validaVencimento($vencimento)
    {
        try {
            if (empty($vencimento)) {
                return true;
            }
            $hoje = new DateTimeImmutable();
            $vencimento = DateTimeImmutable::createFromFormat('d/m/Y', $vencimento);
            if ($vencimento >= $hoje) {
                return true;
            }
            SessionHelper::setPagamentoNaSession($this->pagamento);
            throw new DataVencimentoInvalidaException();
        } catch (DataVencimentoInvalidaException $e) {
            SessionHelper::setMensagemDeErro($e->getCodigo(), $e->getMensagem());
        }
    }


    private function validaValorMaiorQueZero($valor, $nomeCampo)
    {
        try {
            $valor = $this->toMoney($valor);
            if (is_float($valor) && $valor > 0) {
                return true;
            }
            SessionHelper::setPagamentoNaSession($this->pagamento);
            throw new ValorDeveSerPositivoException($nomeCampo);
        } catch (ValorDeveSerPositivoException $e) {
            SessionHelper::setMensagemDeErro($e->getCodigo(), $e->getMensagem());
        }
    }

    private function validaValor($valor, $nomeCampo)
    {
        try {
            $valor = $this->toMoney($valor);
            if (is_float($valor) && $valor >= 0) {
                return true;
            }
            SessionHelper::setPagamentoNaSession($this->pagamento);
            throw new ValorInvalidoException($nomeCampo);
        } catch (ValorInvalidoException $e) {
            SessionHelper::setMensagemDeErro($e->getCodigo(), $e->getMensagem());
        }
    }



    private function validaValoresTotais($pagamento)
    {
        try {
            $valorPagar =
                ($this->toMoney($pagamento->getValorPrincipal())
                    + $this->toMoney($pagamento->getValorJuros())
                    + $this->toMoney($pagamento->getValorMulta())
                    + $this->toMoney($pagamento->getValorOutrosAcrescimos()));
            $valorDescontar =
                ($this->toMoney($pagamento->getValorDescontos())
                    + $this->toMoney($pagamento->getValorOutrasDeducoes()));

            if ($valorDescontar > $valorPagar) {
                SessionHelper::setPagamentoNaSession($this->pagamento);
                throw new ValoresTotaisInvalidosException();
            }

            return true;
        } catch (ValoresTotaisInvalidosException $e) {
            SessionHelper::setMensagemDeErro($e->getCodigo(), $e->getMensagem());
        }
    }

    private function validaNomeContribuinte($nome)
    {
        try {
            if (strlen($nome) >= 2) {
                return true;
            }
            SessionHelper::setPagamentoNaSession($this->pagamento);
            throw new NomeContribuinteInvalidoException();
        } catch (NomeContribuinteInvalidoException $e) {
            SessionHelper::setMensagemDeErro($e->getCodigo(), $e->getMensagem());
        }
    }

    public function toMoney($valorEmString)
    {
        $valorEmString = str_replace(".", "", $valorEmString);
        $valorEmString = str_replace(",", ".", $valorEmString);
        return (float)$valorEmString;
    }
}
