<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Auth;
use App\User;
use App\Pedido;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function validarCPF($value) {
        $cpf = preg_replace('/\D/', '', $value);
        $num = array();

        for($i=0; $i<(strlen($cpf)); $i++) {
            $num[]=$cpf[$i];
        }

        if(count($num)!=11) {
            return false;
        }else {
            for($i=0; $i<10; $i++) {
                if ($num[0]==$i && $num[1]==$i && $num[2]==$i
                && $num[3]==$i && $num[4]==$i && $num[5]==$i
                && $num[6]==$i && $num[7]==$i && $num[8]==$i) {
                return false;
                break;
                }
            }
        }
         
        $j=10;
        for($i=0; $i<9; $i++) {
            $multiplica[$i] = $num[$i]*$j;
            $j--;
        }
        $soma = array_sum($multiplica);
        $resto = $soma%11;
        if($resto<2) {
            $dg=0;
        }
        else {
            $dg=11-$resto;
        }
        
        if($dg!=$num[9]){
            return false;
        }
        
        $j=11;
        for($i=0; $i<10; $i++) {
            $multiplica[$i]=$num[$i]*$j;
            $j--;
        }

        $soma = array_sum($multiplica);
        $resto = $soma%11;
        if($resto<2){
            $dg=0;
        }
        else {
            $dg=11-$resto;
        }
        if($dg!=$num[10]) {
            return false;
        }

        return true;
    }

    public function verificarCPFCadastrado($value){
        //dd($value);
        if (Auth::check()) {
            //$count = User::where('cpf', $value)->where('id', '!=', $id)->count();
            //dd(Auth::user()->cpf);

            if($value == Auth::user()->cpf){
            $count = 0;
            }else{
                $count = User::where('cpf', $value)->where('id', '!=', Auth::user()->id)->count();  
            }

            

            
            /*
            dd($count1);

            $count = User::where('cpf', $value)->where('id', '!=', Auth::user()->id)->count();
            dd($count);
            */
        } else {
            $count = User::where('cpf', $value)->count();
        }

        //dd($count);

        if ($count > 0)
            return false;

        return true;
        
    }

    public function verificarCPFCadastradoPedido($value){
        //dd($id);

        $count = Pedido::where('cpf', $value)->count();
        $nosistema = User::where('cpf', $value)->count();
        /*
        if (Auth::check()) {
            //$count = User::where('cpf', $value)->where('id', '!=', $id)->count();
            //dd($count);
            
            $count = User::where('cpf', $value)->where('id', '!=', Auth::user()->id)->count();
        } else {
            $count = User::where('cpf', $value)->count();
        }
        */
        
        if ($count > 0 or $nosistema > 0)
            return false;

        return true;
        
    }
    public function verificarEmailCadastradoPedido($value) {


        $count = Pedido::where('email', $value)->count();
        /*
        if (Auth::check()) {
            //$count = User::where('email', $value)->where('id', '!=', $id)->count();
            
            $count = Pedido::where('email', $value)->where('id', '!=', Auth::user()->id)->count();
        } else {
            $count = Pedido::where('email', $value)->count();
        }
        */

        if ($count > 0)
            return false;

        return true;
    }

    public function verificarEmailCadastrado($value) {
        
        if (Auth::check()) {
            
            //$count = User::where('email', $value)->where('id', '!=', $id)->count();   
            //$count = User::where('email', $value)->where('email', Auth::user()->email)->count();
            $count = User::where('email', $value)->where('id', '!=', Auth::user()->id)->count();
            //dd($count1);
            
        } else {
            //dd('l');
            $count = User::where('email', $value)->count();
        }
        //dd($count);
        if ($count > 0)
            return false;

        return true;
    }

    public function verificarEmailCadastradoUsuarios($value, $id) {
        //dd($id);
        //$pega = User::where('id', '!=', $id)->where('email', $value)->count();
        //dd($pega);

        if (Auth::check()) { 
            //$count = User::where('email', $value)->where('id', '!=', $id)->count();   
            //$count = User::where('email', $value)->where('email', Auth::user()->email)->count();
            //$count = User::where('email', $value)->where('id', '!=', Auth::user()->id)->count();
            $count = User::where('id', '!=', $id)->where('email', $value)->count();
            //dd($count);
            
        } else {
            //dd('l');
            $count = User::where('email', $value)->count();
        }
        //dd($count);
        if ($count > 0)
            return false;

        return true;
    }
    public function verificarRGCadastrado($value) {
        if (Auth::check()) {
            $count = User::where('rg', $value)->where('id', '!=', Auth::user()->id)->count();
        } else {
            $count = User::where('rg', $value)->count();
        }

        if ($count > 0)
            return false;

        return true;
    }
}
