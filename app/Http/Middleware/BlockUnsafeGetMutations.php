<?php

namespace App\Http\Middleware;

use Closure;

class BlockUnsafeGetMutations
{
    public function handle($request, Closure $next)
    {
        if (!$request->isMethod('get')) {
            return $next($request);
        }

        $path = '/' . ltrim($request->path(), '/');

        $patterns = [
            '#^/hospede/delete/meupedido/[^/]+$#',
            '#^/hospede/cancelar/hospedagem/[^/]+$#',
            '#^/hospede/checkin/[^/]+$#',
            '#^/hospede/checkout/[^/]+/[^/]+$#',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $path)) {
                abort(405, 'Método não permitido para esta operação.');
            }
        }

        return $next($request);
    }
}
