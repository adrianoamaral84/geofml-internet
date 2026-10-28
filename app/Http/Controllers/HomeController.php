<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pedido;
use App\Cidade;
use Auth;
use Crypt;
use \App\User;
use \App\Situacao;
use \App\Nivel;
use Carbon\Carbon;
use \App\Http\Controllers\Mail\MailController;
use Illuminate\Support\Facades\Hash;
use Config;
use App\UserDocumento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\DocumentoService;






class HomeController extends Controller
{
      /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
         $this->middleware('auth')->except('solicitaacesso', 'pedido', 'updatePassword', 'updatePasswordteste');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $usuario = Auth::user();
        $hoje = Carbon::today();
        $pedidos = \App\Hospede::where('user_id', $usuario->id)
            ->with(['tipouh', 'status_hospedagem'])
            ->orderBy('data_inicio')
            ->get();

        $resumo = [
            'total' => $pedidos->count(),
            'aguardando' => $pedidos->where('status', 0)->count(),
            'aprovados' => $pedidos->where('status', 2)->count(),
            'fila' => $pedidos->where('status', 7)->count(),
        ];

        $proximosPedidos = $pedidos->filter(function ($pedido) use ($hoje) {
            return !in_array((int) $pedido->status, [1, 6], true)
                && $pedido->data_termino
                && Carbon::parse($pedido->data_termino)->startOfDay()->gte($hoje);
        })->take(5);

        $pendencias = $pedidos->filter(function ($pedido) {
            return in_array((int) $pedido->status, [3, 4, 5], true);
        })->take(5);

        $competencia = $hoje->copy()->startOfMonth();
        $meses = collect();
        for ($i = 0; $i < 3; $i++) {
            $mes = $competencia->copy()->addMonthsNoOverflow($i);
            $porMes = $pedidos->filter(function ($pedido) use ($mes) {
                if (in_array((int) $pedido->status, [1, 6], true) || !$pedido->data_inicio) {
                    return false;
                }
                $entrada = Carbon::parse($pedido->data_inicio);
                return $entrada->year === $mes->year && $entrada->month === $mes->month;
            });
            $temporada = \App\Temporada::whereDate('data_inicio', '<=', $mes->toDateString())
                ->whereDate('data_termino', '>=', $mes->toDateString())->first();
            $alta = $temporada && (int) $temporada->tipo_temporada_id === 1;
            $configuracao = \App\QuantidadeReserva::first();
            $limiteComum = $alta ? ($configuracao ? (int) $configuracao->reservas : null) : 2;
            $meses->push([
                'titulo' => $mes->locale('pt_BR')->translatedFormat('F/Y'),
                'chave' => $mes->format('Y-m'),
                'alta' => $alta,
                'temporada_definida' => (bool) $temporada,
                'comuns' => $porMes->filter(fn ($p) => !in_array((int) $p->tipo_und_id, [11, 12], true))->count(),
                'camping' => $porMes->filter(fn ($p) => in_array((int) $p->tipo_und_id, [11, 12], true))->count(),
                'limite_comum' => $limiteComum,
                'limite_camping' => 2,
            ]);
        }

        // Indicadores adicionais, calculados somente sobre os pedidos do usuário.
        $proximaHospedagem = $proximosPedidos->first();
        $diasParaHospedagem = $proximaHospedagem
            ? max(0, (int) $hoje->diffInDays(Carbon::parse($proximaHospedagem->data_inicio)->startOfDay(), false))
            : null;

        $pedidosFinanceiros = $pedidos->filter(fn ($pedido) => !in_array((int) $pedido->status, [1, 6], true));
        $financeiro = [
            'total' => round((float) $pedidosFinanceiros->sum(fn ($p) => (float) ($p->valor ?? 0)), 2),
            'pago' => round((float) $pedidosFinanceiros->sum(fn ($p) => (float) ($p->valor_pago ?? 0)), 2),
        ];
        $financeiro['restante'] = max(0, round($financeiro['total'] - $financeiro['pago'], 2));

