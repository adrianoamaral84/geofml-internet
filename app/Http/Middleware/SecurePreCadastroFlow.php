<?php

namespace App\Http\Middleware;

use App\Http\Controllers\PreCadastroController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurePreCadastroFlow
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') && $request->is('user/create/new')) {
            return app(PreCadastroController::class)->store($request);
        }

        $response = $next($request);

        if (
            $request->isMethod('get') &&
            $request->is('precadastro') &&
            $response instanceof Response &&
            method_exists($response, 'getContent') &&
            method_exists($response, 'setContent')
        ) {
            $content = $response->getContent();

            if (is_string($content)) {
                $content = str_replace(
                    'use de 8 a 15 caracteres, com pelo menos 1 letra maiúscula, 1 letra minúscula, 1 número e 1 símbolo.',
                    'use de 8 a 64 caracteres, com pelo menos 1 letra maiúscula, 1 letra minúscula, 1 número e 1 símbolo.',
                    $content
                );

                $content = str_replace(
                    'name="password" id="password" required maxlength="15" minlength="8"',
                    'name="password" id="password" required maxlength="64" minlength="8"',
                    $content
                );

                $content = str_replace(
                    'name="resenha" id="resenha" maxlength="15" minlength="8"',
                    'name="resenha" id="resenha" maxlength="64" minlength="8"',
                    $content
                );

                $response->setContent($content);
            }
        }

        return $response;
    }
}
