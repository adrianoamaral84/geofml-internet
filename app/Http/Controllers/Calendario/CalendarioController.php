<?php

namespace App\Http\Controllers\Calendario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\DB;



class CalendarioController extends Controller
{
    //


    public function index($id){

    	$mes = date("Y-m-d");
    	$hospedagens = \App\Hospede::with('usuario')->get();
    	return view('calendario.cale', compact('hospedagens', 'mes'));
    	

    }

    public function calendarioMes($mes){
    	
    	
    	
    	if($mes == 10 or $mes == 11 or $mes == 12){
  
    	}else{
    		$mes = "0$mes";	
    	}
    	    
    	$hospedagens = \App\Hospede::whereIn(DB::RAW('month(data_inicio)'), [''.$mes.''])->orderBy('data_inicio', 'asc')->get();
    	$mes = date("Y-$mes-d");
        $unidades_habitacionais = \App\UnidadeHabitacional::where('disponivel', 1)
        //->where('tipo_und_hab_id', $hospedagem->und_habitacionais_id)
        ->get();
    	return view('calendario.cale', compact('hospedagens', 'mes', 'unidades_habitacionais'));    	

    }


    public function calendarioUnidade($unidade){

        //dd($unidade);
    	$mesAtual = date('m');
    	if($mesAtual == 12){
            $proximoMes = "01";    
        }else{

            $proximoMes = $mesAtual + 1;
                if($proximoMes <= 9){
                    $proximoMes = '0' . $proximoMes;
                }
            
        }

     
        if($mesAtual == 01 or $mesAtual == 1){
            $mesAnterior = "12";  
            
        }else{

            $mesAnterior = $mesAtual - 1;
                if($mesAnterior <= 9){
                    $mesAnterior = '0' . $mesAnterior;
                }
            
        }

        //dd($proximoMes);

    	$hospedagens = \App\Hospede::with('usuario')->with('tipouh')
    	->where('und_habitacionais_id', $unidade)
    	->whereIn(DB::RAW('month(data_inicio)'), [$mesAtual,$proximoMes])
        ->whereIn('status', [2,3,4,5])
    	->orderBy('data_inicio', 'asc')
    	->get();

        $mes = date("Y-m-d");
    	$unidades_habitacionais = \App\UnidadeHabitacional::where('disponivel', 1)
        ->get();
        
        //dd($hospedagens);

    	return view('calendario.cale', compact('hospedagens', 'mes', 'unidades_habitacionais'));    	

    }

    public function calendarioporUnidade($unidade){


        $mesAtual = date('m');
        if($mesAtual == 12){
            $proximoMes = "01";    
        }else{

            $proximoMes = $mesAtual + 1;
                if($proximoMes <= 9){
                    $proximoMes = '0' . $proximoMes;
                }
            
        }

     
        if($mesAtual == 01 or $mesAtual == 1){
            $mesAnterior = "12";  
            
        }else{

            $mesAnterior = $mesAtual - 1;
                if($mesAnterior <= 9){
                    $mesAnterior = '0' . $mesAnterior;
                }
            
        }

        //dd($proximoMes);

        $hospedagens = \App\Hospede::with('usuario')
        ->where('und_habitacionais_id', $unidade)
        ->whereIn(DB::RAW('month(data_inicio)'), [$mesAtual,$proximoMes])
        ->where('status', 0)
        ->orderBy('data_inicio', 'asc')
        ->get();
    
        $mes = date("Y-m-d");
        $unidades_habitacionais = \App\UnidadeHabitacional::where('disponivel', 1)
        ->get();
        return view('calendario.cale', compact('hospedagens', 'mes', 'unidades_habitacionais'));        

    }





public function calendarioUnidadeJson($unidade){


    	$mesAtual = date('m');
    	if($mesAtual == 12){
        $proximoMes = "01";    
        }else{

            $proximoMes = $mesAtual + 1;
                if($proximoMes <= 9){
                    $proximoMes = '0' . $proximoMes;
                }
            
        }

     
        if($mesAtual == 01 or $mesAtual == 1){
            $mesAnterior = "12";  
            
        }else{

            $mesAnterior = $mesAtual - 1;
                if($mesAnterior <= 9){
                    $mesAnterior = '0' . $mesAnterior;
                }
            
        }

        //dd($proximoMes);

    	$hospedagens = \App\Hospede::with('usuario')
    	->where('und_habitacionais_id', $unidade)
    	->whereIn(DB::RAW('month(data_inicio)'), [$mesAtual,$proximoMes])
    	//->where()
    	->orderBy('data_inicio', 'asc')
    	->get();
    	//dd($hospedagens);
    	return json_encode($hospedagens);

    	$mes = date("Y-m-d");
    	$unidades_habitacionais = \App\UnidadeHabitacional::where('disponivel', 1)
        //->where('tipo_und_hab_id', $hospedagem->und_habitacionais_id)
        ->get();
    	

    	//return view('calendario.cale', compact('hospedagens', 'mes', 'unidades_habitacionais'));    	

    }


}