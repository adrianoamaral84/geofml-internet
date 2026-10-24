<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //return redirect(route('login'));
    //dd('home');
    return redirect(route('home'));   
});

Route::get('/home', 'HomeController@homehome')->name('home.home');
Route::get('/logout', 'HomeController@logout')->name('home.logout');
Route::post('/logout', 'HomeController@logout')->name('home.logout');

//Change Password
Route::post('/change-password', 'HomeController@updatePassword')->name('password.updates');
Route::get('/change-passwords', 'HomeController@updatePasswordInativo')->name('password.update.teste');

// PAGAMENTO
Route::get('/pagamento', 'Pagamento\PagamentoController@prepareJson')->name('pagamento.prepareJson');
Route::get('/geraboleto', 'Pagamento\PagamentoController@geraboleto')->name('pagamento.geraboleto');
Route::get(
    '/pagamento/simulador/{id}',
    'Pagamento\PagamentoController@simulador'
)->name('pagamento.simulador');

Route::post(
    '/pagamento/simulador/{id}/aprovar',
    'Pagamento\PagamentoController@aprovarSimulacao'
)->name('pagamento.simulador.aprovar');

Route::post(
    '/pagamento/simulador/{id}/cancelar',
    'Pagamento\PagamentoController@cancelarSimulacao'
)->name('pagamento.simulador.cancelar');
// Consulta Pagamento
Route::get('/consultapagamento', 'Pagamento\PagamentoController@consultapagamento')->name('pagamento.consulta');
Route::get(
    '/pagamento/inicial/{id}/status',
    'Pagamento\PagamentoController@consultarStatusPagamentoInicial'
)->name('pagamento.inicial.status');















// Processa
Route::get('/pagamentoCarrinho/{id}/total/{total}', 'Pagamento\PagamentoController@pagamentoCarrinho')->name('pagamento.pagamentoCarrinho');
Route::get('/processaRequisicao/{id}', 'Pagamento\PagamentoController@processaRequisicao')->name('pagamento.processaRequisicao');
Route::get('/processaPagamentoRestante/{id}/valor/{restante}', 'Pagamento\PagamentoController@processaPagamentoRestante')->name('pagamento.processaPagamentoRestante');

Route::get('/admin/pagamento/create', 'Pagamento\PagamentoController@create')->name('pagamento.create');
Route::post('/admin/pagamento/store', 'Pagamento\PagamentoController@store')->name('pagamento.store');
Route::post('/admin/pagamento/update', 'Pagamento\PagamentoController@update')->name('pagamento.update');

// UPLOAD
Route::post('/upload', 'UsuarioController@upload')->name('upload');
Route::post('testes', 'UsuarioController@testes')->name('testes');

// MAIL 
Route::get('/envio/confirmacao/hospedagem/{id}', 'Mail\MailController@confirmacaoHospedagem')->name('envia.confirmacao.hospedagem');
Route::get('/mailable', function () {
    $hospede = App\Hospede::where('id', 170)->with('user')->with('tipouh')->first();
 	//dd($user);
    return new App\Mail\testedeMail($hospede);
});

Route::get('/mailable/mail', function () {
    $user = App\Hospede::where('id', 165)->with('user')->with('tipouh')->first();
    return new App\Mail\hospedagemLiberada($user);
});

Route::get('/envio/login/{id}', 'Mail\MailController@envialoginSenha')->name('envia.login.senha');
Route::get('/envio/liberado/{id}', 'Mail\MailController@enviaMailOk')->name('envia.mail.ok');
Route::get('/mail', 'Mail\MailController@testeEmail')->name('envia.mail.teste');
Route::get('/envio/libera/{id}/acesso', 'Mail\MailController@liberaAcesso')->name('libera.acesso');
Route::get('/envio/negado/{id}', 'Mail\MailController@enviaMailNegado')->name('envia.mail.negado');
Route::post('/envio/negado', 'Mail\MailController@enviaMailNegado')->name('envia.mail.negado');
Route::get('/envio/espera/{id}', 'Mail\MailController@enviaMailEspera')->name('envia.mail.espera');
Route::get('/envio/documento/vencido/{id}', 'Mail\MailController@enviaMailDataDocumentoVencido')->name('envia.enviaMailDataDocumentoVencido');

/* FINALIZAR CADASTRO */
Route::get('/{id}/finalizarcadastro', 'HomeController@finalizarcadastro')->name('mail.finalizarcadastro');
Route::get('/envios', function(){
	//return new \App\Mail\newLaravelTips();
	//\Illuminate\Support\Facades\Mail::send(new \App\Mail\newLaravelTips());
});

// OCUPACAO
// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
// Route::get('/ocupacao/categoria/mes/{mes}', 'Ocupacao\OcupacaoController@listaPorMes')->name('ocupacao.lista.mes');
// OCUPACAO




/* ROTA DE PEDIDO DE ACESSO AO SISTEMA  */
Route::get('/solicitaacesso', 'HomeController@solicitaacesso')->name('solicitaacesso');

/* RECEBE A SOLICITACAO DE PEDIDO E GRAVA NO BANCO */
Route::post('/pedido', 'HomeController@pedido')->name('pedido.acesso');
Route::get('/pedido', 'HomeController@pedido')->name('pedido.acesso');
Route::get('/user/create/new', 'HomeController@UserCreateNew')->name('usuario.create.new');
Route::post('/user/create/new', 'HomeController@UserCreateNew')->name('usuario.create.new');
/* 	FIM PEDIDO	*/

