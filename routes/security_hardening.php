<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GeoFML Internet - rotas de hardening operacionais
|--------------------------------------------------------------------------
|
| O routes/web.php legado não é mais carregado. Este arquivo mantém somente
| substituições/ações seguras que fazem parte do fluxo operacional atual.
|
*/

Route::middleware(['auth', 'role:hospede'])
    ->delete('/hospede/delete/meupedido/{id}', 'Security\\SecureHospedeActionsController@deletePedido')
    ->name('hospede.delete.pedido');

Route::middleware(['auth', 'role:hospede'])
    ->delete('/hospede/cancelar/hospedagem/{id}', 'Security\\SecureHospedeActionsController@cancelarReserva')
    ->name('cancelar.hospedagem');

// Solicitação pública de acesso: cria conta sem senha conhecida e envia o
// fluxo seguro de definição de senha. Rate limit reduz abuso do endpoint.
Route::middleware('throttle:5,10')
    ->post('/pedido', 'Security\\SecureAccessRequestController@store')
    ->name('pedido.acesso');

// Pré-cadastro autenticado: atualiza somente dados cadastrais/documentos.
// A senha já foi definida no primeiro acesso e não pode ser alterada aqui.
Route::middleware(['auth', 'role:precadastro'])
    ->post('/user/create/new', 'Security\\SecurePreCadastroController@store')
    ->name('usuario.create.new');

// Criação de pagamento somente por POST + CSRF.
Route::middleware(['auth', 'role:hospede'])
    ->post('/processaRequisicao/{id}', 'Security\\SecurePagamentoController@processaRequisicao')
    ->name('pagamento.processaRequisicao');

// O parâmetro restante permanece temporariamente na URL por compatibilidade
// com a view legada; o controller seguro ignora esse valor e recalcula no servidor.
Route::middleware(['auth', 'role:hospede'])
    ->post('/processaPagamentoRestante/{id}/valor/{restante}', 'Security\\SecurePagamentoController@processaPagamentoRestante')
    ->name('pagamento.processaPagamentoRestante');
