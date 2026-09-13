<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;

class FixLegacyEditStoreRedirect
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!$response instanceof RedirectResponse) {
            return $response;
        }

        $target = $response->getTargetUrl();
        $path = parse_url($target, PHP_URL_PATH);

        if ($path !== '/hospede/pedido/edita/confirmar') {
            return $response;
        }

        $pedidoId = $request->input('id');

        if (!ctype_digit((string) $pedidoId)) {
            return $response;
        }

        $pedido = \App\Hospede::where('id', (int) $pedidoId)
            ->where('user_id', optional($request->user())->id)
            ->where('status', 0)
            ->first();

        if (!$pedido) {
            return $response;
        }

        // O controller legado já gravou erros/old input na sessão. Mantemos
        // esses flashes por mais uma requisição e redirecionamos para uma rota
        // GET real, em vez de deixar o navegador acessar a confirmação POST-only.
        $request->session()->reflash();

        return redirect()
            ->route('hospede.solicitarinscricao.edit', Crypt::encrypt($pedido->id))
            ->withInput($request->except('_token'));
    }
}
