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

/*
|--------------------------------------------------------------------------
| Recuperação temporária do fluxo legado de edição
|--------------------------------------------------------------------------
|
| O gravaEdicao() legado ainda usa redirect()->back() em falhas de validação.
| Como a tela anterior é a confirmação POST-only, o navegador seguia o 302
| como GET para /hospede/pedido/edita/confirmar e recebia 404.
|
| Enquanto a gravação final não é movida para o controller seguro, este GET
| recupera o id que o próprio Laravel colocou em _old_input, valida ownership,
| preserva os flashes de erro e retorna para a tela GET de edição.
|
*/
Route::middleware(['auth', 'role:hospede'])
    ->get('/hospede/pedido/edita/confirmar', function () {
        $pedidoId = old('id');

        if (!$pedidoId || !ctype_digit((string) $pedidoId)) {
            return redirect()->route('hospede.meuspedidos');
        }

        $pedido = \App\Hospede::where('id', (int) $pedidoId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$pedido) {
            return redirect()->route('hospede.meuspedidos');
        }

        session()->reflash();

        return redirect()->route(
            'hospede.solicitarinscricao.edit',
            \Illuminate\Support\Facades\Crypt::encrypt($pedido->id)
        );
    });
