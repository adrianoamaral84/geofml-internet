<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityRoutesTest extends TestCase
{
    public function test_payment_creation_routes_are_post_only_and_use_secure_controller()
    {
        $routes = collect(app('router')->getRoutes());

        $inicial = $routes->first(function ($route) {
            return $route->uri() === 'processaRequisicao/{id}'
                && in_array('POST', $route->methods(), true);
        });

        $restante = $routes->first(function ($route) {
            return $route->uri() === 'processaPagamentoRestante/{id}/valor/{restante}'
                && in_array('POST', $route->methods(), true);
        });

        $this->assertNotNull($inicial);
        $this->assertNotNull($restante);
        $this->assertSame(
            'App\\Http\\Controllers\\Security\\SecurePagamentoController@processaRequisicao',
            $inicial->getActionName()
        );
        $this->assertSame(
            'App\\Http\\Controllers\\Security\\SecurePagamentoController@processaPagamentoRestante',
            $restante->getActionName()
        );

        $this->assertFalse($routes->contains(function ($route) {
            return $route->uri() === 'processaRequisicao/{id}'
                && in_array('GET', $route->methods(), true);
        }));

        $this->assertFalse($routes->contains(function ($route) {
            return $route->uri() === 'processaPagamentoRestante/{id}/valor/{restante}'
                && in_array('GET', $route->methods(), true);
        }));
    }

    public function test_precadastro_post_uses_secure_controller()
    {
        $route = collect(app('router')->getRoutes())->first(function ($route) {
            return $route->uri() === 'user/create/new'
                && in_array('POST', $route->methods(), true);
        });

        $this->assertNotNull($route);
        $this->assertSame(
            'App\\Http\\Controllers\\Security\\SecurePreCadastroController@store',
            $route->getActionName()
        );
        $this->assertContains('auth', $route->gatherMiddleware());
        $this->assertContains('role:precadastro', $route->gatherMiddleware());
    }

    public function test_public_access_request_uses_secure_controller_and_throttle()
    {
        $route = collect(app('router')->getRoutes())->first(function ($route) {
            return $route->uri() === 'pedido'
                && in_array('POST', $route->methods(), true);
        });

        $this->assertNotNull($route);
        $this->assertSame(
            'App\\Http\\Controllers\\Security\\SecureAccessRequestController@store',
            $route->getActionName()
        );
        $this->assertContains('throttle:5,10', $route->gatherMiddleware());
    }

    public function test_document_routes_are_authenticated_and_use_secure_reader()
    {
        $routes = collect(app('router')->getRoutes());

        foreach ([
            'usuarios/{user}/documentos/{tipo}',
            'usuarios/ver/documento/{id}/{doc}/{tipo}/arquivo',
        ] as $uri) {
            $route = $routes->first(function ($route) use ($uri) {
                return $route->uri() === $uri && in_array('GET', $route->methods(), true);
            });

            $this->assertNotNull($route, 'Rota não encontrada: ' . $uri);
            $this->assertStringContainsString(
                'App\\Http\\Controllers\\UserDocumento\\UserDocumentoController@',
                $route->getActionName()
            );
            $this->assertContains('auth', $route->gatherMiddleware());
        }
    }

    public function test_legacy_admin_and_debug_routes_are_not_registered()
    {
        $uris = collect(app('router')->getRoutes())->map(function ($route) {
            return $route->uri();
        });

        foreach ([
            'mailable',
            'mail',
            'envios',
            'geraboleto',
            'consultapagamento',
            'admin/pagamento/create',
            'api/user',
            'laratrust',
        ] as $uri) {
            $this->assertFalse($uris->contains($uri), 'Rota legada registrada: ' . $uri);
        }
    }
}
