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

        if (!$request->routeIs('hospede.meuspedidos') && !$request->routeIs('hospede.meupedido')) {
            return $response;
        }

        $content = $response->getContent();

        if (!is_string($content)) {
            return $response;
        }

        if ($request->routeIs('hospede.meuspedidos')) {
            $content = str_replace(
                '<form action="" id="deletearea" method="get">',
                '<form action="" id="deletearea" method="post">',
                $content
            );
        }

        if ($request->routeIs('hospede.meupedido')) {
            $content = str_replace(
                '<form action="" id="cancelar" method="get">',
                '<form action="" id="cancelar" method="post">',
                $content
            );

            $content = str_replace(
                '<form action="" id="checkin" method="get">',
                '<form action="" id="checkin" method="post">',
                $content
            );

            $content = str_replace(
                '<form action="" id="checkout" method="get">',
                '<form action="" id="checkout" method="post">',
                $content
            );
        }

        $response->setContent($content);

        return $response;
    }
}