        $alertas = collect();
        if ((int) ($usuario->indeterminado ?? 0) !== 1 && !empty($usuario->validade)) {
            $validade = Carbon::parse($usuario->validade)->startOfDay();
            if ($validade->lte($hoje->copy()->addDays(30))) {
                $alertas->push([
                    'titulo' => $validade->lt($hoje) ? 'Documento vencido' : 'Documento próximo do vencimento',
                    'descricao' => 'Confira a validade do seu documento no cadastro.',
                    'url' => route('profile'),
                ]);
            }
        }
        foreach ($pendencias as $pedido) {
            $alertas->push([
                'titulo' => optional($pedido->status_hospedagem)->status ?? 'Pendência de hospedagem',
                'descricao' => 'Consulte a solicitação com entrada em ' . Carbon::parse($pedido->data_inicio)->format('d/m/Y') . '.',
                'url' => route('hospede.meupedido', ['id' => Crypt::encrypt($pedido->id)]),
            ]);
        }
        if ($proximaHospedagem && $diasParaHospedagem === 0) {
            $alertas->push([
                'titulo' => 'Hospedagem com entrada hoje',
                'descricao' => 'Confira as orientações de sua solicitação.',
                'url' => route('hospede.meupedido', ['id' => Crypt::encrypt($proximaHospedagem->id)]),
            ]);
        }

        // Mini calendário: somente datas das solicitações, sem presumir disponibilidade.
        $calendarioInicio = $hoje->copy()->startOfMonth();
        $calendarioDias = collect();
        for ($dia = 0; $dia < $calendarioInicio->daysInMonth; $dia++) {
            $data = $calendarioInicio->copy()->addDays($dia);
            $reservasDoDia = $pedidos->filter(function ($pedido) use ($data) {
                return !in_array((int) $pedido->status, [1, 6], true)
                    && $pedido->data_inicio && $pedido->data_termino
                    && Carbon::parse($pedido->data_inicio)->startOfDay()->lte($data)
                    && Carbon::parse($pedido->data_termino)->startOfDay()->gt($data);
            });
            $calendarioDias->push([
                'dia' => $data->day,
                'data' => $data->format('d/m/Y'),
                'total' => $reservasDoDia->count(),
                'pedidos' => $reservasDoDia->map(fn ($p) => ['id' => $p->id, 'tipo' => optional($p->tipouh)->descricao ?? 'Hospedagem'])->values()->all(),
                'aprovados' => $reservasDoDia->where('status', 2)->count(),
                'espera' => $reservasDoDia->where('status', 7)->count(),
            ]);
        }
        $calendarioOffset = $calendarioInicio->dayOfWeek;
        $calendarioTitulo = $calendarioInicio->locale('pt_BR')->translatedFormat('F/Y');

        $count = \App\Message::countMsg($usuario->id);
        $mensagens = \App\Message::newMessage($usuario->id)->take(3);

