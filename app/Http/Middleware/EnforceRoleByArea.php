<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceRoleByArea
{
    /**
     * Restringe areas funcionais aos perfis correspondentes.
     */
    public function handle(Request $request, Closure $next)
    {
        $role = $this->requiredRole($request);

        if (!$role) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user) {
            return redirect()->guest(route('login'));
        }

        if (!$user->hasRole($role)) {
            abort(403, 'Você não tem autorização para acessar esta área.');
        }

        return $next($request);
    }

    private function requiredRole(Request $request)
    {
        if ($request->is('hospede') || $request->is('hospede/*')) {
            return 'hospede';
        }

        if ($request->is('atendente') || $request->is('atendente/*')) {
            return 'atendente';
        }

        if ($request->is('precadastro') || $request->is('precadastro/*')) {
            return 'precadastro';
        }

        // O envio que conclui o pre-cadastro fica fora do prefixo /precadastro.
        if ($request->is('user/create/new') && $request->isMethod('post')) {
            return 'precadastro';
        }

        return null;
    }
}
