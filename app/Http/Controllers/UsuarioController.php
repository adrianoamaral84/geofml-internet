<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use Route;
use \App\User;
use \App\Models\Role;
use Crypt;
use Config;
use Laratrust;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Response;
use App\UserDocumento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\DocumentoService;

class UsuarioController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        /*
        if(!Auth::user()->hasRole('administrador_geral')){
            abort(403, "Desculpa, você não tem autorização!");
        }
        */

       
        $menuAtivo = "usuarios";
        $user = Auth::user();
        if(Laratrust::hasRole('administrador_especial')) {
            
            $consulta = User::where('status', 1)
            ->paginate(50);          
            $search = '';
            return view('usuario.consulta', compact('menuAtivo', 'consulta'));

        }elseif (Laratrust::hasRole('administrador_geral')){
            //dd('adm geral');
            //$consulta = User::where('status', 1)->get();
            //$consulta = DB::table('user')
            //->where('status', 1)
            //->join('role_user', '=', '')
            //->select('name', 'status')
            //->get();
            
            $consulta = \App\User::where('status', 1)
            ->select('id', 'name', 'cpf', 'status')
            //->with('roles')

            //->with(['roles' => function ($query) {
              //  $query->select('display_name');    
            //}])
            //->select('name', 'cpf', 'status', 'roles:display_name')
            //->limit(10)
            ->get();
            //dd($consulta);
            //dd(json_encode($consulta));
            //$consulta = User::whereRoleIs('administrador_geral')->get();
            //dd('Adm geral');
            //$search = '';
            return view('usuario.consulta', compact('consulta'));

        }elseif (Laratrust::hasRole('atendente')){
            abort(403, "Desculpa, você não tem autorização!");
            $consulta = User::whereRoleIs('atendente')->paginate(20);
            //dd($consulta);
            return view('usuario.consulta', compact('menuAtivo', 'consulta', 'search'));
        
        }elseif (Laratrust::hasRole('hospede')){
            
            abort(403, "Desculpa, você não tem autorização!");
            $consulta = User::whereRoleIs('hospede')->get();
            return view('usuario.consulta', compact('menuAtivo', 'consulta', 'search'));
        
        }elseif (Laratrust::hasRole('auxiliar_administrador_geral')){
            
            //abort(403, "Desculpa, você não tem autorização!");
            $consulta = \App\User::where('status', 1)
            ->select('id', 'name', 'cpf', 'status')
            ->get();
            return view('usuario.consulta', compact('menuAtivo', 'consulta'));
        
        }elseif (Laratrust::hasRole('administrador')){
            

            $consulta = \App\User::where('status', 1)
            ->select('id', 'name', 'cpf', 'status')
            ->get();
            return view('usuario.consulta', compact('menuAtivo', 'consulta'));
        
        }else{
            abort(403, "Desculpa, você não tem autorização!");
        }
        
        //return view('usuario.consulta', compact('menuAtivo', 'consulta', 'search'));
        }



    public function showFormNewUser(){
        /*
         if(!\Gate::allows('isadministrador')){
            abort(403, "Desculpa, você não tem autorização!");
        }
        */

        $perfis = \App\Models\Role::all()->except(5)->sortBy('ordem');
        $oms = \App\GerenciarOm::all();
        $postos = \App\PostoGraduacao::all();
        $forcas = \App\Forca::all();
        $ufs = \App\Uf::all();
        $cidades = \App\Cidade::all();
        $situacoes = \App\Situacao::all();
        $nivels = \App\Nivel::all();
        //$perfis = Perfil::getByPerfilId(Auth::user()->perfil_id);
        //dd($postos);
        if (Auth::user()->perfil_id == 2){
            //$comissao_id = Comissao::getIdByPresidenteId(Auth::id());
        } else {
            //$comissao_id = Comissao::getIdByAuxiliarId(Auth::id());
        }
        
        //$processos = Processo::getByComissaoId($comissao_id);
        $menuAtivo = "usuarios";
        return view('usuario.novo', compact('menuAtivo', 'perfis', 'oms', 'postos', 'forcas', 'ufs', 'cidades', 'situacoes', 'nivels'));
    }



    public function changeStatus($id){
        $usuario = User::find($id);
        
        if ($usuario->status == 1) {
            //$this->saveLog(Config::get('constants.msgLog.usuarioInativado'), Route::currentRouteName(), null, null, null);
            $acao = "Usuário inativado ";
        } else {
            //$this->saveLog(Config::get('constants.msgLog.usuarioAtivado'), Route::currentRouteName(), null, null, null);
            $acao = "Usuário ativado ";
        }
        
        User::changeStatus($id);
        
        \Session::flash('message', ['msg'=>"$acao com sucesso.", 'class'=>'success']);
        return redirect()->route('user.index');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfile($id = null) {
        /*
        if(!Auth::user()->hasRole('administrador_geral')){
            abort(403, "Desculpa, você não tem autorização!");
        }
        */
        $menuAtivo = "";
        if($id)
        {
        $id = Crypt::decrypt($id);
        $user = \App\User::find($id);
        //dd($usuario);   
        }else{
        $user = Auth::user();
        }
        //$perfis = \App\PerfilAcesso::all();
        $perfis = \App\Models\Role::all()->sortBy('ordem');
        $oms = \App\GerenciarOm::all();
        $postos = \App\PostoGraduacao::all();
        $ufs = \App\Uf::all();
        $cidades = \App\Cidade::all();
        $situacoes = \App\Situacao::all();
        $forcas = \App\Forca::all();
        $nivels = \App\Nivel::all();
        $hoje = date('Y-m-d');
        //$id = Crypt::encrypt($id);
        //dd($usuario);
        //dd(Auth::user());

        if($user->indeterminado == 1){
            $user->validade = null;
        }

        //$min = "2024-06-24";
        $min = date("Y-m-d");
       
        return view('usuario.profile', compact('menuAtivo', 'user', 'perfis', 'oms', 'postos', 'id', 'ufs', 'cidades', 'situacoes', 'forcas', 'nivels', 'hoje', 'min'));
    }

    public function resetSenha($id = null) {

      
    
        $menuAtivo = "";

        if($id)
        {
        $id = Crypt::decrypt($id);
        $user = \App\User::find($id);
        //dd($user);   
        }else{
        $user = Auth::user();
        }
        
        $cpf = bcrypt($user->cpf);
        //dd($cpf);
        //$usuario = Auth::user();
        $user->password = $cpf;
        $user->update();



        //dd($usuario);
        $perfis = \App\Models\Role::all();
        $oms = \App\GerenciarOm::all();
        $postos = \App\PostoGraduacao::all();
        $forcas = \App\Forca::all();
        $ufs = \App\Uf::all();
        $cidades = \App\Cidade::all();
        $situacoes = \App\Situacao::all();
        $nivels = \App\Nivel::all();

        /*
         if(!\Gate::allows('isadministrador')){
            abort(403, "Desculpa, você não tem autorização!");
        }
        */
       
        \Session::flash('message', ['msg'=>'Senha resetada para o CPF com sucesso.', 'class'=>'success']);

        return redirect()->back();
        //return view('usuario.verdados', compact('user', 'perfis', 'oms', 'postos', 'forcas', 'ufs', 'cidades', 'situacoes', 'nivels'));

        /*$perfis = \App\PerfilAcesso::all();
        $oms = \App\GerenciarOm::all();
        $postos = \App\PostoGraduacao::all();
        */
        //dd(Auth::user());

        //return view('usuario.verdados', compact('id'));
    }      

    public function editProfile(Request $request) {


        if($request->indeterminado == 1){
            $request->validade = null;
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
        //dd($type);
       if($type != 'jpg' and $type != 'jpeg' and $type != 'png' and $type != 'pdf' or $type2 != 'jpg' and $type2 != 'jpeg' and $type2 != 'png' and $type2 != 'pdf'){
            return back()->withInput()->withErrors(['Formato de Arquivo Inválido!']);
        }

        }
        
        $dataForm = $request->all();
        $dataForm['pttc'] = (!isset($dataForm['pttc']))? 0 : 1;

        //dd($dataForm['pttc']);

        if (isset($request['cpf'])) 
            $request['cpf'] = str_replace([".","-"], "", $request['cpf']);

        if ($request['telefone'] != NULL) 
            $request['telefone'] = str_replace(["(",")"," ","-"], "", $request['telefone']);

        if ($request['idtMil'] != NULL) 
            $request['idtMil'] = str_replace([".","-"], "", $request['idtMil']);

        $customMessages = [
            'nome.max' => 'Nome Guerra deve ter no max 100 caracteres',            
            'nome.required' => 'Campo obrigatório',

            
            'email.max' => 'E-mail deve ter no max 100 caracteres',            
            'email.required' => 'Campo obrigatório',

            'cpf.min' => 'CPF deve ter no min 10 caracteres',
            'cpf.max' => 'CPF deve ter no max 11 caracteres',            
            'cpf.required' => 'Campo obrigatório',

       
            'uf.required' => 'Campo obrigatório',

            'cidade.required' => 'Campo obrigatório',

            'situacao.required' => 'Campo obrigatório',

            'idtMil.required' => 'Campo obrigatório',

            'telefone.required' => 'Campo obrigatório',
            'documento_verso.max' => 'O Documento Verso precisa ter máximo 4MB.',
            'documento.max' => 'O Documento Frente precisa ter máximo 4MB.',
          

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
            'perfil_id'  =>  'nullable',
            'dtUltPromo'  =>  'nullable',
            'forca'  =>  'nullable',
            'om'  =>  'nullable',
            'mecenas' => 'nullable|boolean',
            'nivel' => 'nullable',
            'pttc' => 'nullable',
            'validade' => 'nullable',
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
        //dd($validatedData['nivel']);
        //dd($validatedData['email']);
        //dd(Auth::user()->dtUltPromo);
        $usuario =  Auth::user();
        //$campos[] = '';

        //dd($request->all());
        
        if($usuario->email != $validatedData['email']){
            $campos[] = "E-mail: ".$validatedData['email'];
        }

        if($usuario->situacao_id != $validatedData['situacao']){
            $campos[] = "Trocou a Situação";
        }

        if($usuario->postograd_id != $request['posto']){
            $campos[] = "Trocou o Posto / Graduação";
        }

        if(!empty($validatedData['dtUltPromo'])){
        if($usuario->dtUltPromo != $validatedData['dtUltPromo']){
            $campos[] = "Trocou data última promoção: ".$validatedData['dtUltPromo'];
            
        }
        }


        if(!empty($validatedData['validade'])){
        if($usuario->validade != $validatedData['validade']){
            $campos[] = "Trocou a validade da IDT: ".$validatedData['validade'];
            
        }
        }

        if($usuario->idtMil != $validatedData['idtMil']){
            $campos[] = "Alteração de Identidade: ".$validatedData['idtMil'];
        }

        if($usuario->uf_id != $validatedData['uf']){
            $campos[] = "Trocou o UF";
        }

        if(isset($validatedData['nivel'])){
        if($usuario->nivel != $validatedData['nivel']){
            $campos[] = $validatedData['nivel'];
        }
        }

        //dd($usuario->pttc);
        if(isset($validatedData['pttc'])){
            if($usuario->pttc != $validatedData['pttc']){
            $campos[] = "Trocou o PTTC";
            }
        }else{

            if($usuario->pttc == 1){

                if(!isset($validatedData['pttc'])){
                    $campos[] = "Trocou o PTTC";
                }
            }
        }
    

        if($usuario->cidade_id != $validatedData['cidade']){
            $campos[] = "Trocou a Cidade";
        }

        if($usuario->om_id != $validatedData['om']){
            $NovaAntiga =   \App\GerenciarOm::where('id', $usuario->om_id)->first();
            $NovaNova   =   \App\GerenciarOm::where('id', $validatedData['om'])->first();
            $campos[] = "OM: ".$NovaAntiga->sigla. " -> ".$NovaNova->sigla;
        }

        if($usuario->telefone != $validatedData['telefone']){
            $campos[] = "Telefone: ".$validatedData['telefone'];
            
        }

        




        








        //dd($request->all());





        //dd('stop');











        //dd($usuario);
        $usuario->name = strtoupper($validatedData['nome']);
        $usuario->email = $validatedData['email'];
        //$usuario->cpf = $validatedData['cpf'];
        $usuario->idtMil = $validatedData['idtMil'];
        $usuario->telefone = $validatedData['telefone'];
        $usuario->uf_id = $request['uf'];
        $usuario->cidade_id = $validatedData['cidade'];
        $usuario->situacao_id = $validatedData['situacao'];
        $usuario->pttc = (!isset($validatedData['pttc']))? 0 : 1;
        $usuario->dtUltPromo = $validatedData['dtUltPromo'];
        $usuario->validade = $request->validade;
        $usuario->mecenas = $request->mecenas ? 1 : 0;
        $usuario->indeterminado = (!isset($request->indeterminado))? 0 : 1;
        


        if($request->nivel){
        $usuario->nivel = $validatedData['nivel'];
        }

        if($request->status == 6){
        $usuario->status = 3;
        }
        
        $usuario->om_id = $validatedData['om'];
        $usuario->postograd_id = $request['posto'];
        $usuario->siape = $validatedData['siape'];
    

        if(isset($validatedData['pttc']) == 1 and $validatedData['situacao'] == 2){
            $usuario->dtUltPromo = $validatedData['dtUltPromo'];
        }else{
        
        }
        
        
        
        if($request->hasFile('documento') || $request->hasFile('documento_verso')){
            
            
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->update();
            /*
            $usuario->tipo_doc = $type;
            $usuario->tipo_doc_verso = $type2;
            $file_documento = $request->file('documento');
            $file_documento_verso = $request->file('documento_verso');

            $contents = $file_documento->openFile()->fread($file_documento->getSize());
            $contents = base64_encode($contents);  
            
            $contents_documento_verso = $file_documento_verso->openFile()->fread($file_documento_verso->getSize());
            $contents_documento_verso = base64_encode($contents_documento_verso);  
            */    

            //dd($usuario->documento);
            $campos[] = "Documento Frente";
            $campos[] = "Documento Verso";

           //  if($usuario->documento != $contents){
            //$campos[] = "Documento Frente";
            //}

            //if($usuario->documento_verso != $contents_documento_verso){
            //$campos[] = "Documento Verso";
            //}

            //$usuario->documento = $contents;
            //$usuario->documento_verso = $contents_documento_verso; 



        }else{
            //$campos[] = "Não Houve Alteração do documento";
        }












        if($usuario->update()){


            $documentoService = new DocumentoService();

if ($request->hasFile('documento')) {
    $documentoService->salvarFrente($usuario, $request->file('documento'));
}

if ($request->hasFile('documento_verso')) {
    $documentoService->salvarVerso($usuario, $request->file('documento_verso'));
}



            if(isset($campos)){
            
            //$mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\AtualizacaoDadosUsuario($usuario, $campos));         
            }else{
            $campos[] = "Não Houve Alteração";
            
            //$mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\AtualizacaoDadosUsuario($usuario, $campos));         
            }

            \Session::flash('message', ['msg'=>'Dados pessoais alterados com sucesso.', 'class'=>'success']);
            return redirect()->route('profile');
        }else{
             \Session::flash('message', ['msg'=>'Ocorreu um erro ao salvar os dados.', 'class'=>'danger']);
             return redirect()->back();
        }
   
       
        
        return redirect()->route('profile');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function showSenha() {
        $menuAtivo = "";
        return view('usuario.senha', compact('menuAtivo'));
    }  

    public function editarSenha(Request $request) {
        $customMessages = [
            'senhaAtual.min' => 'A senha atual deve ter no mínimo 6 caracteres',
            'senhaAtual.required' => 'Campo obrigatório',
            'novaSenha.min' => 'A nova senha deve ter no mínimo 6 caracteres',
            'novaSenha.required' => 'Campo obrigatório',
            'novaSenha_confirmation.same'  => 'A confirmação da senha não confere',
            'novaSenha_confirmation.min' => 'A confirmação da senha deve ter no mínimo 6 caracteres'
        ];

        $validatedData = $request->validate([
            'senhaAtual' => 'required|min:6|max:15',
            'novaSenha' => 'required|min:6|max:15',
            'novaSenha_confirmation' => 'required|min:6|same:novaSenha'
        ], $customMessages);

        $usuario = Auth::user();
        $senhaAtual = $validatedData['senhaAtual'];

        if (!Hash::check($senhaAtual, $usuario->password)){
            \Session::flash('message', ['msg'=>'Senha atual incorreta.', 'class'=>'warning']);
            return redirect()->route('senha');
        }

        $novaSenha = $validatedData['novaSenha'];
        $confirmacaoNovaSenha = $validatedData['novaSenha_confirmation'];

        if ($senhaAtual === $novaSenha) {
            \Session::flash('message', ['msg'=>'A nova senha deve ser diferente da senha atual.', 'class'=>'warning']);
            return redirect()->route('senha');
        }

        if ($novaSenha != $confirmacaoNovaSenha){
            \Session::flash('message', ['msg'=>'Nova senha e Confirmação da nova senha não são iguais.', 'class'=>'warning']);
            return redirect()->route('senha');
        }

        $usuario->password = bcrypt($novaSenha);
        $usuario->update();

       
        \Session::flash('message', ['msg'=>'Senha alterada com sucesso.', 'class'=>'success']);
        return redirect()->route('senha');
    }

    public function salvarUsuario(Request $request){
         
        if (isset($request['cpf'])) 
            $request['cpf'] = str_replace([".","-"], "", $request['cpf']);

        if ($request['telefone'] != NULL) 
            $request['telefone'] = str_replace(["(",")"," ","-"], "", $request['telefone']);

        if ($request['idtMil'] != NULL) 
            $request['idtMil'] = str_replace([".","-"], "", $request['idtMil']);


        $customMessages = [
            'nome.min' => 'Nome deve ter no min 5 caracteres',
            'nome.max' => 'Nome deve ter no max 100 caracteres',            
            'nome.required' => 'Campo obrigatório',

            'nomeguerra.min' => 'Nome Guerra deve ter no min 2 caracteres',
            'nomeguerra.max' => 'Nome Guerra deve ter no max 50 caracteres',            
            'nomeguerra.required' => 'Campo obrigatório',

            'email.min' => 'E-mail deve ter no min 5 caracteres',
            'email.max' => 'E-mail deve ter no max 100 caracteres',            
            'email.required' => 'Campo obrigatório',

            'cpf.min' => 'CPF deve ter no min 10 caracteres',
            'cpf.max' => 'CPF deve ter no max 11 caracteres',            
            'cpf.required' => 'Campo obrigatório',

            'perfil_id.required' => 'Campo obrigatório',

            'om.required' => 'Campo obrigatório',
            'posto.required' => 'Campo obrigatório',

            'idtMil.min' => 'Identidade Militar deve ter no min 10 caracteres',
            'idtMil.max' => 'Identidade Militar deve ter no max 11 caracteres',            
            
        ];
        $validatedData = [
            'nome' => 'required|max:100|min:5',
            'email' => 'required|email|max:100|min:5',
            'cpf' => 'required|max:11|min:10',
            'idtMil' => 'nullable|max:10|min:9',
            'secao' => 'nullable|max:45',
            'telefone' => 'nullable|max:11',

            'perfil_id' => 'required',

            'nomeguerra' => 'required|max:50|min:2',
            'om' => 'required',
            'posto' => 'required',
            

        ];
        

        //dd($request['idtMil']);

        $validatedData = $request->validate($validatedData, $customMessages);
   
        if(!$this->validarCPF($validatedData['cpf'])){
            return back()->withInput()->withErrors(['CPF inválido.']);
        }

        if(!$this->verificarCPFCadastrado($validatedData['cpf'], null, true)){
            return back()->withInput()->withErrors(['Este CPF já está cadastrado no sistema.']);
        }

        // // Validação se e-mail já está cadastrado
        if(!$this->verificarEmailCadastrado($validatedData['email'], null, true)){
            return back()->withInput()->withErrors(['Este E-mail já está cadastrado no sistema.']);
        }

        $usuario = new User();
        $usuario->nome = strtoupper($validatedData['nome']);
        $usuario->nomeguerra = $validatedData['nomeguerra'];
        $usuario->email = $validatedData['email'];
        $usuario->cpf = $validatedData['cpf'];
        $usuario->idtMil = $validatedData['idtMil'];
        $usuario->om_id = $validatedData['om'];
        $usuario->secao = $validatedData['secao'];
        $usuario->telefone = $validatedData['telefone'];
        $usuario->postograd_id = $validatedData['posto'];
        $usuario->perfil_id = $validatedData['perfil_id'];
        $usuario->password = bcrypt($usuario->cpf);
        $usuario->mecenas = $request->mecenas ? 1 : 0;
        $usuario->status = 1;
        $usuario->save();

       
        \Session::flash('message', ['msg'=>'Usuário adicionado com sucesso.', 'class'=>'success']);
        $menuAtivo = 'usuarios';
        return redirect()->route('user.index');
    }



    public function NovoUsuario(Request $request){
        
        //dd($request->all());
        if($request->indeterminado == 1){
            $request->validade = null;
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

        if($type != 'jpg' and $type != 'jpeg' and $type != 'png' or $type2 != 'jpg' and $type2 != 'jpeg' and $type2 != 'png'){
            return back()->withInput()->withErrors(['Formato de Arquivo Inválido!']);
        }

        }
        
        $dataForm = $request->all();
        $dataForm['pttc'] = (!isset($dataForm['pttc']))? 0 : 1;

        //dd($dataForm['pttc']);

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
            'cidade' => 'required',
            'situacao' => 'required',
            'pttc'  =>  'nullable',
            'siape'  =>  'nullable',
            'perfil_id'  =>  'required',
            'nivel'  =>  'nullable',
            'om' => 'required',
            'mecenas' => 'nullable|boolean',
            'documento' => 'nullable|image|mimes:jpeg,png|max:2000',
            'documento_verso' => 'nullable|image|mimes:jpeg,png|max:2000',

        ]);

        
        if(!$this->validarCPF($validatedData['cpf'])){
            return back()->withInput()->withErrors(['CPF inválido.']);
        }
        

        if(!$this->verificarCPFCadastrado($validatedData['cpf'], null, true)){
            return back()->withInput()->withErrors(['Este CPF já está cadastrado no sistema.']);
        }
        
        
        if(!$this->verificarEmailCadastrado($validatedData['email']) ){
            return back()->withInput()->withErrors(['Este E-mail já está cadastrado no sistema.']);
        }
        
        
        //dd($validatedData['perfil_id']);
        
        $usuario = new User();
        //dd($usuario);
        $usuario->name = strtoupper($validatedData['nome']);
        $usuario->email = $validatedData['email'];
        $usuario->cpf = $validatedData['cpf'];
        $usuario->idtMil = $validatedData['idtMil'];
        $usuario->telefone = $validatedData['telefone'];
        $usuario->uf_id = $request['uf'];
        $usuario->cidade_id = $validatedData['cidade'];
        $usuario->situacao_id = $validatedData['situacao'];
        $usuario->pttc = (!isset($validatedData['pttc']))? 0 : 1;
        $usuario->dtUltPromo = $request['dtUltPromo'];
        //$usuario->forca_id = $request['forca'];
        $usuario->om_id = $request['om'];
        $usuario->nivel = $request['nivel'];
        $usuario->perfil_id = $validatedData['perfil_id'];
        $usuario->validade = $request->validade;
        $usuario->indeterminado = (!isset($request->indeterminado))? 0 : 1;

        if($validatedData['perfil_id'] == 5){
        $usuario->status = 5;
        }
        //$usuario->status = 1;

        //$usuario->syncRoles([64]);

        $usuario->postograd_id = $request['posto'];
        $usuario->siape = $validatedData['siape'];
        $usuario->password = Hash::make($usuario->cpf);
        $usuario->status = 1;
        $usuario->save();
        $documentoService = new DocumentoService();

if ($request->hasFile('documento')) {
    $documentoService->salvarFrente($usuario, $request->file('documento'));
}

if ($request->hasFile('documento_verso')) {
    $documentoService->salvarVerso($usuario, $request->file('documento_verso'));
}
        //$usuario->documento = file_get_contents($request->file('documento')->getRealPath());
       if($request->hasFile('documento') || $request->hasFile('documento_verso')){

           
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->update();
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

        
             \Session::flash('message', ['msg'=>'Dados pessoais cadastrados com sucesso.', 'class'=>'success']);
             $usuario->syncRoles([$validatedData['perfil_id']]);
            // \Illuminate\Support\Facades\Mail::queue(new \App\Mail\MailNovoCadastro($usuario));
             return redirect()->route('user.index');
        }else{
             \Session::flash('message', ['msg'=>'Ocorreu um erro ao salvar os dados.', 'class'=>'danger']);
             return redirect()->back();
        }
       
   

       
        
    }

    public function showFormDetalhesUsuario($id){
        try {
            $id = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            \Session::flash('message', ['msg'=>'Um erro inesperado ocorreu. Pedimos desculpas pelo inconveniente.', 'class'=>'danger']);
            return redirect()->route('avaliacoes');
        }

        $usuario = User::getById($id);
        $processosUsuario = Processo::getByUserId($id);

        if (Auth::user()->perfil_id == 2){
            $comissao_id = Comissao::getIdByPresidenteId(Auth::id());
        } else {
            $comissao_id = Comissao::getIdByAuxiliarId(Auth::id());
        }
                
        $processos = Processo::getByComissaoId($comissao_id);
        $menuAtivo = "usuarios";
        
        return view('usuario.editar', compact('menuAtivo', 'usuario', 'processos', 'processosUsuario'));
    }

    public function editarUsuario(Request $request){

        if (isset($request['cpf'])) 
            $request['cpf'] = str_replace([".","-"], "", $request['cpf']);

        if ($request['telefone'] != NULL) 
            $request['telefone'] = str_replace(["(",")"," ","-"], "", $request['telefone']);

        if ($request['idtMil'] != NULL) 
            $request['idtMil'] = str_replace([".","-"], "", $request['idtMil']);

        $validatedData = $request->validate([
            'nome' => 'required|max:100',
            'email' => 'required|email|max:100',
            'cpf' => 'required|max:11',
            'idtMil' => 'nullable|max:15',
            'om' => 'nullable|max:100',
            'secao' => 'nullable|max:45',
            'telefone' => 'nullable|max:11',
            'mecenas' => 'nullable|boolean',
            'processo_id' => 'required'
        ]);

        $usuario = User::getById($request['usuario_id']);
        /*
        if(!$this->validarCPF($validatedData['cpf'])){
            return back()->withInput()->withErrors(['CPF inválido.']);
        }
        

        if(!$this->verificarCPFCadastrado($validatedData['cpf'], $usuario->id)){
            return back()->withInput()->withErrors(['Este CPF já está cadastrado no sistema.']);
        }
        */
        
        // // Validação se e-mail já está cadastrado
        if(!$this->verificarEmailCadastrado($validatedData['email'], $usuario->id)){
            return back()->withInput()->withErrors(['Este E-mail já está cadastrado no sistema.']);
        }

        $usuario->nome = $validatedData['nome'];
        $usuario->email = $validatedData['email'];
        //$usuario->cpf = $validatedData['cpf'];
        $usuario->idtMil = $validatedData['idtMil'];
        $usuario->om = $validatedData['om'];
        $usuario->secao = $validatedData['secao'];
        $usuario->telefone = $validatedData['telefone'];
        $usuario->mecenas = $request->mecenas ? 1 : 0;
        $usuario->update();

        $integrantesComissao = IntegrantesComissao::getByUserId($usuario->id);
        $comissao_id = $integrantesComissao->first()->comissao_id;
        foreach ($integrantesComissao as $int) {
            $int->delete();
        }

        $processos = $validatedData['processo_id'];
        foreach ($processos as $key => $value) {
            $integranteComissao = new IntegrantesComissao();
            $integranteComissao->comissao_id = $comissao_id;
            $integranteComissao->integrante_id = $usuario->id;
            $integranteComissao->processo_id = $value;
            $integranteComissao->save();
        }   

        $descricao = "Edição de usuário: [".$usuario->nome." => ".$validatedData['nome'].",[".$usuario->email." => ".$validatedData['email'].",[".$usuario->cpf." => ".$validatedData['cpf'].",[".$usuario->idtMil." => ".$validatedData['idtMil'].",[".$usuario->om." => ".$validatedData['om'].",[".$usuario->secao." => ".$validatedData['secao'].",[".$usuario->telefone." => ".$validatedData['telefone']."]";
        $this->saveLog(Config::get('constants.msgLog.usuarioAlterado'), Route::currentRouteName(), null, null, $descricao);
        \Session::flash('message', ['msg'=>'Usuário alterado com sucesso.', 'class'=>'success']);
        $menuAtivo = 'usuarios';
        return redirect()->route('usuarios', compact('menuAtivo'));
    }

    public function verDados($id){

        
        $id = Crypt::decrypt($id);
        $user = User::find($id);
        $perfis = \App\Models\Role::all()->sortBy('ordem');
        $oms = \App\GerenciarOm::all();
        $postos = \App\PostoGraduacao::all();
        $forcas = \App\Forca::all();
        $ufs = \App\Uf::all();
        $cidades = \App\Cidade::all();
        $situacoes = \App\Situacao::all();
        $roles = \App\Models\Role::all();
        $id = Crypt::encrypt($id);
        $nivels = \App\Nivel::all();
        $motivos = \App\MotivoInativo::all();
        /*
        if(!\Gate::allows('isadministrador')){
            abort(403, "Desculpa, você não tem autorização!");
        }
        */
        $hoje = date('Y-m-d');

        //dd($postos);
        //dd($user);
        if($user->indeterminado == 1){
            $user->validade = null;
        }


        return view('usuario.verdados', compact('user', 'perfis', 'oms', 'postos', 'forcas', 'ufs', 'cidades', 'situacoes', 'roles', 'id', 'nivels', 'hoje', 'motivos'));
    }

    public function verDadosAguardando($id){

        $id = Crypt::decrypt($id);
        $user = User::find($id);
        //dd($usuario);
        $perfis = \App\Models\Role::all();
        $oms = \App\GerenciarOm::all();
        $postos = \App\PostoGraduacao::all();
        $forcas = \App\Forca::all();
        $ufs = \App\Uf::all();
        $cidades = \App\Cidade::all();
        $situacoes = \App\Situacao::all();
        $roles = \App\Models\Role::all();
        $id = Crypt::encrypt($id);
        $nivels = \App\Nivel::all();
        /*
         if(!\Gate::allows('isadministrador')){
            abort(403, "Desculpa, você não tem autorização!");
        }
        */
        if($user->indeterminado == 1){
            $user->validade = null;
        }
       
        //dd($user);

        return view('usuario.verdados_aguardando', compact('user', 'perfis', 'oms', 'postos', 'forcas', 'ufs', 'cidades', 'situacoes', 'roles', 'id', 'nivels'));
    }

    public function ListaInativos() {

        $consulta = User::where('status', 2)->with('motivoinativos')->get();
        //dd($consulta->motivoinativos->motivo);
        return view('usuario.inativos', compact('consulta'));

    }
    public function usuariosNegados(){

        $dataAtual = Carbon::now()->locale('pt_BR');
        $consulta = \App\User::where('status', 6)->get();
        
        return view('pedido.pedidonegado', compact('consulta'));
    
    }
    
    public function upload(Request $request){

        if($request->hasFile('documento')){

            $file = $request->file('documento');
            $filename = $file->getClientOriginalName();
            ///$folder = uniqid() . '-' . now()->timestamp;
            $folder = "1";
            $usuario = $request->file('documento')->store('documentos/'. $folder, $filename);
            dd($usuario);
            return $folder;

        }

        return '';
    }
    public function testes(){
        dd('teste');
    }

    public function precadastroLista(){

        $consulta = \App\User::where('status', 5)
            ->select('id', 'name', 'cpf', 'status')
            ->get();

        //dd($consulta);

        return view('usuario.listaPreCadastro', compact('consulta'));

    }

    public function deleteUsuario($id){

        $id = Crypt::decrypt($id);
        $user = \App\User::findOrFail($id);
        //dd($id);
        $user->delete();
        \Session::flash('message', ['msg'=>'Deletado com sucesso!!', 'class'=>'success']);
        return redirect()->back();
        

    }


    public function documentosVencidos(){

        date_default_timezone_set('America/Sao_Paulo');
        $hoje = now()->format('Y-m-d');
        $consulta = DB::select("SELECT id, name, cpf, validade from user where validade < '$hoje' and status = '1'");
        //dd($consulta);
        return view('usuario.documentos', compact('consulta'));
        
    }

    public function enviaDoc(Request $request){




        dd($request->all()); 


    }
    public function verDocumento($id, $doc, $tipo)
{


    $id = Crypt::decrypt($id);
    $doc = Crypt::decrypt($doc);

    $type = [
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
    ];

    if (empty($doc)) {
        $doc = "png";
    }

    if ($tipo == '1' || $tipo == '2') {

        $user = \App\User::where('id', $id)->firstOrFail();

        $documentoService = new \App\Services\DocumentoService();

        if ($tipo == '1') {
            $documento = $documentoService->obterFrente($user);
        } else {
            $documento = $documentoService->obterVerso($user);
        }

        if (!$documento || empty($documento->arquivo)) {
            abort(404);
        }

        $mime = $documento->mime ?? $doc;

        if (isset($type[$mime])) {
            $contentType = $type[$mime];
        } else {
            $contentType = $type[$doc] ?? 'application/octet-stream';
        }

        $response = Response::make(base64_decode($documento->arquivo), 200);
        $response->header("Content-Type", $contentType);

        return $response;
    }

    if ($tipo == '3') {
        $comprovante = \App\Comprovante::where('id', $id)->firstOrFail();

        $response = Response::make(base64_decode($comprovante->arquivo), 200);
        $response->header("Content-Type", $type[$doc] ?? 'application/octet-stream');

        return $response;
    }

    abort(404);
}

    public function verDocumento_OLD($id, $doc, $tipo){


        $id = Crypt::decrypt($id);
        $doc = Crypt::decrypt($doc);
        $type = [
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        ];

        if(empty($doc)){
           $doc = "png";
        }
        //dd($id);
        if($tipo == '1'){
        
        $user = \App\User::where('id', $id)->first();
        $response = Response::make(base64_decode($user->documento), 200);      //set header 
        $response->header("Content-Type", $type[$doc]);
        return $response;

        }elseif($tipo == '2') {
        //dd('2');
            
        $user = \App\User::where('id', $id)->first();
        $response = Response::make(base64_decode($user->documento_verso), 200);      //set header 
        $response->header("Content-Type", $type[$doc]);
        return $response;



        }elseif($tipo == '3'){
        //dd($type[$doc]);
        $comprovante = \App\Comprovante::where('id', $id)->first();            
        $response = Response::make(base64_decode($comprovante->arquivo), 200);      //set header 
        $response->header("Content-Type", $type[$doc]);
        //$response->header("Content-disposition: attachment; filename='teste.'");
        return $response;

        }else{

            abort(404);
        }
        
        
      
       
        
        
    }
}
