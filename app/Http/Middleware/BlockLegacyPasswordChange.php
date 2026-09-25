<?php

namespace App\Http\Middleware;

use Closure;

class BlockLegacyPasswordChange
{
    /**
     * Bloqueia os endpoints legados de alteração de senha.
     *
     * A redefinição de senha deve ocorrer exclusivamente pelo fluxo oficial
     * do Laravel, que exige um token válido enviado ao e-mail do usuário.
     */
    public function handle($request, Closure $next)
    {
        if ($request->is('change-password') || $request->is('change-passwords')) {
            abort(404);
        }

        return $next($request);
    }
}
