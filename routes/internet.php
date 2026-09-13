<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GeoFML Internet - rotas operacionais
|--------------------------------------------------------------------------
|
| Este arquivo contém somente os fluxos que pertencem ao portal Internet.
| As rotas administrativas históricas permanecem em web.php apenas como
| referência legada e não são carregadas em runtime.
|
*/

Route::get('/', function () {
    return redirect()->route('home.home');
});

Route::get('/home', 'HomeController@homehome')->name('home.home');

/* Autenticação, login, logout e recuperação de senha. */
Auth::routes(['register' => false]);

/* Solicitação pública de primeiro acesso. O POST seguro é registrado por
 * security_hardening.php, carregado depois deste arquivo. */
Route::get('/solicitaacesso', 'HomeController@solicitaacesso')->name('solicitaacesso');
Route::get('/reload', 'HomeController@reload')->name('reload');

/* Combos usados no pré-cadastro e no fluxo de hospedagem. */
Route::get('/cascade/carregarCidades/{id}', 'Cascade\\CascadeController@carregarCidades');
Route::get('/cascade/carregarOm/{id}', 'Cascade\\CascadeController@carregarOm');
Route::get('/cascade/carregarPosto/{id}', 'Cascade\\CascadeController@carregarPosto');
Route::get('/cascade/carregarPostoSituacao/{id}', 'Cascade\\CascadeController@carregarPostoSituacao');
Route::get('/cascade/carregarPostoSituacao/all/{id}', 'Cascade\\CascadeController@carregarPostoSituacaoTodos');
Route::get('/cascade/carregarUnidades/{id}/{tipo}', 'Cascade\\CascadeController@carregarUnidadesHabtacionais');
Route::get('/retornacidade/{id}', 'HomeController@listaCidadePorUF')->name('listaCidadePorUF');

/* Perfil e alteração voluntária de senha do próprio usuário. */
Route::middleware('auth')->group(function () {
    Route::get('/profile', 'UsuarioController@showProfile')->name('profile');
    Route::get('/senha', 'UsuarioController@showSenha')->name('senha');
    Route::post('/editarSenha', 'UsuarioController@editarSenha')->name('editarSenha');
});

/* Pré-cadastro. O POST /user/create/new seguro é registrado no hardening. */
Route::middleware('auth')->prefix('precadastro')->group(function () {
    Route::get('/', 'HomeController@precadastro')->name('precadastro');
    Route::get('/home', 'HomeController@homeUsuario')->name('usuario.home');
});

/* Calendário/consulta de disponibilidade utilizados pelo portal. */
Route::middleware('auth')->group(function () {
    Route::get('/calendario/{id}', 'Calendario\\CalendarioController@index')->name('calendario.index');
    Route::get('/calendario/mes/{id}', 'Calendario\\CalendarioController@calendarioMes')->name('calendario.mes');
    Route::get('/calendario/unidade/{unidade}/{data_ini}/{data_final}', 'Calendario\\CalendarioController@calendarioUnidade')->name('calendario.unidade');
    Route::get('/calendario/unidade/json/{id}', 'Calendario\\CalendarioController@calendarioUnidadeJson')->name('calendario.unidade.json');
});

/* Fluxo operacional do hóspede. */
Route::middleware(['auth', 'role:hospede'])->prefix('hospede')->group(function () {
    Route::get('/', 'Security\\SecureHospedeController@index')->name('hospede.index');

    Route::get('/pedido', 'Security\\SecurePedidoController@create')->name('hospede.solicitarinscricao');
    Route::post('/pedido/confirmar', 'Security\\SecurePedidoController@confirmar')->name('hospede.confirmar');
    Route::post('/pedido/store', 'Security\\SecurePedidoStoreController@store')->name('hospede.store');

    Route::get('/pedido/edita/{id}', 'EditarPedido\\EditarPedidoController@index')->name('hospede.solicitarinscricao.edit');
    Route::post('/pedido/edita/confirmar', 'EditarPedido\\EditarPedidoController@confirmaEdicao')->name('hospede.edita.confirmar');
    Route::post('/pedido/edita/store', 'EditarPedido\\EditarPedidoController@gravaEdicao')->name('hospede.storeEdit.edita');

    Route::get('/meuspedidos', 'Security\\SecureHospedeController@meuspedidos')->name('hospede.meuspedidos');
    Route::get('/meupedido/{id}', 'Security\\SecureHospedeController@meupedido')->name('hospede.meupedido');
    Route::post('/uploadrecibo', 'Security\\SecureHospedeController@uploadComprovantePagamento')->name('hospede.uploadrecibo');
});

