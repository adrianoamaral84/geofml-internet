<?php

namespace App\Http\Middleware;

use Closure;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        //dd($permission);
        $permission = explode('|', $permission);

        if(checkPermission($permission)){
            return $next($request);
        }

        $menuAtivo = "dashboard";
        return response()->view('home', compact('menuAtivo'));
    }
}
