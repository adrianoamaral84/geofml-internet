<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class HardenLegacyMutationForms
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!$response instanceof Response) {
            return $response;
        }

        if (!$request->routeIs('hospede.meuspedidos')) {
            return $response;
        }

        $content = $response->getContent();

        if (!is_string($content)) {
            return $response;
        }

        $content = str_replace(
            '<form action="" id="deletearea" method="get">',
            '<form action="" id="deletearea" method="post">',
            $content
        );

        $response->setContent($content);

        return $response;
    }
}
