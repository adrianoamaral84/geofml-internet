<?php

namespace App\Http\Controllers\TipoUndHab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Crypt;
use \App\TipoUndHab;

class TipoUndHabController extends Controller
{
  public function __construct()
  {
        $this->middleware('auth');
  }

    public function index(){
   	$consulta = \App\TipoUndHab::paginate(20);
   	return view('tipoundhab.index', compact('consulta'));
    }

   public function edit($id){
    
    $id = Crypt::decrypt($id);
   	$consulta = \App\TipoUndHab::find($id);
   	return view('tipoundhab.edit', compact('consulta'));
   }

   public function delete($id){
    
    
    $id = Crypt::decrypt($id);
   	$consulta = \App\TipoUndHab::find($id);
   	$consulta->delete();
   	\Session::flash('message', ['msg'=>"Deletado com sucesso!", 'class'=>'success']);
    return redirect()->route('habitacao.index');
   	
   }

   public function create(){
    
    return view('tipoundhab.create');
   }

   public function store(Request $request){
    
    
   		$customMessages = [
            'nome.min' => 'Descrição deve ter no min 2 caracteres',
            'nome.max' => 'Descrição deve ter no max 50 caracteres',            
            'nome.required' => 'Campo obrigatório', 
        ];

        $validatedData = [
            'nome' => 'required|max:50|min:2',
        ];
        $validatedData = $request->validate($validatedData, $customMessages);
        $consulta1 = \App\TipoUndHab::where('descricao', $request->nome)->count();
        if($consulta1 > 0){
        	\Session::flash('message', ['msg'=>"Já existe uma unidade com esse Nome!", 'class'=>'danger']);
        	return redirect()->back();
        }
        $consulta = new TipoUndHab();
   		$consulta->descricao = $validatedData['nome'];
   		$consulta->save();
   		\Session::flash('message', ['msg'=>"Cadastrado com sucesso!", 'class'=>'success']);
    	return redirect()->route('habitacao.index');
   }

   public function update(Request $request){
    
    //dd($request->all());
    //$id = Crypt::decrypt($id);
   	
   	//dd($consulta);
   	$customMessages = [
            'nome.min' => 'Descrição deve ter no min 2 caracteres',
            'nome.max' => 'Descrição deve ter no max 50 caracteres',            
            'nome.required' => 'Campo obrigatório', 
        ];

        $validatedData = [
            'nome' => 'required|max:50|min:2',
        ];
        $validatedData = $request->validate($validatedData, $customMessages);
        $consulta1 = \App\TipoUndHab::where('descricao', $request->nome)
        ->where('id', '<>', $request->id)
        ->count();
        if($consulta1 > 0){
        	\Session::flash('message', ['msg'=>"Já existe uma unidade com esse Nome!", 'class'=>'danger']);
        	return redirect()->back();
        }
        $consulta = \App\TipoUndHab::find($request->id);
   		$consulta->descricao = $validatedData['nome'];
   		$consulta->update();
   		\Session::flash('message', ['msg'=>"Atualizado com sucesso!", 'class'=>'success']);
   		return redirect()->route('habitacao.index');
   }
}
