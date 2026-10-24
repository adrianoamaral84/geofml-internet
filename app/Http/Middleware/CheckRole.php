<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        if (!$roles || !Auth::user()->hasRole($roles)) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
