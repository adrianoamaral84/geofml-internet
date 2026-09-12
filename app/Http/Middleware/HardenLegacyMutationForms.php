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

            // A tela legada abre a criação de pagamento por window.open(GET).
            // Mantemos o mesmo comportamento visual, mas o envio passa a ser
            // POST com CSRF para que uma simples URL não crie pagamentos.
            $content = str_replace(
                'janelaPagamentoInicial = window.open(',
                'janelaPagamentoInicial = abrirPagamentoSeguro(',
                $content
            );

            $content = preg_replace(
                '/window\.open\("([^"]*processaPagamentoRestante[^"]*)",\s*"_blank",\s*"([^"]*)"\);/',
                'abrirPagamentoSeguro("$1", "pagamentoRestantePagTesouro", "$2");',
                $content
            );

            $csrfToken = json_encode(csrf_token());

            $paymentHelper = <<<HTML
<script>
function abrirPagamentoSeguro(url, nomeJanela, recursos) {
    var janela = window.open('', nomeJanela, recursos);

    if (!janela) {
        alert('O navegador bloqueou a janela. Permita pop-ups e tente novamente.');
        return null;
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.target = nomeJanela;
    form.style.display = 'none';

    var csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = {$csrfToken};

    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

    janela.focus();
    return janela;
}
</script>
HTML;

            if (strpos($content, '</body>') !== false) {
                $content = str_replace(
                    '</body>',
                    $paymentHelper . PHP_EOL . '</body>',
                    $content
                );
            } else {
                $content .= $paymentHelper;
            }
        }

        $response->setContent($content);

        return $response;
    }
}
