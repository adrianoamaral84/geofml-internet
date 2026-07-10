<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Rules\ReCaptcha;
use App\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    
     protected function credentials(Request $request)
    {   
      

        if (isset($request['cpf'])) 
            $request['cpf'] = str_replace([".","-"], "", $request['cpf']);     
        //dd($request);

        return ['cpf' => $request->cpf, 'password' => $request->password, 'status' => [1,3,5,6]];
    }
    

    protected function authenticated(Request $request, $user)
    {

        
        //dd($user);
        $hoje = date("Y-m-d");
        if($user->indeterminado != 1){
            if(strtotime($user->validade) < strtotime($hoje)){
            \Session::flash('message', ['msg'=>"Seu documento de identidade está com a data de validade vencida! Favor atualizar o documento para prosseguir", 'class'=>'danger']);
        }    
        }

        if($user->hasRole('hospede')){
             return redirect('/hospede');
        }

        if($user->hasRole('precadastro')){
             return redirect('/precadastro');
        }


            Session::flush();
            Auth::logout();
      
            return redirect('login');

    
    }


    protected function validateLogin(Request $request)
{
    $request->validate([
        $this->username() => 'required|string',
        'password' => 'required|string',
       // 'g-recaptcha-response' => ['required', new \App\Rules\ReCaptcha]
        'g-recaptcha-response' => ['nullable']
    ]);
}


}
