<?php

namespace App\Http\Middleware;

use Closure;
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
            $this->assertHospedagemOwnership($request->input('hospedagem_id'), $user->id);
        }

        if ($request->is('processaRequisicao/*')) {
            $this->assertHospede($user);
            $this->assertHospedagemOwnership($this->encryptedSegment($request, 1), $user->id);
        }

        if ($request->is('processaPagamentoRestante/*/valor/*')) {
            $this->assertHospede($user);
            $this->assertHospedagemOwnership($this->encryptedSegment($request, 1), $user->id);
        }

        if ($request->is('pagamento/inicial/*/status')) {
            $this->assertHospede($user);
            $this->assertHospedagemOwnership($this->encryptedSegment($request, 2), $user->id);
        }

        return $next($request);
    }

    private function assertHospede($user)
    {
        if (!$user->hasRole('hospede')) {
            abort(403, 'Esta operação é exclusiva do hóspede.');
        }
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

    private function assertHospedagemOwnership($hospedagemId, $userId)
    {
        if (!is_numeric($hospedagemId)) {
            abort(404);
        }

        $pertenceAoUsuario = \App\Hospede::where('id', (int) $hospedagemId)
            ->where('user_id', $userId)
            ->exists();

        if (!$pertenceAoUsuario) {
            abort(403, 'Você não tem autorização para acessar esta hospedagem.');
        }
    }
}