// Carrega uma combo a partir do id selecionado de outra combo
Route::get('/cascade/carregarCidades/{id}','Cascade\CascadeController@carregarCidades');
Route::get('/cascade/carregarOm/{id}','Cascade\CascadeController@carregarOm'); 
Route::get('/cascade/carregarPosto/{id}','Cascade\CascadeController@carregarPosto'); // Carrega as cidades a partir de uma UF informada Carrega as cidades a partir de uma UF informada
Route::get('/cascade/carregarPostoSituacao/{id}','Cascade\CascadeController@carregarPostoSituacao'); // Carrega as cidades a partir de uma UF informada Carrega as cidades a partir de uma UF informada
Route::get('/cascade/carregarPostoSituacao/all/{id}','Cascade\CascadeController@carregarPostoSituacaoTodos'); // Carrega as cidades a partir de uma UF informada Carrega as cidades a partir de uma UF informada

Route::get('/cascade/carregarUnidades/{id}/{tipo}','Cascade\CascadeController@carregarUnidadesHabtacionais'); // Carrega Unidades Habitacionais a partir de Grupo de Destino na tela Distribuição de UNIDADES

Auth::routes(['register' => false]);

Route::get('/', 'HomeController@index')->name('home');

Route::get('/administrador', 'HomeController@administrador')->name('administrador.index');

/* ROTAS API */
Route::get('/retornacidade/{id}', 'HomeController@listaCidadePorUF')->name('listaCidadePorUF');

/*
Route::prefix('administrador')->group(function(){

	Route::get('/', 'HomeController@index')->name('home');

});
*/

Route::group(['middleware'=>['role:administrador_geral']], function () {

	//Route::get('profile', 'UsuarioController@showProfile')->name('profile');
}); 

Route::get('profile', 'UsuarioController@showProfile')->name('profile');

/* 	ROTAS HOSPEDES*/
Route::prefix('hospede')->group(function(){

	Route::get('/', 'Hospede\HospedeController@index')->name('hospede.index');
	//Route::get('/', 'HomeController@index')->name('hospede');
	//Route::get('/solicitarinscricao', 'Hospede\HospedeController@index')->name('hospede.solicitarinscricao');
	//Route::get('/solicitarinscricao/edita/{id}', 'Hospede\HospedeController@solicitarinscricaoEditNOVO')->name('hospede.solicitarinscricao.edit');
	Route::post('/solicitarinscricao/edita/confirmar', 'Hospede\HospedeController@solicitarinscricaoEditConfirmar')->name('hospede.edita.confirmar');
	//Route::post('/solicitarinscricao/edita/confirmar', 'Hospede\HospedeController@solicitarinscricaoEditConfirmarNOVO')->name('hospede.edita.confirmar');
	
	Route::get('/pedido', 'Pedidos\PedidosController@chamaFormularioPedido')->name('hospede.solicitarinscricao');
	Route::post('/pedido/confirmar', 'Pedidos\PedidosController@confimrarPedido')->name('hospede.confirmar');
	Route::post('/pedido/store', 'Pedidos\PedidosController@store')->name('hospede.store');
	Route::get('/pedido/edita/{id}', 'EditarPedido\EditarPedidoController@index')->name('hospede.solicitarinscricao.edit');
	Route::post('/pedido/edita/confirmar', 'EditarPedido\EditarPedidoController@confirmaEdicao')->name('hospede.edita.confirmar');
	Route::post('/pedido/edita/store', 'EditarPedido\EditarPedidoController@gravaEdicao')->name('hospede.storeEdit.edita');
	





	//Route::get('/store', 'Hospede\HospedeController@store')->name('hospede.store');
	//Route::post('/store', 'Hospede\HospedeController@store')->name('hospede.store');

	//Route::get('/store/edita', 'Hospede\HospedeController@storeEdit')->name('hospede.storeEdit.edita');
	//Route::post('/store/edita', 'Hospede\HospedeController@storeEdit')->name('hospede.storeEdit.edita');
	

	//Route::post('/confirmar', 'Hospede\HospedeController@ConfirmarDadosAntigo')->name('hospede.confirmar');
	Route::get('/meuspedidos', 'Hospede\HospedeController@meuspedidos')->name('hospede.meuspedidos');
	Route::get('/meupedido/{id}', 'Hospede\HospedeController@meuspedido')->name('hospede.meupedido');
	Route::get('/delete/meupedido/{id}', 'Hospede\HospedeController@deleteInscricao')->name('hospede.delete.pedido');
	Route::get('/checkin/{id}', 'Hospede\HospedeController@checkin')->name('hospede.checkin');
	Route::get('/checkout/{id}/{hospede}', 'Hospede\HospedeController@checkout')->name('hospede.checkout');
	Route::get('/cancelar/hospedagem/{id}', 'Hospede\HospedeController@cancelarhospedagem')->name('cancelar.hospedagem');
	Route::post('/uploadrecibo', 'Hospede\HospedeController@uploadComprovantePagamento')->name('hospede.uploadrecibo');
	


});
/* */

/* 	ROTAS USUARIO*/
Route::prefix('usuario')->group(function(){

});
/* */


/* 	ROTAS ATENDENTES  */
Route::prefix('atendente')->group(function(){

	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/', 'CheckIn\CheckInController@atendente')->name('atendente');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/checkin', 'CheckIn\CheckInController@checkIn')->name('checkIn');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/checkout', 'CheckIn\CheckInController@checkOut')->name('checkOut');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/buscar', 'CheckIn\CheckInController@buscar')->name('buscar');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/buscahospedagem', 'CheckIn\CheckInController@buscahospedagem')->name('buscahospedagem');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/cadastrar/produto/carrinho', 'CheckIn\CheckInController@cadastrarProdutoCarrinho')->name('cadastrarProdutoCarrinho');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/adicionar/produto/hospedagem/{id}', 'CheckIn\CheckInController@adicionarProdutoHospedagem')->name('adicionar.produto.hospedagem');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/{id}/deletar', 'CheckIn\CheckInController@deletarItem')->name('carrinho.deletar.item');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/finalizar/carrinho', 'CheckIn\CheckInController@finalizarCarrinho')->name('carrinho.finalizar');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/finalizar/carrinho', 'CheckIn\CheckInController@finalizarCarrinho')->name('carrinho.finalizar');
	//Route::post('/{id}/deletar', 'CheckIn\CheckInController@deletarItem')->name('carrinho.deletar.item');

});
/* */