/* Consulta/visualização de documentos do próprio usuário. */
Route::get('/usuarios/{user}/documentos/{tipo}', 'UserDocumento\\UserDocumentoController@show')
    ->middleware('auth')
    ->name('usuarios.documentos.show');

/* Compatibilidade temporária com a tela de pré-cadastro legada, agora usando
 * o mesmo leitor seguro e controle de ownership da rota canônica. */
Route::get('/usuarios/ver/documento/{id}/{doc}/{tipo}/arquivo', 'UserDocumento\\UserDocumentoController@showLegacy')
    ->middleware('auth')
    ->name('documentos.verdocumento');

/* PagTesouro: todo o fluxo operacional passa pelo controller seguro.
 * O simulador só existe quando o modo de teste estiver explicitamente habilitado. */
Route::middleware(['auth', 'role:hospede'])->group(function () {
    Route::get('/pagamento/inicial/{id}/status', 'Security\\SecurePagamentoController@consultarStatusPagamentoInicial')
        ->name('pagamento.inicial.status');

    /* Compatibilidade temporária com a view legada meu_pedido.blade.php.
     * A view ainda referencia este nome dentro de um comentário HTML, que o
     * Blade continua avaliando. A rota permanece protegida e usa o mesmo
     * controller seguro da consulta de status atual. */
    Route::get('/pagamento/consulta/{id}', 'Security\\SecurePagamentoController@consultarStatusPagamentoInicial')
        ->name('pagamento.consulta');

    if (config('services.pagtesouro.modo_teste')) {
        Route::get('/pagamento/simulador/{id}', 'Security\\SecurePagamentoController@simulador')
            ->name('pagamento.simulador');

        Route::post('/pagamento/simulador/{id}/aprovar', 'Security\\SecurePagamentoController@aprovarSimulacao')
            ->name('pagamento.simulador.aprovar');

        Route::post('/pagamento/simulador/{id}/cancelar', 'Security\\SecurePagamentoController@cancelarSimulacao')
            ->name('pagamento.simulador.cancelar');
    }
});

/*
|--------------------------------------------------------------------------
| Sentinelas de compatibilidade da view legada de pedido
|--------------------------------------------------------------------------
|
| A view meuspedidos/meu_pedido.blade.php ainda contém referências Blade
| a ações administrativas que não pertencem ao Portal Internet. O Blade
| resolve route() mesmo quando o trecho está oculto por perfil ou comentário
| HTML. Estes nomes existem somente para permitir a renderização da view.
| Qualquer acesso efetivo é recusado com 404 e nenhuma ação é executada.
|
*/
Route::middleware('auth')->group(function () {
    $rotaLegadaBloqueada = function () {
        abort(404);
    };

    Route::match(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], '/_legacy/internet/hospedagem/liberar/{id}', $rotaLegadaBloqueada)
        ->name('hospedagem.liberar');

    Route::match(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], '/_legacy/internet/hospedagem/negar/{id}', $rotaLegadaBloqueada)
        ->name('hospedagem.negar');

    Route::match(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], '/_legacy/internet/hospede/checkin/{id}', $rotaLegadaBloqueada)
        ->name('hospede.checkin');

    Route::match(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], '/_legacy/internet/hospede/checkout/{id}/{hospede}', $rotaLegadaBloqueada)
        ->name('hospede.checkout');

    Route::match(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], '/_legacy/internet/hospedagem/{id}/produto', $rotaLegadaBloqueada)
        ->name('adicionar.produto.hospedagem');

    Route::match(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], '/_legacy/internet/hospedagem/{id}/fila-espera', $rotaLegadaBloqueada)
        ->name('envia.mail.espera');
});
