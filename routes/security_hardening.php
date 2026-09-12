<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:hospede'])
    ->delete('/hospede/delete/meupedido/{id}', 'Security\SecureHospedeActionsController@deletePedido')
    ->name('hospede.delete.pedido');

Route::middleware(['auth', 'role:hospede'])
    ->delete('/hospede/cancelar/hospedagem/{id}', 'Security\SecureHospedeActionsController@cancelarReserva')
    ->name('cancelar.hospedagem');

Route::middleware(['auth', 'role:atendente|administrador_geral|auxiliar_administrador_geral'])
    ->delete('/atendente/hospedagem/{id}/checkin', 'Security\SecureHospedeActionsController@checkin')
    ->name('hospede.checkin');

Route::middleware(['auth', 'role:atendente|administrador_geral|auxiliar_administrador_geral'])
    ->delete('/atendente/hospedagem/{id}/checkout', 'Security\SecureHospedeActionsController@checkout')
    ->name('hospede.checkout');

// Endpoints de pagamento/debug legados que não fazem parte do fluxo do hóspede.
Route::get('/pagamento', 'Security\BlockedLegacyGetController@legado');
Route::get('/geraboleto', 'Security\BlockedLegacyGetController@legado');
Route::get('/consultapagamento', 'Security\BlockedLegacyGetController@legado');
Route::get('/pagamentoCarrinho/{id}/total/{total}', 'Security\BlockedLegacyGetController@legado');
Route::get('/admin/pagamento/create', 'Security\BlockedLegacyGetController@legado');
Route::post('/admin/pagamento/store', 'Security\BlockedLegacyGetController@legado');
Route::post('/admin/pagamento/update', 'Security\BlockedLegacyGetController@legado');

// Endpoints de teste/debug de e-mail legados.
Route::get('/mailable', 'Security\BlockedLegacyGetController@legado');
Route::get('/mailable/mail', 'Security\BlockedLegacyGetController@legado');
Route::get('/mail', 'Security\BlockedLegacyGetController@legado');
Route::get('/envio/confirmacao/hospedagem/{id}', 'Security\BlockedLegacyGetController@legado');
Route::get('/envios', 'Security\BlockedLegacyGetController@legado');

// Fluxos administrativos de usuário/e-mail pertencem ao GeoFML Admin, não ao Internet.
Route::get('/envio/login/{id}', 'Security\BlockedLegacyGetController@legado');
Route::get('/envio/liberado/{id}', 'Security\BlockedLegacyGetController@legado');
Route::get('/envio/libera/{id}/acesso', 'Security\BlockedLegacyGetController@legado');
Route::get('/envio/negado/{id}', 'Security\BlockedLegacyGetController@legado');
Route::post('/envio/negado', 'Security\BlockedLegacyGetController@legado');
Route::get('/envio/espera/{id}', 'Security\BlockedLegacyGetController@legado');
Route::get('/envio/documento/vencido/{id}', 'Security\BlockedLegacyGetController@legado');

// Mutações legadas que eram acessíveis por GET.
// Os respectivos POSTs legítimos continuam registrados no web.php/Auth::routes.
Route::get('/logout', 'Security\BlockedLegacyGetController@postOnly');
Route::get('/pedido', 'Security\BlockedLegacyGetController@postOnly');
Route::get('/user/create/new', 'Security\BlockedLegacyGetController@postOnly');

// Solicitação pública de acesso: cria conta sem senha conhecida e envia o
// fluxo oficial de definição de senha. Rate limit reduz abuso do endpoint.
Route::middleware('throttle:5,10')
    ->post('/pedido', 'Security\SecureAccessRequestController@store')
    ->name('pedido.acesso');

// Bloqueia os GETs legados que criavam pagamentos. O web.php antigo ainda
// registra essas URLs como GET, então elas são sobrescritas aqui por último.
Route::middleware(['auth', 'role:hospede'])
    ->get('/processaRequisicao/{id}', 'Security\BlockedLegacyGetController@pagamento');

Route::middleware(['auth', 'role:hospede'])
    ->get('/processaPagamentoRestante/{id}/valor/{restante}', 'Security\BlockedLegacyGetController@pagamento');

// Criação de pagamento somente por POST + CSRF.
Route::middleware(['auth', 'role:hospede'])
    ->post('/processaRequisicao/{id}', 'Security\SecurePagamentoController@processaRequisicao')
    ->name('pagamento.processaRequisicao');

// O parâmetro restante permanece temporariamente na URL por compatibilidade
// com a view legada; o controller seguro ignora esse valor e recalcula no servidor.
Route::middleware(['auth', 'role:hospede'])
    ->post('/processaPagamentoRestante/{id}/valor/{restante}', 'Security\SecurePagamentoController@processaPagamentoRestante')
    ->name('pagamento.processaPagamentoRestante');
