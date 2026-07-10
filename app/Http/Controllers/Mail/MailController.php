<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Crypt;
use \App\Pedido;
use Carbon\Carbon;


class MailController extends Controller
{
    public function enviaMailOk($id)
    { 
    	
    	
    	$id = Crypt::decrypt($id);
    	//dd($id);

    	//dd($request);
    	$user = \App\Pedido::find($id);
    	//dd($user);
        $dataAtual = Carbon::now()->locale('pt_BR');
        $user->updated_at = $dataAtual;
        $user->update();

    	return new \App\Mail\newLaravelTips($user);

    	//\Illuminate\Support\Facades\Mail::queue(new \App\Mail\newLaravelTips($user));
    }

    public function EnviaCadastroNovo($id)
    { 
        
        
        $id = Crypt::decrypt($id);
        //dd($id);
        //dd($request);
        $user = \App\User::find($id);
        //dd($user);
        $dataAtual = Carbon::now()->locale('pt_BR');
        $user->updated_at = $dataAtual;
        $user->status = 1;
        $user->syncRoles(['4']);
        $user->update();

        return new \App\Mail\MailNovoCadastro($user);

        //\Illuminate\Support\Facades\Mail::queue(new \App\Mail\liberaAcesso($user));
        return redirect()->route('lista.pedidos');
    }


    public function testeEmail()
    { 
        
        //dd('ok');
        //$id = Crypt::decrypt($id);
        //dd($id);
        //dd($request);
        //$user = \App\User::find($id);
        //dd($user);
        $dataAtual = Carbon::now()->locale('pt_BR');
        
        //return new \App\Mail\MailTeste();

        \Illuminate\Support\Facades\Mail::queue(new \App\Mail\MailTeste());
        //return redirect()->route('lista.pedidos');
    }

    public function liberaAcesso($id)
    { 
        
        
        $id = Crypt::decrypt($id);
        $user = \App\User::find($id);
        $dataAtual = Carbon::now()->locale('pt_BR');
        $user->updated_at = $dataAtual;
        $user->status = 1;
        $user->perfil_id = 4;        
        $user->syncRoles(['4']);
        $user->update();

        \Illuminate\Support\Facades\Mail::queue(new \App\Mail\liberaAcesso($user));
        return redirect()->route('lista.pedidos');
    }

    public function envialoginSenha($id)
    { 
        
        
        $id = Crypt::decrypt($id);
        //dd($id);
        //dd($request);
        $user = \App\User::find($id);
        //dd($user);
        $dataAtual = Carbon::now()->locale('pt_BR');
        $user->updated_at = $dataAtual;

        //$user->update();

        //return new \App\Mail\newLaravelTips($user);

        \Illuminate\Support\Facades\Mail::queue(new \App\Mail\newLaravelTips($user));

        \Session::flash('message', ['msg'=>'Enviamos um e-mail com dados de acesso ao sistema!', 'class'=>'success']);
        return redirect()->route('solicitaacesso');
    }


    public function confirmacaoHospedagem($id)
    { 
        
        //dd($id);
        $id = Crypt::decrypt($id);
        dd($id);
        //dd($request);
        $user = \App\User::find($id);
        //dd($user);
        $dataAtual = Carbon::now()->locale('pt_BR');
        $user->updated_at = $dataAtual;

        //$user->update();

        //return new \App\Mail\newLaravelTips($user);

        \Illuminate\Support\Facades\Mail::queue(new \App\Mail\newLaravelTips($user));

        \Session::flash('message', ['msg'=>'Enviamos um e-mail com dados de acesso ao sistema!', 'class'=>'success']);
        return redirect()->route('solicitaacesso');
    }

    public function enviaMailNegado(Request $request)
    { 
        
     	$user = \App\User::find($request->id);
        $dataAtual = Carbon::now()->locale('pt_BR');
        $user->updated_at = $dataAtual;
        $user->motivo = $request->motivo;
        $user->status = 6;
        $user->syncRoles(['5']);
        $user->update();
     	\Illuminate\Support\Facades\Mail::queue(new \App\Mail\MailNegado($user));
        \Session::flash('message', ['msg'=>'Pedido Cancelado com sucesso!', 'class'=>'success']);
        return redirect()->back();
    }

    public function finalizarcadastro($id)
    {
    	$id = Crypt::decrypt($id);
    	//dd($id);
    	$count = Pedido::where('id', $id)->count();
    	//dd($user);
    	if($count == 1){
    		$user = Pedido::where('id', $id);
	}






    }
}
