<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class EnforceHospedeOwnership
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($request->is('hospede/uploadrecibo') && $request->isMethod('post')) {
            $this->assertHospedagemOwnership($request->input('hospedagem_id'), $user->id, false);
        }

        if ($request->is('processaRequisicao/*')) {
            $this->assertHospedagemOwnership($this->encryptedSegment($request, 1), $user->id, true);
        }

        if ($request->is('processaPagamentoRestante/*/valor/*')) {
            $this->assertHospedagemOwnership($this->encryptedSegment($request, 1), $user->id, true);
        }

        if ($request->is('pagamento/inicial/*/status')) {
            $this->assertHospedagemOwnership($this->encryptedSegment($request, 2), $user->id, true);
        }

        return $next($request);
    }

    private function encryptedSegment($request, $index)
    {
        $segments = $request->segments();

        if (!isset($segments[$index])) {
            abort(404);
        }

        try {
            return Crypt::decrypt($segments[$index]);
        } catch (\Throwable $e) {
            abort(404);
        }
    }

    private function assertHospedagemOwnership($hospedagemId, $userId, $encrypted = false)
    {
        if ($encrypted) {
            $id = $hospedagemId;
        } else {
            if (!is_numeric($hospedagemId)) {
                abort(404);
            }

            $id = (int) $hospedagemId;
        }

        $pertenceAoUsuario = \App\Hospede::where('id', $id)
            ->where('user_id', $userId)
            ->exists();

        if (!$pertenceAoUsuario) {
            abort(403, 'Você não tem autorização para acessar esta hospedagem.');
        }
    }
}