        return view('home', compact('usuario', 'resumo', 'proximosPedidos', 'pendencias', 'meses', 'count', 'mensagens', 'proximaHospedagem', 'diasParaHospedagem', 'financeiro', 'alertas', 'calendarioDias', 'calendarioOffset', 'calendarioTitulo'));
    }

    public function logout(Request $request){

        Auth::logout();
        return redirect('/login');
        
    }

    public function administrador()
    {
        return $this->index();
    }

    public function homehome()
    {
        
        
        if(Auth::user()->hasRole('administrador_especial')) {
            //dd('ADMIN');
            return redirect('/admin');
        }
        if(Auth::user()->hasRole('administrador_geral')) {
            //dd('USER');
            return redirect('/admin');
        }
        if(Auth::user()->hasRole('atendente')) {
            //dd('USER');
            return redirect('/atendente');
        }
        if(Auth::user()->hasRole('hospede')) {
            //dd('USER');
            return redirect('/hospede');
        }
        if(Auth::user()->hasRole('precadastro')) {
        
        return redirect()->route('precadastro');
          //dd('pre');
            //return view('usuario.consulta', compact('menuAtivo', 'consulta', 'search'));
        }
    //dd('home');
    //return view('home');
    }

    public function precadastro()
    {
        
        $user = Auth::user();
        $id = $user->id;
        $count = User::where('id', $id)->count();



        if($count == 1){
        $user = User::findOrFail($id);
        $perfis = \App\Models\Role::all();
        $oms = \App\GerenciarOm::all();
        $postos = \App\PostoGraduacao::all();
        $forcas = \App\Forca::all();
        $ufs = \App\Uf::all();
        $cidades = \App\Cidade::all();
        $situacoes = Situacao::all();
        $nivels = Nivel::all();
        $hoje = date('Y-m-d');
        }else{
            abort('404');
        }

        if($user->indeterminado == 1){
            $user->validade = null;
        }
        $bin = base64_decode($user->documento, true);
        
        $min = date("Y-m-d");
        $documentoService = new \App\Services\DocumentoService();

        $docFrente = $documentoService->obterFrente($user);
        $docVerso = $documentoService->obterVerso($user);       
        return view('pedido.precadastro', compact('perfis', 'oms', 'postos', 'forcas', 'ufs', 'cidades', 'situacoes', 'user', 'nivels', 'hoje', 'min','docFrente', 'docVerso'));
    
    }

    public function solicitaacesso()
    {
        
        $menuAtivo = "administracao";
        return view('solicitaacesso.index');
    }

    public function pedido(Request $request)
    {  

        date_default_timezone_set('America/Sao_Paulo');

        if (isset($request['cpf'])) 
            $request['cpf'] = str_replace([".","-"], "", $request['cpf']);

        if ($request['celular'] != NULL) 
            $request['celular'] = str_replace(["(",")"," ","-"], "", $request['celular']);

        $customMessages = [
            'nome.min' => 'Nome Guerra deve ter no min 2 caracteres',
            'nome.max' => 'Nome Guerra deve ter no max 100 caracteres',            
            'nome.required' => 'Campo obrigatório',

            'email.min' => 'E-mail deve ter no min 5 caracteres',
            'email.max' => 'E-mail deve ter no max 100 caracteres',            
            'email.required' => 'Campo obrigatório',

            'cpf.min' => 'CPF deve ter no min 10 caracteres',
            'cpf.max' => 'CPF deve ter no max 11 caracteres',            
            'cpf.required' => 'Campo obrigatório',

           
            'celular.max' => 'Celular deve ter no max 11 caracteres',            
            'celular.required' => 'Campo obrigatório',

        ];

        $validatedData = [
            'nome' => 'required|max:100|min:2',
            'email' => 'required|email|max:100|min:5',
            'cpf' => 'required|max:11|min:10',
            'celular' => 'required|max:11',

        ];
        $validatedData = $request->validate($validatedData, $customMessages); 

       
        
        if(!$this->validarCPF($validatedData['cpf'])){
            return back()->withInput()->withErrors(['CPF inválido.']);
        }
        
        if(!$this->verificarCPFCadastradoPedido($validatedData['cpf'])){
            return back()->withInput()->withErrors(['Este CPF já está cadastrado no sistema.']);
        }
        

        // // Validação se e-mail já está cadastrado
        if(!$this->verificarEmailCadastrado($validatedData['email'])){
            return back()->withInput()->withErrors(['Este E-mail já está cadastrado no sistema.']);
        }

        //dd($validatedData['cpf']);
        $usuario = new User();
        $usuario->name = strtoupper($validatedData['nome']);
        $usuario->email = $validatedData['email'];
        $usuario->cpf = $validatedData['cpf'];
        $usuario->telefone = $validatedData['celular'];
        $usuario->status = 5;
        $usuario->perfil_id = 5;

        $usuario->password = Hash::make($usuario->cpf);
        //$usuario->status = 1;
        //$usuario->save();
        //dd($usuario->id);
        if($usuario->save()){
        $usuario->syncRoles(['5']);
        $id = Crypt::encrypt($usuario->id);
        \Illuminate\Support\Facades\Mail::queue(new \App\Mail\newLaravelTips($usuario));
        \Session::flash('message', ['msg'=>'Enviamos um e-mail com dados de acesso ao sistema!', 'class'=>'success']);
        
        return redirect('/solicitaacesso')->with('Enviamos um e-mail com dados de acesso ao sistema!');

        //return redirect()->route('envia.login.senha', compact('id'));

        //$mail = new MailController();
        //return $mail->envialoginSenha($id);
        }











      \Session::flash('message', ['msg'=>"Pedido Realizado com sucesso. E-mail de confirmação enviado para $usuario->email!", 'class'=>'success']);
      //return redirect()->route('solicitaacesso');






    }


    public function listaPedidos()
    {
        $dataAtual = Carbon::now()->locale('pt_BR');
          
        $consulta = \App\User::where('status', '3')->get();
        //dd($consulta);
        return view('pedido.index', compact('consulta'));
    }


    public function listaCidadePorUF($id){
        //dd($id);
        $cidade = Cidade::where('uf_id', $id);
        return $cidade;
        dd($cidade);
        return Cidade::where('uf_id', $id)->first;

    }

    public function finalizarcadastro($id){
        /*
         if(!\Gate::allows('isadministrador')){
            abort(403, "Desculpa, você não tem autorização!");
        }
        */
        //dd('OK');
        date_default_timezone_set('America/Sao_Paulo');

        $id = Crypt::decrypt($id);
        $count = User::where('id', $id)->count();
        //dd($count);
        if($count == 1){
        $user = User::find($id);
        //dd($user);
        $perfis = \App\Models\Role::all();
        $oms = \App\GerenciarOm::all();
        $postos = \App\PostoGraduacao::all();
        $forcas = \App\Forca::all();
        $ufs = \App\Uf::all();
        $cidades = \App\Cidade::all();
        $situacoes = Situacao::all();

        return view('pedido.novo', compact('perfis', 'oms', 'postos', 'forcas', 'ufs', 'cidades', 'situacoes', 'user'));
        }else{
            return redirect('/');
        }
        

        //$perfis = Perfil::getByPerfilId(Auth::user()->perfil_id);
        
       
        
        //$processos = Processo::getByComissaoId($comissao_id);
       

        
    }


    public function UserCreateNew(Request $request){

        
   
        if($request->indeterminado == 1){
            $request->validade = null;
        }
   
        date_default_timezone_set('America/Sao_Paulo');

        if($request['password'] != $request['resenha']){
             return back()->withInput()->withErrors(['Senhas não são iguais!']);
        }
        
        if($request->hasFile('documento') and !$request->hasFile('documento_verso')){
              return back()->withInput()->withErrors(['Falta um Arquivo!!']);
        }
        
        if(!$request->hasFile('documento') and $request->hasFile('documento_verso')){
              return back()->withInput()->withErrors(['Falta um Arquivo!!']);
        }

        if($request->hasFile('documento') and $request->hasFile('documento_verso')){
           
            if(!$request->file('documento')->isValid() || !$request->file('documento_verso')->isValid()){
               return back()->withInput()->withErrors(['Arquivo Inválido!']);
            }

                $type = $request->documento->extension();
                $type2 = $request->documento_verso->extension();

        if($type != 'jpg' and $type != 'jpeg' and $type != 'png' and $type != 'pdf' or $type2 != 'jpg' and $type2 != 'jpeg' and $type2 != 'png' and $type2 != 'pdf'){
            return back()->withInput()->withErrors(['Formato de Arquivo Inválido!']);
        }

        }
        
        $dataForm = $request->all();
        $dataForm['pttc'] = (!isset($dataForm['pttc']))? 0 : 1;

   
        if (isset($request['cpf'])) 
            $request['cpf'] = str_replace([".","-"], "", $request['cpf']);

        if ($request['telefone'] != NULL) 
            $request['telefone'] = str_replace(["(",")"," ","-"], "", $request['telefone']);

        if ($request['idtMil'] != NULL) 
            $request['idtMil'] = str_replace("-", "", $request['idtMil']);

        $customMessages = [
            'nome.max' => 'Nome Guerra deve ter no max 100 caracteres',            
            'nome.required' => 'Campo obrigatório',

            
            'email.max' => 'E-mail deve ter no max 100 caracteres',            
            'email.required' => 'Campo obrigatório',

            'cpf.min' => 'CPF deve ter no min 10 caracteres',
            'cpf.max' => 'CPF deve ter no max 11 caracteres',            
            'cpf.required' => 'Campo obrigatório',

            'password.min' => 'Senha deve ter no min 6 caracteres',
            'password.max' => 'CPF deve ter no max 15 caracteres',            
            'password.required' => 'Campo obrigatório',
            'resenha.min' => 'Senha deve ter no min 6 caracteres',
            'resenha.max' => 'CPF deve ter no max 15 caracteres',            
            'resenha.required' => 'Campo obrigatório',

            'uf.required' => 'Campo UF obrigatório',

            'cidade.required' => 'Campo Cidade obrigatório',

            'situacao.required' => 'Campo Situação Militar obrigatório',

            'idtMil.required' => 'Campo Idt Militar obrigatório',

            'telefone.required' => 'Campo Telefone obrigatório',
            'documento_verso.max' => 'O Documento Verso precisa ter máximo MB.',
            'documento.max' => 'O Documento Frente precisa ter máximo 4MB.',

            'mesAnoFinal.required_if' =>
            'O campo Mês/Ano Final é obrigatório quando PTTC estiver marcado.',
            'mesAnoFinal.regex' =>
            'Informe o Mês/Ano Final no formato MM/AAAA.',


        ];

        $validatedData = $request->validate([
            'nome' => 'required|max:100',
            'email' => 'required|max:100',
            'cpf' => 'required|max:11',
            'idtMil' => 'required|max:15',
            'telefone' => 'required|max:11',
            'uf' => 'required',
            'cidade' => 'required',
            'situacao' => 'required',
            'pttc'  =>  'nullable',
            'siape'  =>  'nullable',
            'nivel'  =>  'nullable',
            'om'  =>  'required',
            
            'mesAnoFinal' => [
                'required_if:pttc,1',
                'nullable',
                'regex:/^(0[1-9]|1[0-2])\/[0-9]{4}$/',
            ],
            
            'mecenas' => 'nullable|boolean',
            'password' => 'required|max:15|min:6',
            'posto'  =>  'nullable',
            'resenha' => 'required|max:15|min:6',
            'documento' => 'nullable|mimes:jpeg,png,pdf|max:4000',
            'documento_verso' => 'nullable|mimes:jpeg,png,pdf|max:4000',

        ]);

        if(!$this->validarCPF($validatedData['cpf'])){
            return back()->withInput()->withErrors(['CPF inválido.']);
        }
        
        if(!$this->verificarCPFCadastrado($validatedData['cpf'], null, true)){
            return back()->withInput()->withErrors(['Este CPF já está cadastrado no sistema.']);
        }
        
        
        if(!$this->verificarEmailCadastrado($validatedData['email'], $validatedData['email'])){
            return back()->withInput()->withErrors(['Este E-mail já está cadastrado no sistema.']);
        }
        if($request['password'] != $request['resenha']){
            return back()->withInput()->withErrors(['A Senha deve ser igual a Re-Senha']);
        }     
         
        $usuario = Auth::user();
        $usuario->name = strtoupper($validatedData['nome']);
        $usuario->email = $validatedData['email'];
        $usuario->cpf = $validatedData['cpf'];
        $usuario->idtMil = $validatedData['idtMil'];
        $usuario->telefone = $validatedData['telefone'];
        $usuario->uf_id = $request['uf'];
        $usuario->cidade_id = $validatedData['cidade'];
        $usuario->situacao_id = $validatedData['situacao'];
        $usuario->dtUltPromo = $request['dtUltPromo'];
        $usuario->forca_id = $request['forca'];
        $usuario->om_id = $request['om'];
        $usuario->perfil_id = 5;
        $usuario->postograd_id = $validatedData['posto'];
        $usuario->siape = $validatedData['siape'];
        $usuario->status = 3;
        $usuario->validade = $request->validade;
        $usuario->mecenas = $request->mecenas ? 1 : 0;
        $usuario->indeterminado = (!isset($request->indeterminado))? 0 : 1;
         $usuario->pttc = $request->has('pttc') ? 1 : 0;
        if (
            (int) $validatedData['situacao'] === 2 &&
            $request->has('pttc')
        ) {
            $usuario->mesAnoFinal = $validatedData['mesAnoFinal'];
        } else {
            $usuario->mesAnoFinal = null;
        }


       
        


        if($request['password'] === $request['resenha']){
            $usuario->password = Hash::make($request['password']);
        }
        
        if($request->hasFile('documento') || $request->hasFile('documento_verso')){

//

    





    
           
        }
        
        
        try {
    /*
     * Atualiza os dados do usuário.
     */
    if (!$usuario->update()) {
        \Session::flash('message', [
            'msg' => 'Ocorreu um erro ao salvar os dados.',
            'class' => 'danger',
        ]);

        return redirect()->back()->withInput();
    }

    /*
     * Instancia o serviço caso exista frente ou verso.
     */
    if (
        $request->hasFile('documento') ||
        $request->hasFile('documento_verso')
    ) {
        $documentoService = new DocumentoService();

        if ($request->hasFile('documento')) {
            $documentoService->salvarFrente(
                $usuario,
                $request->file('documento')
            );
        }

        if ($request->hasFile('documento_verso')) {
            $documentoService->salvarVerso(
                $usuario,
                $request->file('documento_verso')
            );
        }
    }

    /*
     * Define o perfil do usuário.
     */
    $usuario->syncRoles(['5']);

    \Session::flash('message', [
        'msg' => 'Aguarde o recebimento do e-mail de confirmação para acessar o sistema completo!',
        'class' => 'success',
    ]);

    return redirect()->route('usuario.home');

} catch (\Throwable $erro) {
    /*
     * Registra os detalhes em storage/logs/laravel.log.
     */
    \Illuminate\Support\Facades\Log::error(
        'Erro ao atualizar cadastro ou salvar documentos.',
        [
            'usuario_id' => $usuario->id ?? null,
            'erro' => $erro->getMessage(),
            'arquivo' => $erro->getFile(),
            'linha' => $erro->getLine(),
        ]
    );

    \Session::flash('message', [
        'msg' => 'Ocorreu um erro ao salvar os dados. Tente novamente.',
        'class' => 'danger',
    ]);

    return redirect()->back()->withInput();
}
       
        
        

        

       
        
    }


    public function atualizaDadosUsuario(Request $request){

        //dd($request->all());
        if($request->indeterminado == 1){
            $request->validade = null;
        }
        date_default_timezone_set('America/Sao_Paulo');
        if($request->hasFile('documento') and !$request->hasFile('documento_verso')){
              return back()->withInput()->withErrors(['Falta um Arquivo!!']);
        }
        if(!$request->hasFile('documento') and $request->hasFile('documento_verso')){
              return back()->withInput()->withErrors(['Falta um Arquivo!!']);
        }
        if($request->hasFile('documento') and $request->hasFile('documento_verso')){
           
        if(!$request->file('documento')->isValid() || !$request->file('documento_verso')->isValid()){
               return back()->withInput()->withErrors(['Arquivo Inválido!']);
        }
        $type = $request->documento->extension();
        $type2 = $request->documento_verso->extension();
        if($type != 'jpg' and $type != 'jpeg' and $type != 'png' and $type != 'pdf' or $type2 != 'jpg' and $type2 != 'jpeg' and $type2 != 'png' and $type2 != 'pdf'){
            return back()->withInput()->withErrors(['Formato de Arquivo Inválido!']);
        }
        }
        
        $dataForm = $request->all();
        $dataForm['pttc'] = (!isset($dataForm['pttc']))? 0 : 1;

        if (isset($request['cpf'])) 
            $request['cpf'] = str_replace([".","-"], "", $request['cpf']);

        if ($request['telefone'] != NULL) 
            $request['telefone'] = str_replace(["(",")"," ","-"], "", $request['telefone']);

        if ($request['idtMil'] != NULL) 
            $request['idtMil'] = str_replace([".","-"], "", $request['idtMil']);

        $customMessages = [
            'nome.max' => 'Nome deve ter no max 100 caracteres',            
            'nome.required' => 'Campo obrigatório',
            'email.max' => 'E-mail deve ter no max 100 caracteres',            
            'email.required' => 'Campo obrigatório',
            'cpf.min' => 'CPF deve ter no min 10 caracteres',
            'cpf.max' => 'CPF deve ter no max 11 caracteres',            
            'cpf.required' => 'Campo obrigatório',
            'uf.required' => 'Campo obrigatório',
            'cidade.required' => 'Campo obrigatório',
            'situacao.required' => 'Campo obrigatório',
            'perfil_id.required' => 'Campo obrigatório',
            'dtUltPromo.required' => 'Campo obrigatório',
            'idtMil.required' => 'Campo obrigatório',
            'telefone.required' => 'Campo obrigatório',         
            'documento_verso.max' => 'O Documento Verso precisa ter máximo 2mb.',
            'documento.max' => 'O Documento Frente precisa ter máximo 2mb.',
        ];

        $validatedData = $request->validate([
            'nome' => 'required|max:100',
            'email' => 'required|max:100',
            'cpf' => 'required|max:11',
            'idtMil' => 'required|max:15',
            'telefone' => 'required|max:11',
            'uf' => 'required',
            'mecenas' => 'nullable|boolean',
            'cidade' => 'required',
            'situacao' => 'required',
            'pttc'  =>  'nullable',
            'siape'  =>  'nullable',
            'perfil_id'  =>  'required',
            'nivel' => 'nullable',
            'om' => 'required',
            'documento' => 'nullable|mimes:jpeg,png,pdf|max:2000',
            'documento_verso' => 'nullable|mimes:jpeg,png,pdf|max:2000',
        ]);
        
        if(!$this->validarCPF($validatedData['cpf'])){
            return back()->withInput()->withErrors(['CPF inválido.']);
        }
        
        if(!$this->verificarCPFCadastradoAtualizaCPF($validatedData['cpf'], $request->id)){
           return back()->withInput()->withErrors(['Este CPF já está cadastrado no sistema.']);
        }
        
        if(!$this->verificarEmailCadastradoUsuarios($validatedData['email'], Crypt::decrypt($request['id']) )){
            return back()->withInput()->withErrors(['Este E-mail já está cadastrado no sistema.']);
        }
        
        $usuario =  User::findOrFail(Crypt::decrypt($request['id']));
        $usuario->name = strtoupper($validatedData['nome']);
        $usuario->email = $validatedData['email'];
        $usuario->cpf = $validatedData['cpf'];
        $usuario->idtMil = $validatedData['idtMil'];
        $usuario->telefone = $validatedData['telefone'];
        $usuario->uf_id = $validatedData['uf'];
        $usuario->cidade_id = $validatedData['cidade'];
        $usuario->situacao_id = $validatedData['situacao'];
        $usuario->pttc = (!isset($validatedData['pttc']))? 0 : 1;
        $usuario->nivel = $validatedData['nivel'];
        $usuario->postograd_id = $request['posto'];
        $usuario->siape = $validatedData['siape'];
        $usuario->om_id = $validatedData['om'];
        $usuario->validade = $request->validade;
        $usuario->mecenas = $request->mecenas ? 1 : 0;
        $usuario->indeterminado = (!isset($request->indeterminado))? 0 : 1;
        if(isset($request->motivo)){
            //dd('ok');
            $usuario->motivo_id = $request->motivo;
        }else{
            $usuario->motivo_id =  null;
        }
        if(isset($type) and isset(($type2))){
        //$usuario->tipo_doc = $type;
        //$usuario->tipo_doc_verso = $type2;
        }
        $usuario->dtUltPromo = $request['dtUltPromo'];

        if($validatedData['perfil_id'] == 5){  
            $usuario->status = 5;
        }

        $usuario->perfil_id = $validatedData['perfil_id'];
        
        if($request->hasFile('documento') || $request->hasFile('documento_verso')){
           


      
        /*
            $file_documento = $request->file('documento');
            $file_documento_verso = $request->file('documento_verso');

            $contents = $file_documento->openFile()->fread($file_documento->getSize());
            $contents = base64_encode($contents);  
            
            $contents_documento_verso = $file_documento_verso->openFile()->fread($file_documento_verso->getSize());
            $contents_documento_verso = base64_encode($contents_documento_verso);  
           
            $usuario->documento = $contents;
            $usuario->documento_verso = $contents_documento_verso; 
        */
        }
        
        if($usuario->update()){
            if($request->hasFile('documento')) {
        $documentoService = new DocumentoService();
        $documentoService->salvarFrente($usuario, $request->file('documento'));
    }

    if($request->hasFile('documento_verso')) {
        $documentoService->salvarVerso($usuario, $request->file('documento_verso'));
    }
             \Session::flash('message', ['msg'=>'Dados pessoais alterados com sucesso.', 'class'=>'success']);
             $usuario->syncRoles([$validatedData['perfil_id']]);

             return redirect()->back();
        }else{
             \Session::flash('message', ['msg'=>'Ocorreu um erro ao salvar os dados.', 'class'=>'danger']);
             return redirect()->back();
        }
        
        
        
    }

    public function homeUsuario()
    {    
        return view('pedido.home');
    }
public function updatePassword(Request $request)
{

        //dd($request->all());
        # Validation
        $request->validate([
            'password' => 'required',
            'password_confirmation' => 'required',
        ]);

        /*
        #Match The Old Password
        if(!Hash::check($request->password_confirmation, auth()->user()->password)){
            return back()->with("error", "Old Password Doesn't match!");
        }
        */

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->update();
        //dd($user);
        /*
        #Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->password)
        ]);
        */
        return redirect()->route('login')->with("status", "Senha alterada com sucesso!");

        //return back()->with("status", "Senha alterada com sucesso!");
}
    public function updatePasswordInativo(){
        
        dd('rota');

        return redirect()->route('login')->with("erro", "Favor Contactar a Administração!");

    }


}
