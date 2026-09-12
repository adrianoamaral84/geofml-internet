<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Services\CalculoHospedagemService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SecurePagamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function processaRequisicao($id)
    {
        try {
            $hospedagemId = Crypt::decrypt($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        $hospedagem = \App\Hospede::findOrFail($hospedagemId);
        $this->assertOwnership($hospedagem);

        $pagamentoAtual = \App\Pagamento::where('hospedagem_id', $hospedagem->id)
            ->where('tipo', 'diaria_inicial')
            ->orderByDesc('id')
            ->first();

        if ($pagamentoAtual && in_array($pagamentoAtual->situacao, ['CRIADO', 'INICIADO'], true)) {
            $pagamentoAtual->situacao = 'SUBSTITUIDO';
            $pagamentoAtual->save();
        }

        if ($pagamentoAtual && in_array(
            $pagamentoAtual->situacao,
            ['CONCLUIDO', 'PAGO', 'PAGAMENTO_CONCLUIDO'],
            true
        )) {
            return response('O pagamento inicial já foi confirmado. Esta janela pode ser fechada.', 200);
        }

        $valorDiaria = round((float) $hospedagem->valorTarifaComDesconto(), 2);

        if ($valorDiaria <= 0) {
            return redirect()->back()->withErrors([
                'pagamento' => 'Não há valor válido para gerar o pagamento inicial.',
            ]);
        }

        if (config('services.pagtesouro.modo_teste')) {
            return $this->criarPagamentoTeste($hospedagem, 'diaria_inicial', $valorDiaria);
        }

        $pagtesouro = $this->pagTesouroConfig();
        $payload = $this->payloadPagamentoInicial($pagtesouro, $hospedagem, $valorDiaria);
        $resultado = $this->enviarPagTesouro($pagtesouro, $payload);

        if (!$resultado) {
            return redirect()->back();
        }

        $pagamento = new \App\Pagamento();
        $pagamento->idPagamento = $resultado->idPagamento;
        $pagamento->proximaUrl = $resultado->proximaUrl;
        $pagamento->hospedagem_id = $hospedagem->id;
        $pagamento->tipo = 'diaria_inicial';
        $pagamento->situacao = $resultado->situacao->codigo ?? 'CRIADO';
        $pagamento->valor = $valorDiaria;
        $pagamento->save();

        return redirect()->to($resultado->proximaUrl);
    }

    public function processaPagamentoRestante($id, $restante = null)
    {
        try {
            $hospedagemId = Crypt::decrypt($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        $hospedagem = \App\Hospede::findOrFail($hospedagemId);
        $this->assertOwnership($hospedagem);

        if (empty($hospedagem->checkin_at)) {
            return redirect()->back()->withErrors([
                'pagamento' => 'O pagamento restante só pode ser calculado após o check-in.',
            ]);
        }

        $pagamentoAtual = \App\Pagamento::where('hospedagem_id', $hospedagem->id)
            ->where('tipo', 'pagamento_restante')
            ->orderByDesc('id')
            ->first();

        if ($pagamentoAtual && in_array($pagamentoAtual->situacao, ['CRIADO', 'INICIADO'], true)) {
            \Session::flash('message', [
                'msg' => 'Já existe um pagamento restante em aberto. Consulte a situação antes de gerar outro.',
                'class' => 'warning',
            ]);

            return redirect()->back();
        }

        if ($pagamentoAtual && in_array(
            $pagamentoAtual->situacao,
            ['CONCLUIDO', 'PAGO', 'PAGAMENTO_CONCLUIDO'],
            true
        )) {
            \Session::flash('message', [
                'msg' => 'Pagamento restante já foi confirmado.',
                'class' => 'success',
            ]);

            return redirect()->back();
        }

        // O valor recebido na URL é ignorado. O servidor recalcula sempre.
        $calculo = (new CalculoHospedagemService())->calcular($hospedagem);
        $valorRestante = round((float) $calculo['valor_restante'], 2);

        $hospedagem->valor_restante = $valorRestante;
        $hospedagem->qntdiarias = $calculo['dias'];
        $hospedagem->valor = $calculo['valor_total'];
        $hospedagem->save();

        if ($valorRestante <= 0) {
            \Session::flash('message', [
                'msg' => 'Não há valor restante para pagamento.',
                'class' => 'info',
            ]);

            return redirect()->back();
        }

        if (config('services.pagtesouro.modo_teste')) {
            return $this->criarPagamentoTeste($hospedagem, 'pagamento_restante', $valorRestante);
        }

        $pagtesouro = $this->pagTesouroConfig();
        $payload = $this->payloadPagamentoRestante($pagtesouro, $hospedagem, $valorRestante);
        $resultado = $this->enviarPagTesouro($pagtesouro, $payload);

        if (!$resultado) {
            return redirect()->back();
        }

        $pagamento = new \App\Pagamento();
        $pagamento->idPagamento = $resultado->idPagamento;
        $pagamento->proximaUrl = $resultado->proximaUrl;
        $pagamento->hospedagem_id = $hospedagem->id;
        $pagamento->tipo = 'pagamento_restante';
        $pagamento->situacao = $resultado->situacao->codigo ?? 'CRIADO';
        $pagamento->valor = $valorRestante;
        $pagamento->save();

        return redirect()->to($resultado->proximaUrl);
    }

    public function consultarStatusPagamentoInicial($id)
    {
        try {
            try {
                $hospedagemId = Crypt::decrypt($id);
            } catch (\Throwable $e) {
                abort(404);
            }

            $hospedagem = \App\Hospede::findOrFail($hospedagemId);
            $this->assertOwnership($hospedagem);

            $pagamento = \App\Pagamento::where('hospedagem_id', $hospedagem->id)
                ->where('tipo', 'diaria_inicial')
                ->where('situacao', '<>', 'SUBSTITUIDO')
                ->orderByDesc('id')
                ->firstOrFail();

            $situacoesConfirmadas = [
                'CONCLUIDO',
                'PAGO',
                'PAGAMENTO_CONCLUIDO',
            ];

            if (config('services.pagtesouro.modo_teste')) {
                $hospedagem->refresh();
                $pagamento->refresh();

                $confirmado = in_array($pagamento->situacao, $situacoesConfirmadas, true);

                return response()->json([
                    'sucesso' => true,
                    'pagamento_confirmado' => $confirmado,
                    'situacao' => $pagamento->situacao,
                    'valor_pago' => (float) ($hospedagem->valor_pago ?? 0),
                    'valor_restante' => (float) ($hospedagem->valor_restante ?? 0),
                    'mensagem' => $confirmado
                        ? 'Pagamento inicial de teste confirmado.'
                        : 'Pagamento de teste aguardando aprovação.',
                ]);
            }

            if (in_array($pagamento->situacao, $situacoesConfirmadas, true)) {
                return response()->json([
                    'sucesso' => true,
                    'pagamento_confirmado' => true,
                    'situacao' => $pagamento->situacao,
                    'valor_pago' => (float) ($hospedagem->valor_pago ?? 0),
                    'valor_restante' => (float) ($hospedagem->valor_restante ?? 0),
                    'mensagem' => 'Pagamento inicial já confirmado.',
                ]);
            }

            $dados = $this->consultarPagTesouro($pagamento->idPagamento);
            $situacaoPagTesouro = $dados->situacao->codigo ?? null;
            $pagamentoConfirmado = in_array($situacaoPagTesouro, $situacoesConfirmadas, true);

            if (!$pagamentoConfirmado) {
                if (!empty($situacaoPagTesouro)) {
                    $pagamento->situacao = $situacaoPagTesouro;
                    $pagamento->save();
                }

                return response()->json([
                    'sucesso' => true,
                    'pagamento_confirmado' => false,
                    'situacao' => $situacaoPagTesouro,
                    'mensagem' => 'Pagamento ainda não confirmado.',
                ]);
            }

            DB::transaction(function () use (
                $pagamento,
                $hospedagem,
                $dados,
                $situacaoPagTesouro,
                $situacoesConfirmadas
            ) {
                $pagamentoBanco = \App\Pagamento::where('id', $pagamento->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $hospedagemBanco = \App\Hospede::where('id', $hospedagem->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $jaContabilizado = in_array(
                    $pagamentoBanco->situacao,
                    $situacoesConfirmadas,
                    true
                );

                $pagamentoBanco->situacao = $situacaoPagTesouro;

                if (isset($dados->valor)) {
                    $pagamentoBanco->valor = round((float) $dados->valor, 2);
                }

                $pagamentoBanco->save();

                if (!$jaContabilizado) {
                    $valorDiaria = round(
                        (float) ($pagamentoBanco->valor ?? $hospedagemBanco->valorTarifaComDesconto()),
                        2
                    );
                    $valorTotal = round((float) ($hospedagemBanco->valor ?? 0), 2);
                    $valorPagoAtual = round((float) ($hospedagemBanco->valor_pago ?? 0), 2);
                    $novoValorPago = min($valorTotal, round($valorPagoAtual + $valorDiaria, 2));
                    $novoValorRestante = max(0, round($valorTotal - $novoValorPago, 2));

                    $hospedagemBanco->status = 5;
                    $hospedagemBanco->valor_pago = $novoValorPago;
                    $hospedagemBanco->valor_restante = $novoValorRestante;
                    $hospedagemBanco->save();
                }
            });

            $hospedagem->refresh();

            return response()->json([
                'sucesso' => true,
                'pagamento_confirmado' => true,
                'situacao' => $situacaoPagTesouro,
                'valor_pago' => (float) ($hospedagem->valor_pago ?? 0),
                'valor_restante' => (float) ($hospedagem->valor_restante ?? 0),
                'mensagem' => 'Pagamento da diária inicial confirmado e registrado.',
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Erro ao consultar pagamento da diária inicial.', [
                'erro' => $exception->getMessage(),
            ]);

            return response()->json([
                'sucesso' => false,
                'pagamento_confirmado' => false,
                'mensagem' => 'Não foi possível consultar o pagamento inicial.',
            ], 500);
        }
    }

    public function simulador($id)
    {
        abort_unless(config('services.pagtesouro.modo_teste'), 404);

        try {
            $pagamentoId = Crypt::decrypt($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        $pagamento = \App\Pagamento::findOrFail($pagamentoId);
        $hospedagem = \App\Hospede::findOrFail($pagamento->hospedagem_id);
        $this->assertOwnership($hospedagem);

        return view('pagamento.simulador', compact('pagamento', 'hospedagem'));
    }

    public function aprovarSimulacao($id)
    {
        abort_unless(config('services.pagtesouro.modo_teste'), 404);

        try {
            $pagamentoId = Crypt::decrypt($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        DB::transaction(function () use ($pagamentoId) {
            $pagamento = \App\Pagamento::where('id', $pagamentoId)
                ->lockForUpdate()
                ->firstOrFail();

            $hospedagem = \App\Hospede::where('id', $pagamento->hospedagem_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertOwnership($hospedagem);

            if (in_array(
                $pagamento->situacao,
                ['PAGO', 'CONCLUIDO', 'PAGAMENTO_CONCLUIDO'],
                true
            )) {
                return;
            }

            $valorPagamento = round((float) ($pagamento->valor ?? 0), 2);
            $valorTotal = round((float) ($hospedagem->valor ?? 0), 2);
            $valorPagoAtual = round((float) ($hospedagem->valor_pago ?? 0), 2);
            $novoValorPago = min($valorTotal, round($valorPagoAtual + $valorPagamento, 2));
            $novoValorRestante = max(0, round($valorTotal - $novoValorPago, 2));

            $pagamento->situacao = 'PAGO';
            $pagamento->save();

            $hospedagem->valor_pago = $novoValorPago;
            $hospedagem->valor_restante = $novoValorRestante;

            if ($pagamento->tipo === 'diaria_inicial') {
                $hospedagem->status = 5;
            }

            if ($novoValorRestante <= 0) {
                $hospedagem->situacao_pgto_id = 1;
            }

            $hospedagem->save();
        });

        return view('pagamento.simulador_resultado', [
            'titulo' => 'Pagamento aprovado',
            'mensagem' => 'Pagamento de teste confirmado. Esta janela pode ser fechada.',
            'sucesso' => true,
        ]);
    }

    public function cancelarSimulacao($id)
    {
        abort_unless(config('services.pagtesouro.modo_teste'), 404);

        try {
            $pagamentoId = Crypt::decrypt($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        DB::transaction(function () use ($pagamentoId) {
            $pagamento = \App\Pagamento::where('id', $pagamentoId)
                ->lockForUpdate()
                ->firstOrFail();

            $hospedagem = \App\Hospede::where('id', $pagamento->hospedagem_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertOwnership($hospedagem);

            if (!in_array(
                $pagamento->situacao,
                ['PAGO', 'CONCLUIDO', 'PAGAMENTO_CONCLUIDO'],
                true
            )) {
                $pagamento->situacao = 'CANCELADO';
                $pagamento->save();
            }
        });

        return view('pagamento.simulador_resultado', [
            'titulo' => 'Pagamento cancelado',
            'mensagem' => 'O pagamento de teste foi cancelado. Esta janela pode ser fechada.',
            'sucesso' => false,
        ]);
    }

    private function assertOwnership($hospedagem)
    {
        if ((int) Auth::id() !== (int) $hospedagem->user_id) {
            Log::warning('Tentativa de pagamento em hospedagem de outro usuário.', [
                'usuario_id' => Auth::id(),
                'hospedagem_id' => $hospedagem->id,
            ]);

            abort(403);
        }
    }

    private function pagTesouroConfig()
    {
        $pagtesouro = \App\PagTesouro::findOrFail(
            config('services.pagtesouro.config_id', 2)
        );

        if (!filter_var($pagtesouro->url, FILTER_VALIDATE_URL)) {
            abort(500, 'URL do PagTesouro inválida.');
        }

        $scheme = parse_url($pagtesouro->url, PHP_URL_SCHEME);
        if (strtolower((string) $scheme) !== 'https') {
            abort(500, 'A integração com o PagTesouro exige HTTPS.');
        }

        if (empty($pagtesouro->token)) {
            Log::error('Token do PagTesouro não configurado no ambiente.');
            abort(500, 'Configuração do PagTesouro incompleta.');
        }

        return $pagtesouro;
    }

    private function enviarPagTesouro($pagtesouro, array $payload)
    {
        $dataString = json_encode($payload);

        if ($dataString === false) {
            \Session::flash('message', [
                'msg' => 'Não foi possível preparar os dados do pagamento.',
                'class' => 'danger',
            ]);
            return null;
        }

        $ch = curl_init($pagtesouro->url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $pagtesouro->token,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $this->aplicarTlsSeguro($ch);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($result === false) {
            $erro = curl_error($ch);
            curl_close($ch);

            Log::error('Erro cURL ao enviar pagamento ao PagTesouro.', [
                'erro' => $erro,
            ]);

            \Session::flash('message', [
                'msg' => 'Houve um erro de comunicação com o PagTesouro.',
                'class' => 'danger',
            ]);

            return null;
        }

        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            Log::warning('PagTesouro retornou HTTP inesperado.', [
                'http_code' => $httpCode,
            ]);

            \Session::flash('message', [
                'msg' => 'O PagTesouro não aceitou a solicitação de pagamento.',
                'class' => 'danger',
            ]);

            return null;
        }

        $dados = json_decode($result);

        if (json_last_error() !== JSON_ERROR_NONE || !is_object($dados)) {
            \Session::flash('message', [
                'msg' => 'O PagTesouro retornou uma resposta inválida.',
                'class' => 'danger',
            ]);
            return null;
        }

        if (!empty($dados->codigo)) {
            \Session::flash('message', [
                'msg' => $dados->descricao ?? 'O PagTesouro recusou a solicitação.',
                'class' => 'danger',
            ]);
            return null;
        }

        if (empty($dados->idPagamento) || empty($dados->proximaUrl)) {
            \Session::flash('message', [
                'msg' => 'Resposta do PagTesouro sem os dados necessários para continuar.',
                'class' => 'danger',
            ]);
            return null;
        }

        if (!$this->isHttpsUrl($dados->proximaUrl)) {
            Log::warning('PagTesouro retornou URL de redirecionamento inválida.', [
                'host' => parse_url((string) $dados->proximaUrl, PHP_URL_HOST),
            ]);

            \Session::flash('message', [
                'msg' => 'O PagTesouro retornou uma URL de pagamento inválida.',
                'class' => 'danger',
            ]);
            return null;
        }

        return $dados;
    }

    private function consultarPagTesouro($idPagamento)
    {
        $pagtesouro = $this->pagTesouroConfig();
        $url = 'https://pagtesouro.tesouro.gov.br/api/gru/pagamentos/' . rawurlencode($idPagamento);
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . $pagtesouro->token,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $this->aplicarTlsSeguro($ch);

        $resultado = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($resultado === false) {
            $erro = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('Erro de comunicação com o PagTesouro: ' . $erro);
        }

        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException('PagTesouro retornou HTTP inesperado.');
        }

        $dados = json_decode($resultado);
        if (json_last_error() !== JSON_ERROR_NONE || !is_object($dados)) {
            throw new \RuntimeException('Resposta inválida do PagTesouro.');
        }

        return $dados;
    }

    private function aplicarTlsSeguro($ch)
    {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $proxy = config('services.pagtesouro.proxy');
        if (!empty($proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        $caBundle = config('services.pagtesouro.ca_bundle');
        if (!empty($caBundle)) {
            if (!is_readable($caBundle)) {
                throw new \RuntimeException('CA bundle do PagTesouro não está acessível.');
            }

            curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
        }
    }

    private function isHttpsUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https';
    }

    private function payloadPagamentoInicial($pagtesouro, $hospedagem, $valorDiaria)
    {
        return [
            'codigoServico' => $pagtesouro->codservico,
            'referencia' => '',
            'competencia' => '',
            'vencimento' => now()->format('dmY'),
            'cnpjCpf' => $hospedagem->user_cpf,
            'nomeContribuinte' => $hospedagem->user->name,
            'valorPrincipal' => number_format($valorDiaria, 2, '.', ''),
            'valorDescontos' => '',
            'valorOutrasDeducoes' => '',
            'valorMulta' => '',
            'valorJuros' => '',
            'valorOutrosAcrescimos' => '',
            'modoNavegacao' => '2',
            'urlNotificacao' => 'https://valpagtesouro.tesouro.gov.br/api/simulador/ug/notificacao',
        ];
    }

    private function payloadPagamentoRestante($pagtesouro, $hospedagem, $valorRestante)
    {
        return [
            'codigoServico' => $pagtesouro->codservico,
            'referencia' => '',
            'competencia' => '',
            'vencimento' => now()->format('dmY'),
            'cnpjCpf' => $hospedagem->user_cpf,
            'nomeContribuinte' => $hospedagem->user->name,
            'valorPrincipal' => number_format($valorRestante, 2, '.', ''),
            'valorDescontos' => '',
            'valorOutrasDeducoes' => '',
            'valorMulta' => '',
            'valorJuros' => '',
            'valorOutrosAcrescimos' => '',
            'modoNavegacao' => '2',
            'urlNotificacao' => 'https://valpagtesouro.tesouro.gov.br/api/simulador/ug/notificacao',
        ];
    }

    private function criarPagamentoTeste($hospedagem, $tipo, $valor)
    {
        $pagamento = new \App\Pagamento();
        $pagamento->idPagamento = 'TESTE-' . strtoupper($tipo) . '-' . strtoupper(uniqid());
        $pagamento->proximaUrl = 'teste';
        $pagamento->hospedagem_id = $hospedagem->id;
        $pagamento->tipo = $tipo;
        $pagamento->situacao = 'CRIADO';
        $pagamento->valor = round((float) $valor, 2);
        $pagamento->save();

        return redirect()->route('pagamento.simulador', [
            'id' => Crypt::encrypt($pagamento->id),
        ]);
    }
}
