<?php

namespace App\Http\Middleware;

use Closure;

class FinalizePasswordSecurityMessages
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->hasSession() && $request->session()->has('_password_security_notice')) {
            $notice = $request->session()->pull('_password_security_notice');
            $request->session()->flash('message', $notice);
        }

        return $response;
    }
}
