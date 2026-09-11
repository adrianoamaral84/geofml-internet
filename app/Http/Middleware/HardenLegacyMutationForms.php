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

        if (
            !$request->routeIs('hospede.meuspedidos') &&
            !$request->routeIs('hospede.meupedido') &&
            !$request->routeIs('usuario.verdados')
        ) {
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

        if ($request->routeIs('usuario.verdados')) {
            $token = e(csrf_token());

            $content = preg_replace_callback(
                '#<a\s+href="([^"]*/users/[^"/]+/reset)"\s+class="btn btn-dark">(.*?)Resetar Senha!\s*</a>#s',
                function ($matches) use ($token) {
                    $action = e($matches[1]);

                    return '<form action="' . $action . '" method="post" style="display:inline-block;">'
                        . '<input type="hidden" name="_token" value="' . $token . '">'
                        . '<button type="submit" class="btn btn-dark" onclick="return confirm(\'Enviar link seguro de redefinição de senha para o e-mail deste usuário?\');">'
                        . '<i class="fas fa-check-circle fa-sm"></i> Resetar Senha!'
                        . '</button></form>';
                },
                $content
            );
        }

        $response->setContent($content);

        return $response;
    }
}
