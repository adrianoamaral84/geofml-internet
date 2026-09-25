<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EnforceRoleByArea
{
    /**
     * Restringe areas funcionais aos perfis correspondentes.
     */
    public function handle(Request $request, Closure $next)
    {
        $roles = $this->requiredRoles($request);

        if (!$roles) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user) {
            return redirect()->guest(route('login'));
        }

        if (!$this->hasAnyRole($user, $roles)) {
            abort(403, 'Você não tem autorização para acessar esta área.');
        }

        // O detalhe do pedido é compartilhado com a equipe operacional.
        // Para hóspedes, porém, o pedido precisa pertencer ao próprio usuário.
        if ($request->is('hospede/meupedido/*') && $user->hasRole('hospede')) {
            $staffRoles = [
                'atendente',
                'administrador_geral',
                'auxiliar_administrador_geral',
            ];

            if (!$this->hasAnyRole($user, $staffRoles)) {
                try {
                    $pedidoId = Crypt::decrypt($request->route('id'));
                } catch (\Throwable $e) {
                    abort(404);
                }

                $pertenceAoUsuario = \App\Hospede::where('id', $pedidoId)
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$pertenceAoUsuario) {
                    abort(403, 'Você não tem autorização para acessar este pedido.');
                }
            }
        }

        return $next($request);
    }

    private function requiredRoles(Request $request)
    {
        if ($request->is('hospede/meupedido/*')) {
            return [
                'hospede',
                'atendente',
                'administrador_geral',
                'auxiliar_administrador_geral',
            ];
        }

        if (
            $request->is('atendente/hospedagem/*/checkin') ||
            $request->is('atendente/hospedagem/*/checkout')
        ) {
            return [
                'atendente',
                'administrador_geral',
                'auxiliar_administrador_geral',
            ];
        }

        if ($request->is('hospede') || $request->is('hospede/*')) {
            return ['hospede'];
        }

        if ($request->is('atendente') || $request->is('atendente/*')) {
            return ['atendente'];
        }

        if ($request->is('precadastro') || $request->is('precadastro/*')) {
            return ['precadastro'];
        }

        // O envio que conclui o pre-cadastro fica fora do prefixo /precadastro.
        if ($request->is('user/create/new') && $request->isMethod('post')) {
            return ['precadastro'];
        }

        return [];
    }

    private function hasAnyRole($user, array $roles)
    {
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        return false;
    }
}