/* 	ROTAS PRE CADASTRO*/
Route::prefix('precadastro')->group(function(){

	Route::get('/', 'HomeController@precadastro')->name('precadastro');
	Route::get('/home', 'HomeController@homeUsuario')->name('usuario.home');

});
/*  */

//Route::prefix('admin')->group(function(){
//Route::prefix('admin')->group(function(){

		//CALENDARIO
		Route::get('/calendario/{id}', 'Calendario\CalendarioController@index')->name('calendario.index');
		Route::get('/calendario/mes/{id}', 'Calendario\CalendarioController@calendarioMes')->name('calendario.mes');
		Route::get('/calendario/unidade/{unidade}/{data_ini}/{data_final}', 'Calendario\CalendarioController@calendarioUnidade')->name('calendario.unidade');
		//Route::get('/calendario/unidade/{id}', 'Calendario\CalendarioController@calendarioporUnidade')->name('calendario.por.unidade');
		Route::get('/calendario/unidade/json/{id}', 'Calendario\CalendarioController@calendarioUnidadeJson')->name('calendario.unidade.json');
		// FIM CALENDARIO


		



		/* ROTAS DE HOSPEDAGEM */
		Route::prefix('hospedagem')->group(function(){

		/* HOSPEDAGEM */
		Route::get('/aguardando/index', 'Hospedagem\HospedagemController@index')->name('hospedagem.index');

		
		Route::prefix('distribuicao')->group(function(){
		// DITRIBUIÇÃO
		Route::get('/gen', 'Hospedagem\HospedagemController@distribuicaoGen')->name('hospedagem.distribuicao.gen');
		Route::get('/ofsup', 'Hospedagem\HospedagemController@distribuicaoOfSup')->name('hospedagem.distribuicao.ofsup');
		Route::get('/capten', 'Hospedagem\HospedagemController@distribuicaoCapTen')->name('hospedagem.distribuicao.capten');
		Route::get('/subten', 'Hospedagem\HospedagemController@distribuicaoSubSgt')->name('hospedagem.distribuicao.subten');
		
		Route::get('/motorhome', 'Hospedagem\HospedagemController@distribuicaoMotorhomeCamping')->name('hospedagem.distribuicao.motorhome');
		
		Route::get('/camping', 'Hospedagem\HospedagemController@distribuicaoCamping')->name('hospedagem.distribuicao.camping');
		//
		});
		
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/edit/bloqueio/{id}', 'ConfigurarHospedagem\ConfigurarHospedagemController@edit')->name('configurarhospedagem.edit');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/create', 'ConfigurarHospedagem\ConfigurarHospedagemController@create')->name('configurarhospedagem.create');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::post('/store', 'ConfigurarHospedagem\ConfigurarHospedagemController@store')->name('configurarhospedagem.store');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::post('/{id}/destroy', 'ConfigurarHospedagem\ConfigurarHospedagemController@destroy')->name('configurarhospedagem.destroy');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/{id}/destroy', 'ConfigurarHospedagem\ConfigurarHospedagemController@destroy')->name('configurarhospedagem.destroy');
		Route::get('/{id}/negar', 'Hospedagem\HospedagemController@negar')->name('hospedagem.negar');
		Route::get('/liberar', 'Hospedagem\HospedagemController@liberar')->name('hospedagem.liberar');
		Route::post('/liberar', 'Hospedagem\HospedagemController@liberar')->name('hospedagem.liberar');
		Route::get('/{id}/verdados', 'Hospedagem\HospedagemController@verdados')->name('hospedagem.verdados');
		Route::get('/aguardando', 'Hospedagem\HospedagemController@aguardando_liberacao')->name('hospedagem.aguardando_liberacao');
		Route::get('/{id}/verdados_aguardando', 'Hospedagem\HospedagemController@verdados_aguardando_liberacao')->name('hospedagem.verdados_aguardando_liberacao');
		Route::get('/{id}/liberar', 'Hospedagem\HospedagemController@liberar_uso')->name('hospedagem.liberar_uso');
		Route::get('/{id}/negar/uso', 'Hospedagem\HospedagemController@negar_uso')->name('hospedagem.negar_uso');

		Route::get('/{id}/retornar/distribuicao', 'Hospedagem\HospedagemController@retornarDistribuicao')->name('hospedagem.retornarDistribuicao');
		Route::post('/retornar/envio/comprovante', 'Hospedagem\HospedagemController@retornarEnvioComprovante')->name('hospedagem.retornarEnvioComprovante');

		//Route::post('/liberar', 'Hospedagem\HospedagemController@verdados')->name('hospedagem.verdados');
		});

		/* ROTAS DE RELATORIO */
		Route::prefix('relatorio')->group(function(){

		/* RELATORIO */
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/verdados/{id}', 'Relatorio\RelatorioController@verdados')->name('relatorio.verdados');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::post('/atualizar/', 'Relatorio\RelatorioController@atualizar')->name('relatorio.atualizar');
		
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/index', 'Relatorio\RelatorioController@index')->name('relatorio.index');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/view', 'Relatorio\RelatorioController@view')->name('relatorio.view');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::post('/view', 'Relatorio\RelatorioController@view')->name('relatorio.view');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/arrecadacao', 'Relatorio\RelatorioController@arrecadacao')->name('relatorio.arrecadacao');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/arrecadacao/view', 'Relatorio\RelatorioController@arrecadacaoview')->name('arrecadacao.view');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::post('/arrecadacao/view', 'Relatorio\RelatorioController@arrecadacaoview')->name('arrecadacao.view');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/cancelados', 'Relatorio\RelatorioController@cancelados')->name('relatorio.cancelados');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::post('/cancelados/view', 'Relatorio\RelatorioController@canceladosview')->name('cancelados.view');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/arrecadacao/total', 'Relatorio\RelatorioController@arrecadacaoTotal')->name('arrecadacaoTotlal.index');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::post('/arrecadacao/total/view', 'Relatorio\RelatorioController@arrecadacaoTotalview')->name('arrecadacao.total.view');


		});

		/* ROTAS DE ADMINISTRACAO */
		Route::prefix('administracao')->group(function(){

		// HOSPEDAGEM CONFIGURAR
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/configurar/hospedagem', 'ConfigurarHospedagem\ConfigurarHospedagemController@index')->name('configurarhospedagem.index');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/createDia/{id}', 'ConfigurarHospedagem\ConfigurarHospedagemController@createDia')->name('configurarhospedagem.create.dia');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::post('/createDiaPost', 'ConfigurarHospedagem\ConfigurarHospedagemController@createDiaPost')->name('configurarhospedagem.create.dia.post');
		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::post('/update/bloqueio/dia', 'ConfigurarHospedagem\ConfigurarHospedagemController@update')->name('configurarhospedagem.update.dia');

		/* PAGTESOURO */
		Route::get('/admin/pagamento/index', 'Pagamento\PagamentoController@index')->name('pagamento.index');
		Route::get('/admin/pagamento/{id}/edit', 'Pagamento\PagamentoController@edit')->name('pagamento.edit');

		/* TEMPOARADA */
		Route::prefix('temporada')->group(function(){

			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/', 'Temporada\TemporadaController@index')->name('temporada.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/edit', 'Temporada\TemporadaController@edit')->name('temporada.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/update', 'Temporada\TemporadaController@update')->name('temporada.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/create', 'Temporada\TemporadaController@create')->name('temporada.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/store', 'Temporada\TemporadaController@store')->name('temporada.store');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/delete', 'Temporada\TemporadaController@delete')->name('temporada.delete');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/{id}/delete', 'Temporada\TemporadaController@delete')->name('temporada.delete');
		});

		

		/* HORARIO */
		Route::prefix('horario')->group(function(){

			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/', 'Horario\HorarioController@index')->name('horario.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/edit', 'Horario\HorarioController@edit')->name('horario.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/update', 'Horario\HorarioController@update')->name('horario.update');
			
		});

		/* DADOS GERAIS */
		Route::prefix('dadosgerais')->group(function(){

			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/', 'DadosGerais\DadosGeraisController@index')->name('dadosgerais.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/edit', 'DadosGerais\DadosGeraisController@edit')->name('dadosgerais.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/update', 'DadosGerais\DadosGeraisController@update')->name('dadosgerais.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/create', 'DadosGerais\DadosGeraisController@create')->name('dadosgerais.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/store', 'DadosGerais\DadosGeraisController@store')->name('dadosgerais.store');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/delete', 'DadosGerais\DadosGeraisController@delete')->name('dadosgerais.delete');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/{id}/delete', 'DadosGerais\DadosGeraisController@delete')->name('dadosgerais.delete');
		});
		
			/** GUARNICOES */
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/guarnicao', 'Guarnicao\GuarnicaoController@index')->name('guarnicao.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/guarnicao', 'Guarnicao\GuarnicaoController@index')->name('guarnicao.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/guarnicao/create', 'Guarnicao\GuarnicaoController@showFormCadastra')->name('showFormCadastra');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/guarnicao/create', 'Guarnicao\GuarnicaoController@create')->name('guarnicao.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/guarnicao/{id}/edit', 'Guarnicao\GuarnicaoController@edit')->name('guarnicao.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/guarnicao/update', 'Guarnicao\GuarnicaoController@update')->name('guarnicao.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/guarnicao/{id}/destroy', 'Guarnicao\GuarnicaoController@destroy')->name('guarnicao.destroy');
			/* FIM GUARNICOES */
			
			/** ROTAS UF */
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/uf', 'Uf\UfController@index')->name('indexuf');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/uf', 'Uf\UfController@index')->name('indexuf');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/uf/{id}/delete', 'Uf\UfController@deleteUf')->name('deleteuf');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/uf/create', 'Uf\UfController@ufcreate')->name('uf.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/uf/cadastrar', 'Uf\UfController@cadastruf')->name('cadastruf');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/uf/{id}/edit', 'Uf\UfController@editUf')->name('edituf');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/uf/update', 'Uf\UfController@ufupdate')->name('ufupdate');
			/** FIM ROTAS UF */

			/** ROTAS FORCA */
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/forca', 'Forca\ForcaController@index')->name('forca.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/forca', 'Forca\ForcaController@index')->name('forca.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/forca/{id}/delete', 'Forca\ForcaController@forcadelete')->name('forca.delete');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/forca/create', 'Forca\ForcaController@forcacreate')->name('forca.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/forca/create', 'Forca\ForcaController@forcacreate')->name('forca.create');

			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/forca/cadastrar', 'Forca\ForcaController@forcacadastra')->name('forca.cadastra');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/forca/{id}/edit', 'Forca\ForcaController@forcaedit')->name('forca.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/forca/update', 'Forca\ForcaController@forcaupdate')->name('forca.update');
			/** FIM ROTAS FORCA */

			/** CIDADES 	*/
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/cidade/{id}/index', 'Uf\Cidade\CidadeController@index')->name('cidade.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/cidade/{id}/index', 'Uf\Cidade\CidadeController@index')->name('cidade.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/cidade/{id}/{iduf}/create', 'Uf\Cidade\CidadeController@ShowFormCreate')->name('cidade.create.show');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/cidade/create', 'Uf\Cidade\CidadeController@create')->name('cidade.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/cidade/create', 'Uf\Cidade\CidadeController@create')->name('cidade.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/cidade/{id}/{iduf}/edit', 'Uf\Cidade\CidadeController@edit')->name('cidade.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/cidade/update', 'Uf\Cidade\CidadeController@cidadeupdate')->name('cidade.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/cidade/{id}/delete', 'Uf\Cidade\CidadeController@delete')->name('cidade.delete');
			/** FIM CIDADES */

			/*** INICIO POSTO GRADUACAO ***/
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/postograduacao', 'PostoGraduacao\PostoGraduacaoController@index')->name('postograduacao.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/postograduacao', 'PostoGraduacao\PostoGraduacaoController@index')->name('postograduacao.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/postograduacao/{id}/show', 'PostoGraduacao\PostoGraduacaoController@showFormCadastra')->name('postograduacao.create.show');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/postograduacao/{id}/edit', 'PostoGraduacao\PostoGraduacaoController@edit')->name('postograduacao.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/postograduacao/update', 'PostoGraduacao\PostoGraduacaoController@update')->name('postograduacao.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/postograduacao/create', 'PostoGraduacao\PostoGraduacaoController@create')->name('postograduacao.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/postograduacao/{id}/destroy', 'PostoGraduacao\PostoGraduacaoController@destroy')->name('postograduacao.destroy');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/postograduacao/list', 'PostoGraduacao\PostoGraduacaoController@list')->name('postograduacao.list');
			
			/* FIM POSTO GRADUACAO */

			/*** Gerenciar OM */
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/om', 'GerenciarOm\GerenciarOmController@index')->name('gerenciarom.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/om', 'GerenciarOm\GerenciarOmController@index')->name('gerenciarom.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/om/{id}/create', 'GerenciarOm\GerenciarOmController@showFormCadastra')->name('gerenciarom.create.show');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/om/create', 'GerenciarOm\GerenciarOmController@create')->name('gerenciarom.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/om/update', 'GerenciarOm\GerenciarOmController@update')->name('gerenciarom.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/om/{id}/edit', 'GerenciarOm\GerenciarOmController@edit')->name('gerenciarom.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/om/{id}/destroy', 'GerenciarOm\GerenciarOmController@destroy')->name('gerenciarom.destroy');
			/*		    */
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/om', 'GerenciarOm\GerenciarOmController@listOMs')->name('gerenciarom.listOMs');
			/* Gerenciar OM */

	});
	
	/* ROTA USUARIOS */
	Route::prefix('usuarios')->group(function(){
				
		Route::get('/solicitacoes/list', 'HomeController@listaPedidos')->name('lista.pedidos');
		Route::get('/list', 'UsuarioController@index')->name('user.index');
		Route::get('/list/inativos', 'UsuarioController@ListaInativos')->name('user.list.inativos');
		Route::get('/precadastro/lista', 'UsuarioController@precadastroLista')->name('user.precadastro.lista');		
		Route::get('/cadastra', 'UsuarioController@showFormNewUser')->name('usuario.novo');
		Route::get('/{id}/index', 'UsuarioController@verDados')->name('usuario.verdados');
		Route::get('/{id}/index/aguardando', 'UsuarioController@verDadosAguardando')->name('usuario.verdados.aguardando');
		Route::get('/negados', 'UsuarioController@usuariosNegados')->name('usuarios.negados');
		Route::get('/delete/{id}', 'UsuarioController@deleteUsuario')->name('delete.user');
		Route::get('/documentos/vencidos', 'UsuarioController@documentosVencidos')->name('documentos.vencidos');
		Route::get('/ver/documento/{id}/{doc}/{tipo}/arquivo', 'UsuarioController@verDocumento')->name('documentos.verdocumento');
		
		Route::post('/envia/doc', 'UsuarioController@enviaDoc')->name('usuario.envidoc');

		//Route::post('/delete/{id}', 'UsuarioController@deleteUsuario')->name('delete.user');
		
		/* ATUALIZA DADOS USUARIO */
		//Route::get('/update', 'HomeController@atualizaDadosUsuario')->name('atualiza.dados.usuario');
		Route::post('/update', 'HomeController@atualizaDadosUsuario')->name('atualiza.dados.usuario');
	});
	
	/* ROTA HABITACAO */
	Route::prefix('habitacao')->group(function(){
		/* TARIFAS */
		Route::prefix('tarifas')->group(function(){
		
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/', 'Tarifas\TarifasUndController@index')->name('tarifas.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/edit', 'Tarifas\TarifasUndController@edit')->name('tarifas.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/update', 'Tarifas\TarifasUndController@update')->name('tarifas.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/create', 'Tarifas\TarifasUndController@create')->name('tarifas.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/store', 'Tarifas\TarifasUndController@store')->name('tarifas.store');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/delete', 'Tarifas\TarifasUndController@delete')->name('tarifas.delete');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/{id}/delete', 'Tarifas\TarifasUndController@delete')->name('tarifas.delete');
		
		});
		/* TARIFAS EXTRAS*/
		Route::prefix('tarifasextras')->group(function(){
		
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/', 'TarifaExtra\TarifaExtraController@index')->name('tarifaextra.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/edit', 'TarifaExtra\TarifaExtraController@edit')->name('tarifaextra.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/update', 'TarifaExtra\TarifaExtraController@update')->name('tarifaextra.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/create', 'TarifaExtra\TarifaExtraController@create')->name('tarifaextra.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/store', 'TarifaExtra\TarifaExtraController@store')->name('tarifaextra.store');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/delete', 'TarifaExtra\TarifaExtraController@delete')->name('tarifaextra.delete');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/{id}/delete', 'TarifaExtra\TarifaExtraController@delete')->name('tarifaextra.delete');
		
		});
		/* TIPO UNIDADE HABITACIONAL */
		Route::prefix('tipouh')->group(function(){
		
			Route::get('/', 'TipoUndHab\TipoUndHabController@index')->name('habitacao.index');
			Route::get('/{id}/edit', 'TipoUndHab\TipoUndHabController@edit')->name('habitacao.edit');
			Route::post('/update', 'TipoUndHab\TipoUndHabController@update')->name('habitacao.update');
			Route::get('/create', 'TipoUndHab\TipoUndHabController@create')->name('habitacao.create');
			Route::post('/store', 'TipoUndHab\TipoUndHabController@store')->name('habitacao.store');
			Route::get('/{id}/delete', 'TipoUndHab\TipoUndHabController@delete')->name('habitacao.delete');
			Route::post('/{id}/delete', 'TipoUndHab\TipoUndHabController@delete')->name('habitacao.delete');
		
		});

		/* CLASSE HABITACIONAL */
		Route::prefix('classe_habitacional')->group(function(){
		
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/', 'ClasseHabitacional\ClasseHabitacionalController@index')->name('classehabitacao.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/edit', 'ClasseHabitacional\ClasseHabitacionalController@edit')->name('classehabitacao.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/update', 'ClasseHabitacional\ClasseHabitacionalController@update')->name('classehabitacao.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/create', 'ClasseHabitacional\ClasseHabitacionalController@create')->name('classehabitacao.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/store', 'ClasseHabitacional\ClasseHabitacionalController@store')->name('classehabitacao.store');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/delete', 'ClasseHabitacional\ClasseHabitacionalController@delete')->name('classehabitacao.delete');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/{id}/delete', 'ClasseHabitacional\ClasseHabitacionalController@delete')->name('classehabitacao.delete');
		
		});

		/* GRUPO DESTINACAO */
		Route::prefix('grupo_destinacao')->group(function(){
		
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/', 'GrupoDestinacao\GrupoDestinacaoController@index')->name('grupo_destinacao.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/edit', 'GrupoDestinacao\GrupoDestinacaoController@edit')->name('grupo_destinacao.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/update', 'GrupoDestinacao\GrupoDestinacaoController@update')->name('grupo_destinacao.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/create', 'GrupoDestinacao\GrupoDestinacaoController@create')->name('grupo_destinacao.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/store', 'GrupoDestinacao\GrupoDestinacaoController@store')->name('grupo_destinacao.store');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/delete', 'GrupoDestinacao\GrupoDestinacaoController@delete')->name('grupo_destinacao.delete');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/{id}/delete', 'GrupoDestinacao\GrupoDestinacaoController@delete')->name('grupo_destinacao.delete');
		
		});


		/* GRUPO TARIFA */
		Route::prefix('grupo_tarifa')->group(function(){
		
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/', 'GrupoTarifa\GrupoTarifaController@index')->name('grupo_tarifa.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/edit', 'GrupoTarifa\GrupoTarifaController@edit')->name('grupo_tarifa.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/update', 'GrupoTarifa\GrupoTarifaController@update')->name('grupo_tarifa.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/create', 'GrupoTarifa\GrupoTarifaController@create')->name('grupo_tarifa.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/store', 'GrupoTarifa\GrupoTarifaController@store')->name('grupo_tarifa.store');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/delete', 'GrupoTarifa\GrupoTarifaController@delete')->name('grupo_tarifa.delete');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/{id}/delete', 'GrupoTarifa\GrupoTarifaController@delete')->name('grupo_tarifa.delete');
		
		});

		/* UNIDADE HABITACIONAL */
		
		Route::prefix('unidadehabitacional')->group(function(){
		
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/', 'UnidadeHabitacional\UnidadeHabitacionalController@index')->name('uh.index');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/edit', 'UnidadeHabitacional\UnidadeHabitacionalController@edit')->name('uh.edit');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/update', 'UnidadeHabitacional\UnidadeHabitacionalController@update')->name('uh.update');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/create', 'UnidadeHabitacional\UnidadeHabitacionalController@create')->name('uh.create');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/store', 'UnidadeHabitacional\UnidadeHabitacionalController@store')->name('uh.store');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::get('/{id}/delete', 'UnidadeHabitacional\UnidadeHabitacionalController@delete')->name('uh.delete');
			// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
			// Route::post('/{id}/delete', 'UnidadeHabitacional\UnidadeHabitacionalController@delete')->name('uh.delete');
		
		});
		
	});

	/* USUARIOS */
	// INDEX //
	Route::get('changeStatusUsuario/{id}', 'UsuarioController@changeStatus')->name('changeStatusUsuario');
	Route::post('/users/create', 'UsuarioController@salvarUsuario')->name('usuario.create');
	Route::post('/users/new', 'UsuarioController@NovoUsuario')->name('usuario.new');
	Route::get('/users/{id}/reset', 'UsuarioController@resetSenha')->name('usuario.reset');
	// VER DADOS
	//Route::get('/users/{id}/show', 'UsuarioController@showFormNewUser')->name('usuario.novo');
	//
	// Profile
	//Route::get('/profile', 'UsuarioController@showProfile')->name('profile');
	Route::get('profile/{id}/show', 'UsuarioController@showProfile')->name('profile.user');
	Route::post('editProfile', 'UsuarioController@editProfile')->name('editProfile');
	
	// Alteração de senha
	Route::get('senha', 'UsuarioController@showSenha')->name('senha');
	Route::post('editarSenha', 'UsuarioController@editarSenha')->name('editarSenha');
	/* FIM USUARIOS */

	/*** ROTAS AREA */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/area', 'AreaController@index')->name('listArea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/area', 'AreaController@index')->name('listArea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/busca', 'AreaController@buscaarea')->name('buscaarea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/busca', 'AreaController@buscaarea')->name('buscaarea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/area/cadastra', 'AreaController@showFormCadastraArea')->name('showFormCadastraArea');
	//Route::post('/area/cadastra', 'AreaController@showFormCadastraArea')->name('showFormCadastraArea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/area/{id}/show', 'AreaController@showArea')->name('showarea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/area/{id}/edit', 'AreaController@editArea')->name('editarea');

	/** CRUD AREA */
	/*
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/area/{id}/delete', 'AreaController@deleteArea')->name('deletearea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/area/cadastrar', 'AreaController@cadastraarea')->name('cadastraarea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/area/cadastrar', 'AreaController@cadastraarea')->name('cadastraarea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/area/{id}/update', 'AreaController@updatearea')->name('updatearea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/area/{id}/update', 'AreaController@updatearea')->name('updatearea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/area/update', 'AreaController@areaupdate')->name('areaupdate');
	*/
	/** FIM CRUD AREA */

	/** CRUD AREA */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/area/{id}/delete', 'AreaController@deleteArea')->name('deletearea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/area/cadastrar', 'AreaController@cadastraarea')->name('cadastraarea');
	//Route::get('/area/cadastrar', 'AreaController@cadastraarea')->name('cadastraarea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/area/{id}/update', 'AreaController@updatearea')->name('updatearea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/area/{id}/update', 'AreaController@updatearea')->name('updatearea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/area/update', 'AreaController@areaupdate')->name('areaupdate');
	/** FIM CRUD AREA */

	/** SUBAREA */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/model', 'SubArea\SubAreaController@index')->name('indexsubarea');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/subarea/{id}/index', 'SubArea\SubAreaController@index')->name('subarea.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/subarea/{id}/index', 'SubArea\SubAreaController@index')->name('subarea.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/subarea/create', 'SubArea\SubAreaController@create')->name('subarea.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/subarea/{id}/edit', 'SubArea\SubAreaController@edit')->name('subarea.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/subarea/update', 'SubArea\SubAreaController@subareaupdate')->name('subarea.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/subarea/{id}/create', 'SubArea\SubAreaController@ShowFormCreate')->name('subarea.create.show');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/subarea/{id}/delete', 'SubArea\SubAreaController@deleteSubArea')->name('deletesubarea');
	/** FIM SUBAREA */

	/*** ESTADO CIVIL */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/estadocivil', 'EstadoCivil\EstadoCivilController@index')->name('estadocivil.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/estadocivil', 'EstadoCivil\EstadoCivilController@index')->name('estadocivil.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/estadocivil/create', 'EstadoCivil\EstadoCivilController@showFormCadastra')->name('estadocivil.create.show');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/estadocivil/create', 'EstadoCivil\EstadoCivilController@create')->name('estadocivil.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/estadocivil/update', 'EstadoCivil\EstadoCivilController@update')->name('estadocivil.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/estadocivil/{id}/edit', 'EstadoCivil\EstadoCivilController@edit')->name('estadocivil.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/estadocivil/{id}/destroy', 'EstadoCivil\EstadoCivilController@destroy')->name('estadocivil.destroy');
	/* FIM ESTADO CIVIL */

	/*** SITUACAO CANDIDATO */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/situacaocandidato', 'SituacaoCandidato\SituacaoCandidatoController@index')->name('situacaocandidato.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/situacaocandidato', 'SituacaoCandidato\SituacaoCandidatoController@index')->name('situacaocandidato.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/situacaocandidato/create', 'SituacaoCandidato\SituacaoCandidatoController@showFormCadastra')->name('situacaocandidato.create.show');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/situacaocandidato/create', 'SituacaoCandidato\SituacaoCandidatoController@create')->name('situacaocandidato.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/situacaocandidato/update', 'SituacaoCandidato\SituacaoCandidatoController@update')->name('situacaocandidato.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/situacaocandidato/{id}/edit', 'SituacaoCandidato\SituacaoCandidatoController@edit')->name('situacaocandidato.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/situacaocandidato/{id}/destroy', 'SituacaoCandidato\SituacaoCandidatoController@destroy')->name('situacaocandidato.destroy');
	/* FIM SITUACAO CANDIDATO */

	/*** DOCUMENTO MILITAR */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/documentomilitar', 'TipoDocumentoMilitar\TipoDocumentoMilitarController@index')->name('documentomilitar.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/documentomilitar', 'TipoDocumentoMilitar\TipoDocumentoMilitarController@index')->name('documentomilitar.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/documentomilitar/create', 'TipoDocumentoMilitar\TipoDocumentoMilitarController@showFormCadastra')->name('documentomilitar.create.show');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/documentomilitar/create', 'TipoDocumentoMilitar\TipoDocumentoMilitarController@create')->name('documentomilitar.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/documentomilitar/update', 'TipoDocumentoMilitar\TipoDocumentoMilitarController@update')->name('documentomilitar.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/documentomilitar/{id}/edit', 'TipoDocumentoMilitar\TipoDocumentoMilitarController@edit')->name('documentomilitar.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/documentomilitar/{id}/destroy', 'TipoDocumentoMilitar\TipoDocumentoMilitarController@destroy')->name('documentomilitar.destroy');
	/* FIM DOCUMENTO MILITAR */

	/*** INICIO CATEGORIA ***/
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/categoria', 'Categoria\CategoriaController@index')->name('categoria.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/categoria', 'Categoria\CategoriaController@index')->name('categoria.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/categoria/show', 'Categoria\CategoriaController@showFormCadastra')->name('categoria.create.show');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/categoria/{id}/edit', 'Categoria\CategoriaController@edit')->name('categoria.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/categoria/update', 'Categoria\CategoriaController@update')->name('categoria.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/categoria/create', 'Categoria\CategoriaController@create')->name('categoria.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/categoria/{id}/destroy', 'Categoria\CategoriaController@destroy')->name('categoria.destroy');
	/* FIM CATEGORIA */

	/*** Título / Diploma */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/titulodiploma', 'TituloDiploma\TituloDiplomaController@index')->name('titulodiploma.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/titulodiploma', 'TituloDiploma\TituloDiplomaController@index')->name('titulodiploma.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/titulodiploma/create', 'TituloDiploma\TituloDiplomaController@showFormCadastra')->name('titulodiploma.create.show');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/titulodiploma/create', 'TituloDiploma\TituloDiplomaController@create')->name('titulodiploma.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/titulodiploma/update', 'TituloDiploma\TituloDiplomaController@update')->name('titulodiploma.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/titulodiploma/{id}/edit', 'TituloDiploma\TituloDiplomaController@edit')->name('titulodiploma.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/titulodiploma/{id}/destroy', 'TituloDiploma\TituloDiplomaController@destroy')->name('titulodiploma.destroy');
	/* FIM Título / Diploma */

	/*** CURSO */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/curso', 'Curso\CursoController@index')->name('curso.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/curso', 'Curso\CursoController@index')->name('curso.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/curso/create', 'Curso\CursoController@showFormCadastra')->name('curso.create.show');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/curso/create', 'Curso\CursoController@create')->name('curso.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/curso/update', 'Curso\CursoController@update')->name('curso.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/curso/{id}/edit', 'Curso\CursoController@edit')->name('curso.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/curso/{id}/destroy', 'Curso\CursoController@destroy')->name('curso.destroy');
	/* FIM CURSO */

	/*** Proficiencia */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/proficiencia', 'Proficiencia\ProficienciaController@index')->name('proficiencia.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/proficiencia', 'Proficiencia\ProficienciaController@index')->name('proficiencia.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/proficiencia/create', 'Proficiencia\ProficienciaController@showFormCadastra')->name('proficiencia.create.show');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/proficiencia/create', 'Proficiencia\ProficienciaController@create')->name('proficiencia.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/proficiencia/update', 'Proficiencia\ProficienciaController@update')->name('proficiencia.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/proficiencia/{id}/edit', 'Proficiencia\ProficienciaController@edit')->name('proficiencia.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/proficiencia/{id}/destroy', 'Proficiencia\ProficienciaController@destroy')->name('proficiencia.destroy');
	/* FIM Proficiencia */

	/*** ConselhoProfissional */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/conselhoprofissional', 'ConselhoProfissional\ConselhoProfissionalController@index')->name('conselhoprofissional.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/conselhoprofissional', 'ConselhoProfissional\ConselhoProfissionalController@index')->name('conselhoprofissional.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/conselhoprofissional/create', 'ConselhoProfissional\ConselhoProfissionalController@showFormCadastra')->name('conselhoprofissional.create.show');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/conselhoprofissional/create', 'ConselhoProfissional\ConselhoProfissionalController@create')->name('conselhoprofissional.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/conselhoprofissional/update', 'ConselhoProfissional\ConselhoProfissionalController@update')->name('conselhoprofissional.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/conselhoprofissional/{id}/edit', 'ConselhoProfissional\ConselhoProfissionalController@edit')->name('conselhoprofissional.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/conselhoprofissional/{id}/destroy', 'ConselhoProfissional\ConselhoProfissionalController@destroy')->name('conselhoprofissional.destroy');
	/* FIM ConselhoProfissional */

	/*** TipoPublicacao */
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/tipopublicacao', 'TipoPublicacao\TipoPublicacaoController@index')->name('tipopublicacao.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/tipopublicacao', 'TipoPublicacao\TipoPublicacaoController@index')->name('tipopublicacao.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/tipopublicacao/create', 'TipoPublicacao\TipoPublicacaoController@showFormCadastra')->name('tipopublicacao.create.show');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/tipopublicacao/create', 'TipoPublicacao\TipoPublicacaoController@create')->name('tipopublicacao.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/tipopublicacao/update', 'TipoPublicacao\TipoPublicacaoController@update')->name('tipopublicacao.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/tipopublicacao/{id}/edit', 'TipoPublicacao\TipoPublicacaoController@edit')->name('tipopublicacao.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/tipopublicacao/{id}/destroy', 'TipoPublicacao\TipoPublicacaoController@destroy')->name('tipopublicacao.destroy');
	/* FIM ConselhoProfissional */

	






	//});
	//});


	// GERENCIAR E-MAILS
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/email/gerenciar', 'GerenciarEmails\GerenciaEmailController@index')->name('email.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/email/editar/{id}', 'GerenciarEmails\GerenciaEmailController@editar')->name('email.editar');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/email/atualizar', 'GerenciarEmails\GerenciaEmailController@atualizar')->name('email.atualizar');
	
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/email/gerenciar/assinatura', 'Assinatura\AssinaturaController@index')->name('assinatura.index');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/email/gerenciar/assinatura/{id}/edit', 'Assinatura\AssinaturaController@edit')->name('assinatura.edit');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/email/gerenciar/assinatura/update', 'Assinatura\AssinaturaController@update')->name('assinatura.update');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/email/gerenciar/assinatura/create', 'Assinatura\AssinaturaController@create')->name('assinatura.create');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/email/gerenciar/assinatura/store', 'Assinatura\AssinaturaController@store')->name('assinatura.store');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::get('/email/gerenciar/assinatura/{id}/delete', 'Assinatura\AssinaturaController@delete')->name('assinatura.delete');
	// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
	// Route::post('/email/gerenciar/assinatura/{id}/delete', 'Assinatura\AssinaturaController@delete')->name('assinatura.delete');
	


// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
// Route::get('/usuarios/{user}/documentos/{tipo}', 'UserDocumentoController@show')
->name('usuarios.documentos.show')
->middleware('auth');



	/* GRAFICOS */
	Route::prefix('graficos')->group(function(){

		// MIGRACAO: controller ausente no modulo Internet; rota desabilitada ate restauracao.
		// Route::get('/get/usuarios/ativos', 'Graficos\GraficosController@geUsuariosAtivos')->name('graficos.getUsuariosAtivos');

	});
	/* FIM DE GRAFICOS */








	Auth::routes();
	//Route::get('/home', 'HomeController@index')->name('home');
