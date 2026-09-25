<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class PreventOverlappingHospedePeriod
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$request->isMethod('post')) {
            return $next($request);
        }

        $periodo = $this->extrairPeriodo($request->input('peridoinicial'));

        // O controller continua responsável pelas mensagens de formato/data inválida.
        if (!$periodo) {
            return $next($request);
        }

        [$dataInicio, $dataTermino] = $periodo;

        if ($dataInicio->gte($dataTermino)) {
            return $next($request);
        }

        $pedidoAtualId = $this->pedidoAtualId($request);

        $conflito = \App\Hospede::where('user_id', $user->id)
            ->when($pedidoAtualId, function ($query) use ($pedidoAtualId) {
                $query->where('id', '<>', $pedidoAtualId);
            })
            // Intervalos são tratados como [entrada, saída): a saída não conta como diária.
            ->whereDate('data_inicio', '<', $dataTermino->format('Y-m-d'))
            ->whereDate('data_termino', '>', $dataInicio->format('Y-m-d'))
            ->exists();

        if (!$conflito) {
            return $next($request);
        }

        $erros = [
            'peridoinicial' => 'Você já possui outro pedido com período que se sobrepõe ao período informado.',
        ];

        if ($request->is('hospede/pedido/edita/store')) {
            $pedidoAtualId = $pedidoAtualId ?: session('edicao_pedido_id');

            if ($pedidoAtualId && ctype_digit((string) $pedidoAtualId)) {
                return redirect()->route(
                    'hospede.solicitarinscricao.edit',
                    Crypt::encrypt((int) $pedidoAtualId)
                )->withErrors($erros)->withInput();
            }

            return redirect()->route('hospede.meuspedidos')->withErrors($erros);
        }

        if ($request->is('hospede/pedido/store')) {
            return redirect()->route('hospede.solicitarinscricao')
                ->withErrors($erros)
                ->withInput();
        }

        return redirect()->back()->withErrors($erros)->withInput();
    }

    private function extrairPeriodo($valor)
    {
        if (!is_string($valor) || trim($valor) === '') {
            return null;
        }

        try {
            $partes = array_map('trim', explode(' - ', trim($valor)));

            if (count($partes) !== 2) {
                return null;
            }

            $inicioTexto = $partes[0];
            $terminoTexto = $partes[1];

            $inicio = Carbon::createFromFormat('d-m-Y', $inicioTexto)->startOfDay();
            $termino = Carbon::createFromFormat('d-m-Y', $terminoTexto)->startOfDay();

            if (
                $inicio->format('d-m-Y') !== $inicioTexto ||
                $termino->format('d-m-Y') !== $terminoTexto
            ) {
                return null;
            }

            return [$inicio, $termino];
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function pedidoAtualId($request)
    {
        if (!$request->is('hospede/pedido/edita/*')) {
            return null;
        }

        $id = $request->input('id');

        if ($id && ctype_digit((string) $id)) {
            return (int) $id;
        }

        $idSessao = session('edicao_pedido_id');

        if ($idSessao && ctype_digit((string) $idSessao)) {
            return (int) $idSessao;
        }

        return null;
    }
}
