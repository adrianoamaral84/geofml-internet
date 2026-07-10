<?php

namespace App\Http\Controllers\Hospede;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Crypt;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Mail\MailController;
use Response;

class HospedeController extends Controller
{
    

    public function __construct()
    {
        $this->middleware('auth');
    }
    


    
    public function checkin($id){

    

    date_default_timezone_set('America/Sao_Paulo');
    $id = Crypt::decrypt($id);
    $hospedagem = \App\Hospede::findOrFail($id);


    if($hospedagem->checkin == 1){
        \Session::flash('message', ['msg'=>"Hospedagem não está Liberada! Realizar o Chek-Out para liberar!", 'class'=>'danger']);
        return redirect()->route('checkIn');

    }

    
    $hospedagem->checkin = 1;
    $hospedagem->checkin_at = date("Y-m-d H:i:s");

    







    $hospedagem->update();
    \Session::flash('message', ['msg'=>"Check-In realizado com sucesso!", 'class'=>'success']);
    return redirect()->route('checkIn');
    //return redirect()->back();
    //dd($hospedagem); 
    
    }

    public function checkout($id, $hospede){


    date_default_timezone_set('America/Sao_Paulo');
    $id = Crypt::decrypt($id);
    $hospede = Crypt::decrypt($hospede);
    //dd($hospede);
    

    // Verifica Pendencias
    $carrinhoCount = \App\CheckOut::where('hospedagem_id', $id)
    ->where('usuario_id', $hospede)
    ->count();
    
    if($carrinhoCount > 0){
        //\Session::flash('message', ['msg'=>"Existe Produtos no carrinho!", 'class'=>'danger']);
        //return redirect()->back();
    }

    //dd($carrinhoCount);
    
    $hora = date("Y-m-d H:i:s");
    $hospedagem = \App\Hospede::findOrFail($id);
    //dd($hospedagem);
    $hospedagem->checkin = 2;
    $hospedagem->checkout_user_id = Auth::user()->id;
    $hospedagem->checkout_at = $hora;
    $hospedagem->update();

    \Session::flash('message', ['msg'=>"Check-Out realizado com sucesso!", 'class'=>'success']);
    return redirect()->route('checkOut');
    //return redirect()->back();
    //dd($hospedagem); 
    
    }
    
    public function index(){
         

       // dd('INDEX CADASTRA PEDIDO');

        date_default_timezone_set('America/Sao_Paulo');
        $usuarioAutenticado = Auth::user();
        $hoje = date("Y-m-d");
        if($usuarioAutenticado->indeterminado != 1){

            if(isset($usuarioAutenticado->validade)) {
                if(strtotime($usuarioAutenticado->validade) < strtotime($hoje)){
                \Session::flash('message', ['msg'=>"Seu documento de identidade está com a data de validade vencida! Favor atualizar o documento para prosseguir", 'class'=>'danger']);
                        return redirect()->route('home');
                }
            }        

        }
        
        /*
        foreach ($grupoTarifa as $key => $value) {
            foreach ($value->postos as $posto) {
               // echo $posto->pivot->grupotarifa_id;
                if($posto->id === Auth::user()->postograd_id){
                    

                    $tarifa = \App\Tarifas::where('tipoundhab_id', $request->tipo)
                    ->where('grupo_destinacao_id', $posto->pivot->grupotarifa_id)
                    ->first();

                    
                }
            }
            
        }
        */

        // Tipo de Unidade por Posto de Graduação
        $tipos = \App\TipoUndHab::all();
        $GrupoTarifa = \App\GrupoTarifa::with('grupo_tarifa_posto_graduacao');
        $GrupoTarifaPostoGraduacao = \App\GrupoTarifaPostoGraduacao::where('posto_id', $usuarioAutenticado->postograd_id)->orderBy("grupotarifa_id", "DESC")->get();


        $users = \App\GrupoTarifa::with(['tipoundhabitacao' => function ($query) {
        $query->orderBy('tipoundhab.id', 'DESC');
        }])
        ->get();
       
        if(sizeof($GrupoTarifaPostoGraduacao) == 0){

            \Session::flash('message', ['msg'=>"Grupo de Tarifa não cadastrado! Contacte o administrador!", 'class'=>'danger']);
            return redirect()->back();
        }
        
        foreach ($GrupoTarifaPostoGraduacao as $key => $value) {
           
        $tipoHab = \App\GrupoTarifa::where('id', $value->grupotarifa_id)->with('tipoundhabitacao')->get();
        $te[] = $value;
        foreach ($tipoHab as $keys => $values) {
            $unidades[] = (
            $values->tipoundhabitacao
            );  

            $unidadess[] = ["id" => $values->tipoundhabitacao->id, "value" => $values->tipoundhabitacao->descricao];

        }
        }
        // Fim do Tipo de Unidade por Posto de Graduação
        //dd($unidadess);
        uasort($unidadess, function ($a, $b) {
        return strcmp($a['value'], $b['value']);
        //Se quiser inverter a ordem basta trocar por return strcmp($b['nome'], $a['nome']);
        });
        //$unidadess = json_encode($unidadess);
        //dd($unidadess);

        //print_r($unidadess);


        //dd('fim');

        /*
        asort($unidadess, 'value');
        dd($unidadess);

        foreach ($unidadess as $chave => $valor) {
        $pos[] = $valor;
        
        }
        */

        //dd($pos);


        $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
        $data_inicio = $altaTemporada->data_inicio;
        $data_termino = $altaTemporada->data_termino;
        $diaBloqueado = \App\BloqueioDia::where('id', 1)->first();
     

      
        $mesAtual = date("m");
        //$mesAtual = "08";
        $ano = date("Y");

       
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


        $ultimo_dia = date("t", mktime(0,0,0,$proximoMes,'01',$ano));
        $ultimoDiadoProximoMes = date("Y-" . "$proximoMes". "-t");
      

        $today = Carbon::now();
        $hoje = $today->format('d');
        

        if($today >= $data_inicio && $today <= $data_termino){
            $is_altaTemporada = true;
        }

        $diaBloqueado = \App\BloqueioDia::where('id', 1)->first(); 

        //$datetime = new Carbon('2023-12-11 14:53:20');
        //$hoje = $datetime->format('d');
        //dd($datetime);

        


       // dd($mesAtual);

        // MES 12
        $a = [];
        if($mesAtual == 12){
        
            if($hoje <= $diaBloqueado->dia){               
               // dd('ok');
                $minDate = Carbon::today()->addMonths(1)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
                
    
            }

            if($hoje > $diaBloqueado->dia && $hoje <= 31){
                //dd('ok1');
                $minDate = Carbon::today()->addMonths(2)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);


            }  
        
        }
        //dd($diaBloqueado->dia); 
        //dd($minDate);
       
        //  MES 11
        if($mesAtual == 11){

       
            if($hoje <= $diaBloqueado->dia){               
                
                $minDate = Carbon::today()->format('Y-m-01');            
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);

            
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 30){

                    
                $a[] = [$ano.'-12-01', $ano.'-12-31'];
                $minDate = Carbon::today()->format('Y-m-d');            
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }




         // MES 10
        if($mesAtual == 10){

            if($hoje <= $diaBloqueado->dia){                               
                $minDate = Carbon::today()->format('Y-m-01');            
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
            }

            if($hoje > $diaBloqueado->dia && $hoje <= 31){             
                $minDate = Carbon::today()->format('Y-m-01');            
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            }
        }





       
        //  MES 09
        if($mesAtual == "09" or $mesAtual == "08" or $mesAtual == "07" or $mesAtual == "06" or $mesAtual == "05" or $mesAtual == "04" or $mesAtual == "03"){

        
            if($hoje <= $diaBloqueado->dia){               
                
               
                $minDate = Carbon::today()->format('Y-m-01');            
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);

            
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 31){
            
                $minDate = Carbon::today()->format('Y-m-01');            
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }



     
      



        //  MES 02
        if($mesAtual == "02"){

        
            if($hoje <= $diaBloqueado->dia){               
                
                $minDate = Carbon::today()->addMonths(1)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
        
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 29){
           

                $minDate = Carbon::today()->addMonths(2)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }




        //  MES 01
        if($mesAtual == "01"){

            if($hoje <= $diaBloqueado->dia){               
                
                $minDate = Carbon::today()->addMonths(1)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
        
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 31){
        


                $minDate = Carbon::today()->addMonths(2)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }




        ////////////////////////////////////////////////////////////
        // BLOQUEIO DE DIAS    
        $bloquearDias = \App\LockDays::where('tipo', 1)->get();
        //$a = [];
        foreach ($bloquearDias as $key => $value) {
            
            $a[] = [
                $value->data_inicio, 
                $value->data_fim
            ];
                
        }
        
        $a2 = [];
        $bloquearDias2 = \App\LockDays::where('tipo', 2)->get();
        
        foreach ($bloquearDias2 as $key => $value1) {
            
           $a[] = $value1->data_inicio;
        
        }
        //dd($a);
        
        $a = json_encode($a);
        ////////////////////////////////////////////////////////////

        $horario = \App\Horario::all()->first();

        /* PEGA AS DATAS RESERVADAS */
        $dateS = Carbon::now()->startOfMonth()->addMonth(1);
        $dateE = Carbon::now()->startOfMonth(); 
        $datasReservadas = \App\Hospede::whereIn(DB::RAW('month(data_inicio)'), [9,10])->orderBy('data_inicio', 'asc')->get();
        $datasReservadasCount = \App\Hospede::whereIn(DB::RAW('month(data_inicio)'), [9,10])->count();
        //dd($datasReservadas);

        if($datasReservadasCount > 0){
        foreach ($datasReservadas as $key => $value) {

        $datas[] = ["$value->data_inicio", "$value->data_termino"];
            
        $data_inicio = new DateTime($value->data_inicio);
        $data_fim = new DateTime($value->data_termino);

        $dateInterval = $data_inicio->diff($data_fim);
        $diasReservados[] = $value->data_inicio;
        for ($i=1; $i < $dateInterval->days; $i++) { 

            $diasReservados[] = date('Y-m-d', strtotime($value->data_inicio. ' + '.$i.' days'));
        
        }
        }                        
        }else{
            $diasReservados = '2000-09-22';
        }
        
        $maxYear = Carbon::today()->addYear()->format('Y');
        $minYear = Carbon::today()->format('Y');
        $diasReservados = json_encode($diasReservados);
        /*      FIM        */




       // dd('ok');
        return view('hospedagem.create', compact('tipos', 'minDate', 'maxDate', 'hoje', 'a', 'horario', 'diaBloqueado', 'unidades', 'datasReservadas','diasReservados','maxYear', 'minYear', 'unidadess'));
    }



        public function solicitarinscricaoEditNOVO($id){

        //dd('okl');
        $id = Crypt::decrypt($id);   
        date_default_timezone_set('America/Sao_Paulo');
        $usuarioAutenticado = Auth::user();
        $hoje = date("Y-m-d");
        if($usuarioAutenticado->indeterminado != 1){

            if(isset($usuarioAutenticado->validade)) {
                if(strtotime($usuarioAutenticado->validade) < strtotime($hoje)){
                \Session::flash('message', ['msg'=>"Seu documento de identidade está com a data de validade vencida! Favor atualizar o documento para prosseguir", 'class'=>'danger']);
                        return redirect()->route('home');
                }
            }        

        }

        // Tipo de Unidade por Posto de Graduação
        $tipos = \App\TipoUndHab::all();
        $GrupoTarifa = \App\GrupoTarifa::with('postos')->get();
        $GrupoTarifaPostoGraduacao = \App\GrupoTarifaPostoGraduacao::where('posto_id', $usuarioAutenticado->postograd_id)->get();
       
        if(sizeof($GrupoTarifaPostoGraduacao) == 0){

            \Session::flash('message', ['msg'=>"Grupo de Tarifa não cadastrado! Contacte o administrador!", 'class'=>'danger']);
            return redirect()->back();
        }
        
        foreach ($GrupoTarifaPostoGraduacao as $key => $value) {
           
        $tipoHab = \App\GrupoTarifa::where('id', $value->grupotarifa_id)->with('tipoundhabitacao')->get();
       
        foreach ($tipoHab as $keys => $values) {
            $unidades[] = (
            $values->tipoundhabitacao
            );    
        }
        }
        // Fim do Tipo de Unidade por Posto de Graduação

        $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
        $data_inicio = $altaTemporada->data_inicio;
        $data_termino = $altaTemporada->data_termino;
        $diaBloqueado = \App\BloqueioDia::where('id', 1)->first();
     

      
        $mesAtual = date("m");
        //$mesAtual = "08";
        $ano = date("Y");

       
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


        $ultimo_dia = date("t", mktime(0,0,0,$proximoMes,'01',$ano));
        $ultimoDiadoProximoMes = date("Y-" . "$proximoMes". "-t");
      

        $today = Carbon::now();
        $hoje = $today->format('d');
        

        if($today >= $data_inicio && $today <= $data_termino){
            $is_altaTemporada = true;
        }

        $diaBloqueado = \App\BloqueioDia::where('id', 1)->first(); 


        // MES 12
        $a = [];
        if($mesAtual == 12){
        
            if($hoje <= $diaBloqueado->dia){               
               // dd('ok');
                $minDate = Carbon::today()->addMonths(1)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
                
    
            }

            if($hoje > $diaBloqueado->dia && $hoje <= 31){
                //dd('ok1');
                $minDate = Carbon::today()->addMonths(2)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);


            }  
        
        }
       
         //  MES 11
        if($mesAtual == 11){

       
            if($hoje <= $diaBloqueado->dia){               
                
                $minDate = Carbon::today()->format('Y-m-01');            
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);

            
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 30){
            

                $a[] = [$ano.'-12-01', $ano.'-12-31'];
                $minDate = Carbon::today()->format('Y-m-d');            
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }
        



         // MES 10
        if($mesAtual == 10){

            if($hoje <= $diaBloqueado->dia){                               
                $minDate = Carbon::today()->format('Y-m-01');            
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
            }

            if($hoje > $diaBloqueado->dia && $hoje <= 31){             
                $minDate = Carbon::today()->format('Y-m-01');            
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            }
        }





       
        //  MES 09
        if($mesAtual == "09" or $mesAtual == "08" or $mesAtual == "07" or $mesAtual == "06" or $mesAtual == "05" or $mesAtual == "04" or $mesAtual == "03"){

        
            if($hoje <= $diaBloqueado->dia){               
                
               
                $minDate = Carbon::today()->format('Y-m-01');            
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);

            
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 31){
            
                $minDate = Carbon::today()->format('Y-m-01');            
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }



     
      



        //  MES 02
        if($mesAtual == "02"){

        
            if($hoje <= $diaBloqueado->dia){               
                
                $minDate = Carbon::today()->addMonths(1)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
        
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 29){
           

                $minDate = Carbon::today()->addMonths(2)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }




        //  MES 01
        if($mesAtual == "01"){

            if($hoje <= $diaBloqueado->dia){               
                
                $minDate = Carbon::today()->addMonths(1)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
        
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 31){
        


                $minDate = Carbon::today()->addMonths(2)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }


       ////////////////////////////////////////////////////////////
        // BLOQUEIO DE DIAS    
        $bloquearDias = \App\LockDays::where('tipo', 1)->get();
        //$a = [];
        foreach ($bloquearDias as $key => $value) {
            
            $a[] = [
                $value->data_inicio, 
                $value->data_fim
            ];
                
        }
        
        $a2 = [];
        $bloquearDias2 = \App\LockDays::where('tipo', 2)->get();
        
        foreach ($bloquearDias2 as $key => $value1) {
            
           $a[] = $value1->data_inicio;
        
        }
        //dd($a);
        
        $a = json_encode($a);
        ////////////////////////////////////////////////////////////


        $horario = \App\Horario::all()->first();

        /* PEGA AS DATAS RESERVADAS */
        $dateS = Carbon::now()->startOfMonth()->addMonth(1);
        $dateE = Carbon::now()->startOfMonth(); 
        $datasReservadas = \App\Hospede::whereIn(DB::RAW('month(data_inicio)'), [9,10])->orderBy('data_inicio', 'asc')->get();
        $datasReservadasCount = \App\Hospede::whereIn(DB::RAW('month(data_inicio)'), [9,10])->count();
        //dd($datasReservadas);

        if($datasReservadasCount > 0){
        foreach ($datasReservadas as $key => $value) {
        

        $datas[] = ["$value->data_inicio", "$value->data_termino"];
            
        $data_inicio = new DateTime($value->data_inicio);
        $data_fim = new DateTime($value->data_termino);

        
        $dateInterval = $data_inicio->diff($data_fim);
        $diasReservados[] = $value->data_inicio;
        for ($i=1; $i < $dateInterval->days; $i++) { 

            $diasReservados[] = date('Y-m-d', strtotime($value->data_inicio. ' + '.$i.' days'));
        
        }
        }                        
        }else{
            $diasReservados = '2000-09-22';
        }
        
        $maxYear = Carbon::today()->addYear()->format('Y');
        $minYear = Carbon::today()->format('Y');
        $diasReservados = json_encode($diasReservados);
        /*      FIM        */
        $hospedagem = \App\Hospede::where('id', $id)->first();
        $peridoinicial = $hospedagem->data_inicio." - ".$hospedagem->data_termino;



        return view('hospedagem.edit_inscricao', compact('peridoinicial','tipos', 'minDate', 'maxDate', 'hoje','a', 'horario', 'diaBloqueado', 'hospedagem', 'unidades'));


       


    }
    public function solicitarinscricaoEdit($id){

        
        //dd('Editar');
        $id = Crypt::decrypt($id);
        date_default_timezone_set('America/Sao_Paulo');
        $usuarioAutenticado = Auth::user();


      
        // Tipo de Unidade por Posto de Graduação
        $tipos = \App\TipoUndHab::all();
        $GrupoTarifa = \App\GrupoTarifa::with('postos')->get();  
        $GrupoTarifaPostoGraduacao = \App\GrupoTarifaPostoGraduacao::where('posto_id', $usuarioAutenticado->postograd_id)->get();

        if(sizeof($GrupoTarifaPostoGraduacao) == 0){

            \Session::flash('message', ['msg'=>"Grupo de Tarifa não cadastrado! Contacte o administrador!", 'class'=>'danger']);
            return redirect()->back();
        }

        foreach ($GrupoTarifaPostoGraduacao as $key => $value) {
           
        $tipoHab = \App\GrupoTarifa::where('id', $value->grupotarifa_id)->with('tipoundhabitacao')->get();
        foreach ($tipoHab as $keys => $values) {
            $unidades[] = (
                $values->tipoundhabitacao
            );
          
        }


        }
        
    
        // Fim do Tipo de Unidade por Posto de Graduação

       

        $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
        $data_inicio = $altaTemporada->data_inicio;
        $data_termino = $altaTemporada->data_termino;
        $diaBloqueado = \App\BloqueioDia::where('id', 1)->first();
        $mesAtual = date("m");
        $ano = date("Y");



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
        
    
        $ultimo_dia = date("t", mktime(0,0,0,$proximoMes,'01',$ano)); // Mági
        $ultimoDiadoProximoMes = date("Y-" . "$proximoMes". "-t");
        $today = Carbon::now();
        $hoje = $today->format('d');
        

        if($today >= $data_inicio && $today <= $data_termino){
            $is_altaTemporada = true;
        }


 // MES 12
        $a = [];
        if($mesAtual == 12){
    
             if($hoje <= $diaBloqueado->dia){               
               // dd('ok');
                $minDate = Carbon::today()->addMonths(1)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
                
    
            }

            if($hoje > $diaBloqueado->dia && $hoje <= 31){
                //dd('ok1');
                $minDate = Carbon::today()->addMonths(2)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);


            } 
        
        }
            

       
       
        //  MES 11
        if($mesAtual == 11){

        
            if($hoje <= $diaBloqueado->dia){               
                
               
                $minDate = Carbon::today()->format('Y-m-d');            
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);

            
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 30){

                    
                $a[] = [$ano.'-12-01', $ano.'-12-31'];
                $minDate = Carbon::today()->format('Y-m-d');            
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }




         // MES 10
        if($mesAtual == 10){

            if($hoje <= $diaBloqueado->dia){                               
                $minDate = Carbon::today()->format('Y-m-d');            
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
            }

            if($hoje > $diaBloqueado->dia && $hoje <= 31){
             
                $minDate = Carbon::today()->format('Y-m-d');            
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            }
        }

     
        //  MES 09
        if($mesAtual == "09" or $mesAtual == "08" or $mesAtual == "07" or $mesAtual == "06" or $mesAtual == "05" or $mesAtual == "04" or $mesAtual == "03"){

        
            if($hoje <= $diaBloqueado->dia){               
                
               
                $minDate = Carbon::today()->format('Y-m-d');            
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);

            
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 31){
            
                $minDate = Carbon::today()->format('Y-m-d');            
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }


        //  MES 02
        if($mesAtual == "02"){

        
            if($hoje <= $diaBloqueado->dia){               
                
                $minDate = Carbon::today()->addMonths(1)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
        
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 29){
           

                $minDate = Carbon::today()->addMonths(2)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }




        //  MES 01
        if($mesAtual == "01"){

        
            if($hoje <= $diaBloqueado->dia){               
                
                $minDate = Carbon::today()->addMonths(1)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(2)->format('Y-m-'.$diaBloqueado->limitedia);
        
            }


            if($hoje > $diaBloqueado->dia && $hoje <= 31){
           

                $minDate = Carbon::today()->addMonths(2)->format('Y-m-01');
                $maxDate = Carbon::today()->addMonths(3)->format('Y-m-'.$diaBloqueado->limitedia);
            

            }
            

        }

        $bloquearDias = \App\LockDays::where('tipo', 1)->get();
        $a = [];
        foreach ($bloquearDias as $key => $value) {
            
            $a[] = [
                $value->data_inicio, 
                $value->data_fim
            ];
           
        }
       
        $a2 = [];
        $bloquearDias2 = \App\LockDays::where('tipo', 2)->get();
       
        foreach ($bloquearDias2 as $key => $value1) {
            
            $a[] = $value1->data_inicio;
            
        }
       
        $a = json_encode($a);

        $horario = \App\Horario::all()->first();
        $hospedagem = \App\Hospede::where('id', $id)->first();
        
          
        $peridoinicial = $hospedagem->data_inicio." - ".$hospedagem->data_termino;
        //dd($peridoinicial);

        return view('hospedagem.edit_inscricao', compact('peridoinicial','tipos', 'minDate', 'maxDate', 'hoje','a', 'horario', 'diaBloqueado', 'hospedagem', 'unidades'));


    }



    
    public function solicitarinscricaoEdit2($id){



        $id = Crypt::decrypt($id);
        date_default_timezone_set('America/Sao_Paulo');
        $usuarioAutenticado = Auth::user();
        //dd('ok');
        
        // Tipo de Unidade por Posto de Graduação
        $tipos = \App\TipoUndHab::all();
        $GrupoTarifa = \App\GrupoTarifa::with('postos')->get();  
        $GrupoTarifaPostoGraduacao = \App\GrupoTarifaPostoGraduacao::where('posto_id', $usuarioAutenticado->postograd_id)->get();

        if(sizeof($GrupoTarifaPostoGraduacao) == 0){

            \Session::flash('message', ['msg'=>"Grupo de Tarifa não cadastrado! Contacte o administrador!", 'class'=>'danger']);
            return redirect()->back();
        }

        foreach ($GrupoTarifaPostoGraduacao as $key => $value) {
           
        $tipoHab = \App\GrupoTarifa::where('id', $value->grupotarifa_id)->with('tipoundhabitacao')->get();
        foreach ($tipoHab as $keys => $values) {
            $unidades[] = (
                $values->tipoundhabitacao
            );
          
        }


        }
        
    
        // Fim do Tipo de Unidade por Posto de Graduação

       

        $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
        $data_inicio = $altaTemporada->data_inicio;
        $data_termino = $altaTemporada->data_termino;
        $diaBloqueado = \App\BloqueioDia::where('id', 1)->first();
        $mesAtual = date("m");
        $ano = date("Y");



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
        
    
        $ultimo_dia = date("t", mktime(0,0,0,$proximoMes,'01',$ano)); // Mági
        $ultimoDiadoProximoMes = date("Y-" . "$proximoMes". "-t");
        $today = Carbon::now();
        $hoje = $today->format('d');
        

        if($today >= $data_inicio && $today <= $data_termino){
            $is_altaTemporada = true;
        }


       
        if($mesAtual == 11 or $mesAtual == 12){

            if($hoje <= 10){

                $minDate = $ano. "-" . "$proximoMes". "-" . "01";
                $maxDate = $ano. "-" . "$proximoMes". "-" . $ultimo_dia;

            }

            if($hoje > 10 && $hoje <= 31){
            
                \Session::flash('message', ['msg'=>"Pedido de hospedagem até dia 10 do mês! Para Alta Temporada!", 'class'=>'danger']);
                return redirect()->back();

            }

        
        
        }else{
            

            $minDate = Carbon::now()->format('Y-m-d');            
            $maxDate = Carbon::today()->addMonths(1)->format('Y-m-t');
            
            
        }
        //dd($maxDate);

        $bloquearDias = \App\LockDays::where('tipo', 1)->get();
        $a = [];
        foreach ($bloquearDias as $key => $value) {
            
            $a[] = [
                $value->data_inicio, 
                $value->data_fim
            ];
           
        }
       
        $a2 = [];
        $bloquearDias2 = \App\LockDays::where('tipo', 2)->get();
       
        foreach ($bloquearDias2 as $key => $value1) {
            
            $a[] = $value1->data_inicio;
            
        }
       
        $a = json_encode($a);

        $horario = \App\Horario::all()->first();

        $hospedagem = \App\Hospede::where('id', $id)->first();
        
        
       $peridoinicial = $hospedagem->data_inicio." - ".$hospedagem->data_termino;
        //dd($peridoinicial);

        return view('hospedagem.edit_inscricao', compact('peridoinicial','tipos', 'minDate', 'maxDate', 'hoje','a', 'horario', 'diaBloqueado', 'hospedagem', 'unidades'));





    }

   

    public function store(Request $request){
        
        //dd($request->all());

        date_default_timezone_set('America/Sao_Paulo');
        $totalValor = Crypt::decrypt($request->cod);
        $valortarifa = $request->valortarifa;

        $inicial1 = substr($request->peridoinicial, 0, -13);
        $inicial = $inicial1;

        //$inicial = $request->peridoinicial;
        
        $final1 = substr($request->peridoinicial, -10);
        $final = $final1;


        $pi = Carbon::createFromFormat('d-m-Y', $inicial)->format('Y-m-d');    
        $pf = Carbon::createFromFormat('d-m-Y', $final)->format('Y-m-d');
        
        // CALCULO DE DIAS ENTRE DUAS DATAS
        // SERVE PARA SABER QUANTAS DIÁRIAS SÃO E GRAVAR NO BANCO
        $dateTime = new \DateTime($pi);
        $dataTime2 = new \DateTime($pf);
        $diarias = $dateTime->diff($dataTime2);
        // FIM
        
        $dateStart = $dateTime->format('Y-m-01');
        $dateEnd = $dateTime->format('Y-m-t');
     

        //dd($dateEnd);
        /*
        $verifica_data_mes = \App\Hospede::whereBetween('data_inicio', [$dateStart, $dateEnd])
        ->whereBetween('data_termino', [$dateStart, $dateEnd])
        ->where('und_habitacionais_id', $request->tipo)
        ->where('user_id', Auth::user()->id)
        ->count();
      
        dd($verifica_data_mes);
        if($verifica_data_mes > 0){
            \Session::flash('message', ['msg'=>"Já tem cadastro nesse mês com essa UH!", 'class'=>'danger']);
                return redirect()->route('hospede.solicitarinscricao');
        

        }
        */
        $customMessages = [
                      
            'tipo.required' => 'Campo obrigatório',
            'peridoinicial.required' => 'Campo obrigatório',
            'adultos.required' => 'Campo obrigatório',
            'criancas.required' => 'Campo obrigatório',
            'pne.required' => 'Campo obrigatório',
            'pet.required' => 'Campo obrigatório',
            //'pet.required' => 'Campo obrigatório',
         
        ];

        $validatedData = [
            'tipo' => 'required',
            'peridoinicial' => 'required',
            //'final' => 'required',
            'adultos' => 'required',
            'criancas' => 'required',
            'pne' => 'required',
            'pet' => 'required',
            'observacao' => 'nullable',
            
           
        ];


        $validatedData = $request->validate($validatedData, $customMessages);       
        


        //VERIFICA SE JA FEZ UM CADASTRO MESMO PERIODO E BLOQUEIA

        $verificaDuplicados = \App\Hospede::where('data_inicio', $pi)
        ->where('data_termino', $pf)
        ->where('user_id', Auth::user()->id)
        ->count();
        
        //dd($verificaDuplicados);
        if($verificaDuplicados > 0){
            \Session::flash('message', ['msg'=>"Inscrição duplicada, selecione outra data!", 'class'=>'danger']);
            return redirect()->route('hospede.meuspedidos'); 
        }
        //

        //dd($verificaDuplicados);



        if(isset($request->id)){
            $consulta = \App\Hospede::where('id', $request->id)->first();  
        }else{
            $consulta = new \App\Hospede(); 
        }
        $consulta->user_cpf = Auth::user()->cpf;
        $consulta->user_id = Auth::user()->id;
        $consulta->tipo_und_id  = $validatedData['tipo'];
        
        $consulta->data_inicio = $pi;
        $consulta->data_termino = $pf;

        
        $consulta->adulto = $validatedData['adultos'];
        $consulta->crianca  = $validatedData['criancas'];
        $consulta->pne  = $validatedData['pne'];
        $consulta->pet  = $validatedData['pet'];
        $consulta->observacao  = $validatedData['observacao'];
        $consulta->valor  = $totalValor;
        $consulta->valortarifa = $valortarifa;
        $consulta->qntdiarias = $diarias->days;
       


        if(isset($request->id)){
        
            $consulta->update();
            //$email = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\ConfirmaHospedagem($consulta));

            \Session::flash('message', ['msg'=>"Inscrição atualizada com sucesso!", 'class'=>'success']);
            return redirect()->route('hospede.meuspedidos');
        
        }else{
          
            $consulta->save();
            //return new \App\Mail\ConfirmaHospedagem($consulta);
            
            
            // este > $email = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\ConfirmaHospedagem($consulta)); 


            if($email == 0){
                \Session::flash('message', ['msg'=>"Inscrição realizada, receberá em seu e-mail os dados da sua requisição", 'class'=>'success']);
                return redirect()->route('hospede.meuspedidos');      
            }else{
                \Session::flash('message', ['msg'=>"Inscrição realizada!", 'class'=>'success']);
                return redirect()->route('hospede.meuspedidos');      
            }   

        }
        
        
        return redirect()->route('hospede.meuspedidos');
      

        
    }





    public function storeEdit(Request $request){

        
        date_default_timezone_set('America/Sao_Paulo');
        $totalValor = Crypt::decrypt($request->cod);
        $valortarifa = $request->valortarifa;

        $inicial1 = substr($request->peridoinicial, 0, -13);
        $inicial = $inicial1;

        //$inicial = $request->peridoinicial;
        $final1 = substr($request->peridoinicial, -10);
        $final = $final1;


        // CALCULO DE DIAS ENTRE DUAS DATAS
        // SERVE PARA SABER QUANTAS DIÁRIAS SÃO E GRAVAR NO BANCO
        $dateTime = new \DateTime($inicial);
        $dataTime2 = new \DateTime($final);
        $diarias = $dateTime->diff($dataTime2);
        // FIM
        
        $dateStart = $dateTime->format('Y-m-01');
        $dateEnd = $dateTime->format('Y-m-t');
     

        //dd($dateEnd);
        /*
        $verifica_data_mes = \App\Hospede::whereBetween('data_inicio', [$dateStart, $dateEnd])
        ->whereBetween('data_termino', [$dateStart, $dateEnd])
        ->where('und_habitacionais_id', $request->tipo)
        ->where('user_id', Auth::user()->id)
        ->count();
      
        dd($verifica_data_mes);
        if($verifica_data_mes > 0){
            \Session::flash('message', ['msg'=>"Já tem cadastro nesse mês com essa UH!", 'class'=>'danger']);
                return redirect()->route('hospede.solicitarinscricao');
        

        }
        */
        $customMessages = [
                      
            'tipo.required' => 'Campo obrigatório',
            'peridoinicial.required' => 'Campo obrigatório',
            'adultos.required' => 'Campo obrigatório',
            'criancas.required' => 'Campo obrigatório',
            'pne.required' => 'Campo obrigatório',
            'pet.required' => 'Campo obrigatório',
            //'pet.required' => 'Campo obrigatório',
         
        ];

        $validatedData = [
            'tipo' => 'required',
            'peridoinicial' => 'required',
            //'final' => 'required',
            'adultos' => 'required',
            'criancas' => 'required',
            'pne' => 'required',
            'pet' => 'required',
            'observacao' => 'nullable',
            
           
        ];


        $validatedData = $request->validate($validatedData, $customMessages);       
        


        //VERIFICA SE JA FEZ UM CADASTRO MESMO PERIODO E BLOQUEIA

        $verificaDuplicados = \App\Hospede::where('data_inicio', $inicial)
        ->where('data_termino', $final)



        ->count();

        if($verificaDuplicados > 0){
        //    \Session::flash('message', ['msg'=>"Inscrição duplicada, selecione outra data!", 'class'=>'danger']);
         //   return redirect()->route('hospede.meuspedidos'); 
        }
        //

        //dd($verificaDuplicados);



        if(isset($request->id)){
            $consulta = \App\Hospede::where('id', $request->id)->first();  
        }else{
            $consulta = new \App\Hospede(); 
        }
        $consulta->user_cpf = Auth::user()->cpf;
        $consulta->user_id = Auth::user()->id;
        $consulta->tipo_und_id  = $validatedData['tipo'];
        
        $consulta->data_inicio = $inicial;
        $consulta->data_termino = $final;

        
        $consulta->adulto = $validatedData['adultos'];
        $consulta->crianca  = $validatedData['criancas'];
        $consulta->pne  = $validatedData['pne'];
        $consulta->pet  = $validatedData['pet'];
        $consulta->observacao  = $validatedData['observacao'];
        $consulta->valor  = $totalValor;
        $consulta->valortarifa = $valortarifa;
        $consulta->qntdiarias = $diarias->days;
       


        if(isset($request->id)){
        
            $consulta->update();
            $email = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\ConfirmaHospedagem($consulta));

            \Session::flash('message', ['msg'=>"Inscrição atualizada com sucesso!", 'class'=>'success']);
            return redirect()->route('hospede.meuspedidos');
        
        

        }else{
        







            $consulta->save();
            //return new \App\Mail\ConfirmaHospedagem($consulta);
            $email = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\ConfirmaHospedagem($consulta)); 


            if($email == 0){
                \Session::flash('message', ['msg'=>"Inscrição realizada, receberá em seu e-mail os dados da sua requisição", 'class'=>'success']);
                return redirect()->route('hospede.meuspedidos');      
            }else{
                \Session::flash('message', ['msg'=>"Inscrição realizada!", 'class'=>'success']);
                return redirect()->route('hospede.meuspedidos');      
            }   

        }
        
        
        return redirect()->route('hospede.meuspedidos');
      

        
    }


    public function ConfirmarDados(Request $request){


       


        $inicial = $request->peridoinicial;
        $final = $request->final;
       
        if ($inicial == "" or $final == "") {
             \Session::flash('message', ['msg'=>"Data vazia!", 'class'=>'danger']);
        return redirect()->back();
        }

       
        $pi = Carbon::createFromFormat('d-m-Y', $inicial)->format('Y-m-d');    
      
        $pf = Carbon::createFromFormat('d-m-Y', $final)->format('Y-m-d');
        
    
        

        $datework = Carbon::createFromDate($pi);
        $diasHospedagem = $datework->diffInDays($pf);
        


        $pegaTemporadaAlta = \App\Temporada::where('tipo_temporada_id', 1)->first();
        $pegaTemporadaBaixa = \App\Temporada::where('tipo_temporada_id', 2)->first();


        $dataTemporadaAltaInicio = strtotime($pegaTemporadaAlta->data_inicio);
        $dataTemporadaAltaTermino = strtotime($pegaTemporadaAlta->data_termino);
        
        $dataTemporadaBaixaInicio = strtotime($pegaTemporadaBaixa->data_inicio);
        $dataTemporadaBaixaTermino = strtotime($pegaTemporadaBaixa->data_termino);


         //$mutable = Carbon::now();        
        //$modifiedMutable = $mutable->add(2, 'day');
        $data = new \DateTime($pi);
        //$data1 = $data->format('Y-m-d');
        //dd($data1);
        //print_r($data1);
        //$data->add(new \DateInterval('P'.$diasHospedagem.'D'));
        //print_r($data);
        //$diasHospedagem = $diasHospedagem + 1;
        
        $pi1 = strtotime($pi);
        $pf1 = strtotime($pf);

        $a[] = $pi;

        $diasAltaTemporada = 0;
        $diasBaixaTemporada = 0;
        for ($i=0; $i < $diasHospedagem; $i++) { 
          
        //$a[] = '';
        $data1 = $data->add(new \DateInterval('P1D'));
        $data2 = $data1->format('Y-m-d');
    
        
        if ($dataTemporadaAltaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaAltaTermino) {
            $diasAltaTemporada = $diasAltaTemporada + 1;
            //echo "Alta Temporada<br>";
        }


        if ($dataTemporadaBaixaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaBaixaTermino) {
            $diasBaixaTemporada = $diasBaixaTemporada + 1;
            //echo "Baixa Temporada";
        }


        $a[] = $data2;
        
        }

        //dd($diasBaixaTemporada);
        //var_dump($a);


        $grupoTarifa = \App\GrupoTarifa::where('unidade_habitacional_id', $request->tipo)->with('postos')->get();
        
        
        
        //dd($grupoTarifa);


        foreach ($grupoTarifa as $key => $value) {
            foreach ($value->postos as $posto) {
               // echo $posto->pivot->grupotarifa_id;
                if($posto->id === Auth::user()->postograd_id){
                    

                    $tarifa = \App\Tarifas::where('tipoundhab_id', $request->tipo)
                    ->where('grupo_destinacao_id', $posto->pivot->grupotarifa_id)
                    ->first();

                    
                }
            }
            
        }
        //dd('ok');
        if (empty($tarifa)) {
            
            \Session::flash('message', ['msg'=>"Tarifa não encontrada para essa UH! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }
        //dd($tarifa);


        // CALCULA DIARIAS
        $calculaDiariaAlta = 0;
        if($diasAltaTemporada > 0){
            $calculaDiariaAlta = $diasAltaTemporada * $tarifa->valor;
            $totalValor = $calculaDiariaAlta;
        }

        $calculaDiariaBaixa = 0;
        if($diasBaixaTemporada > 0){
            $calculaDiariaBaixa = $diasBaixaTemporada * $tarifa->valor_baixa;
            $totalValor = $calculaDiariaBaixa;
        }  
        
        if($calculaDiariaAlta > 0 and $calculaDiariaBaixa > 0){
            $totalValor = $calculaDiariaAlta + $calculaDiariaBaixa;
        }

        //FIM DIARIAS
        //dd($totalValor);





        //dd($data);

       

       
        // DATA INICIO ALTA TEMPORADA
        // 01-12-20

        // DATA TERMINO ALTA TEMPORADA
        // 28-02-21

        


       
            
        
        //dd($pegaTemporadaBaixa);




        //dd($intervalo);
        // FIM DIAS HOSPEDAGEM

        
        //dd(Auth::user()->postograd_id);
        //dd(Auth::user());

        $tarifas = \App\Tarifas::where('tipoundhab_id', $request->tipo)->get();
        //dd($tarifas);

        

        

        








        /*
        $verificaPostos = \App\GrupoTarifaPostoGraduacao::where('posto_id', Auth::user()->postograd_id)
        ->where('grupotarifa_id', 44)
        ->get();      
        dd($verificaPostos);
        */
        $totalValor = Crypt::encrypt($totalValor);

        $tipos = \App\TipoUndHab::all();
        $consulta = $request;
        //dd($consulta);
        return view('hospedagem.confirmarantigo', compact('consulta', 'tipos', 'diasHospedagem', 'calculaDiariaAlta', 'calculaDiariaBaixa', 'totalValor'));


    }

        public function ConfirmarDadosAntigo(Request $request){


            //dd('Confirmar Dados');
            date_default_timezone_set('America/Sao_Paulo');
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///////////////////   FUNÇÃO QUE PEGA O PRIMEIRO MES DA ALTA TEMPORADA E O ULTIMO MES ////////////////////////////
            $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
            $baixaTemporada = \App\Temporada::where('tipo_temporada_id', 2)->first();
            
            $altaTemporadaInicio = new DateTime($altaTemporada->data_inicio);
            $altaTemporadaTermino = new DateTime($altaTemporada->data_termino);
            
            
            $mesAnteriorAltaTemporadaInicio = new DateTime($altaTemporadaInicio->format('Y-m-d'));
            $mesAnteriorAltaTemporadaInicio = $mesAnteriorAltaTemporadaInicio->modify('last day of last month');
            $mesAnteriorAltaTemporadaInicio = $mesAnteriorAltaTemporadaInicio->format('m');
       
             
            
            $interval = $altaTemporadaInicio->diff($altaTemporadaTermino);

            

            // MONTA UM ARRAY COM OS MESES DA ALTA TEMPORADA
            $mesesAltaTemporada[] = $altaTemporadaInicio->format('m');       
            for ($i=0; $i < $interval->m; $i++) {

                    $altaTemporadaInicio->add(new \DateInterval('P1M'));                 
                    $mesesAltaTemporada[] = $altaTemporadaInicio->format('m');
                
                   
            }
            ///////////////////   FUNÇÃO QUE PEGA O PRIMEIRO MES DA ALTA TEMPORADA E O ULTIMO MES ////////////////////////////

            /*  VERIFICA SE É ALTA TEMPORADA */
            $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
            $baixaTemporada = \App\Temporada::where('tipo_temporada_id', 2)->first();

           
            $altaTemporadaInicio1 = strtotime($altaTemporada->data_inicio);
            $altaTemporadaTermino1 = strtotime($altaTemporada->data_termino);

            $baixaTemporadaInicio1 = strtotime($baixaTemporada->data_inicio);
            $baixaTemporadaTermino1 = strtotime($baixaTemporada->data_termino);

            

            $dataperidoinicial = substr($request->peridoinicial, 0, -13);
            //$dataperidoinicial = new DateTime($request->peridoinicial);
            $dataperidoinicial = new DateTime($dataperidoinicial);
            $dataperidoinicial = $dataperidoinicial->format('Y-m-d');
            //dd($dataperidoinicial);
            $dataperidoinicialToTime = strtotime($dataperidoinicial);




            $datafinal = substr($request->peridoinicial, -10);
            $datafinal = new DateTime($datafinal);
            //$datafinal = new DateTime($request->final);
            $datafinal = $datafinal->format('Y-m-d');
            $datafinalToTime = strtotime($datafinal);
            //dd($datafinalToTime);


            $dataPedido = date("Y-m-d");
            $dataPedido = strtotime($dataPedido);
            

           if ($dataperidoinicialToTime >= $altaTemporadaInicio1 and $dataperidoinicialToTime <= $altaTemporadaTermino1) {
                $is_altaTemporada = true;
                
            }elseif($datafinalToTime >= $altaTemporadaInicio1 and $datafinalToTime <= $altaTemporadaTermino1) {
                ///$is_altaTemporada = true;
                $is_altaTemporada = false;
               
            //dd('ok');

            }else{
                $is_altaTemporada = false;
            }
            
            //dd($is_altaTemporada);

            // VERIFICA SE A DATA DO PEDIDO ESTA NA ALTA TEMPORADA
            if($dataPedido >= $altaTemporadaInicio1 and $dataPedido <= $altaTemporadaTermino1){
                $pedidoDentroAltaTemporada = true;
            }else{
                $pedidoDentroAltaTemporada = false;
            }


            // VERIFICA SE A DATA DO PEDIDO ESTA NA ALTA TEMPORADA 
            



            /*  VERIFICA SE É ALTA TEMPORADA */


            ///////////////////////////////////////////////////////////////////////////////////////
            /* FUNCAO QUE BLOQUEIA A INSCRICAO ANTES DO DIA AGENDADO NOS MESES DE ALTA TEMPORADA */
            $diaBloqueado = \App\BloqueioDia::where('id', 1)->first();
            

            //$dataperidoinicial = substr($request->peridoinicial, 0, -13);
            $dataperidoinicial = new DateTime($dataperidoinicial);
            $dataperidoinicial = $dataperidoinicial->format('m');


            $datafinal = new DateTime($datafinal);
            //$datafinal = new DateTime($request->final);
            $datafinal = $datafinal->format('m');
            //dd($datafinal);


            $mes = date("m");
            $diaHoje = date("d");
            
            /*  PEDIDOS EM ALTA TEMPORADA ATÉ O DIA SALVO EX 10 DO MES */
            $ultimoMesdaAltaTemporada = $mesesAltaTemporada;
            $ultimoMesdaAltaTemporada = end($ultimoMesdaAltaTemporada);
            //dd($ultimoMesdaAltaTemporada);
            if($pedidoDentroAltaTemporada){
           
                // PEDIDOS REALIZADOS NO ULTIMO MES DA ALTA TEMPORADA SÓ SERÃO ACEITOS PARA O PROXIMO MES
                if($ultimoMesdaAltaTemporada == $mes){

                    
                    if($dataperidoinicial == $ultimoMesdaAltaTemporada or $datafinal == $ultimoMesdaAltaTemporada ){
                        //\Session::flash('message', ['msg'=>"Pedidos aceitos somente para o próximo mês.", 'class'=>'danger']);
                          //  return redirect()->back();
                        }
                
                }
             
            }             
            /*  PEDIDOS EM ALTA TEMPORADA ATÉ O DIA SALVO EX 10 DO MES */

            
            /* VERIFICA PEDIDOS DENTRO DO MES ANTERIOR AO PRIMEIRO MES DA ALTA TEMPORADA EXEMPLO MES NOVEMBRO */
            $primeiroMesdaAltaTemporada = $mesesAltaTemporada;
            $primeiroMesdaAltaTemporada = current($primeiroMesdaAltaTemporada);

            if($mes == $mesAnteriorAltaTemporadaInicio){
         
                if($diaHoje > $diaBloqueado->dia){
                                 
                    if($dataperidoinicial == $primeiroMesdaAltaTemporada or $datafinal == $primeiroMesdaAltaTemporada){
                    
                        //\Session::flash('message', ['msg'=>"Inscrições encerradas para o próximo mês.", 'class'=>'danger']);
                       // return redirect()->back();
                
                    }

                }

            }
            /* VERIFICA PEDIDOS DENTRO DO MES ANTERIOR AO PRIMEIRO MES DA ALTA TEMPORADA EXEMPLO MES NOVEMBRO */


            /* VERIFICA PEDIDOS DENTRO DO MES 12 */
            if($mes == $primeiroMesdaAltaTemporada){
            
                if($diaHoje > $diaBloqueado->dia){
       
                    // \Session::flash('message', ['msg'=>"Inscrições encerradas dia ". $diaBloqueado->dia ." do mês!", 'class'=>'danger']);
                    // return redirect()->back();
                                       
                   
                } 

                if($dataperidoinicial == $primeiroMesdaAltaTemporada or $datafinal == $primeiroMesdaAltaTemporada){
                  
                       // \Session::flash('message', ['msg'=>"Inscrições encerradas dia ". $diaBloqueado->dia ." do mês!", 'class'=>'danger']);
                       // return redirect()->back();
                
                        }                  
            }
            /* VERIFICA PEDIDOS DENTRO DO MES 12 */
            /* FIM DA FUNCAO*/





            /*  VERIFICA SE JÁ TEM PEDIDO DE HOSPEDAGEM NAQUELE MêS  */
            //$dataInicialPedido = new DateTime($request->peridoinicial);
            $dataInicialPedido1 = substr($request->peridoinicial, 0, -13);
            $dataInicialPedido = new DateTime($dataInicialPedido1);
            //$dataInicialPedido = $dataInicialPedido->format('Y-m-d');
            $dataInicialPedidoAno = $dataInicialPedido->format('Y');
            $dataInicialPedidoMes = $dataInicialPedido->format('m');

            $dataFinalPedido1 = substr($request->peridoinicial, -10);
            $dataFinalPedido = new DateTime($dataFinalPedido1);
            //$dataFinalPedido = $dataFinalPedido->format('Y-m-d');
            //dd($dataFinalPedido);
            $dataFinalPedidoAno = $dataFinalPedido->format('Y');
            $dataFinalPedidoMes = $dataFinalPedido->format('m');
            

            $idUsuario = Auth::user()->id;
           
            
            /*
            $verificaQuantidadeHospedagemMesAnoCount = \App\Hospede::where('user_id', $idUsuario)
            ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
            ->count();
            */

          
            
           

       

            $verificaQuantidadeHospedagemMesAnoCount = \App\Hospede::where('user_id', $idUsuario)
                ->whereYear('data_inicio', '=', $dataInicialPedidoAno)
                ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
                ->count();


            $verificaQuantidadeHospedagemMesAnoCountTipo = \App\Hospede::where('user_id', $idUsuario)
                ->whereYear('data_inicio', '=', $dataInicialPedidoAno)
                ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
                ->whereIn('tipo_und_id', [11,12])
               ->count();

              
       
            $quantidadeReservas = \App\QuantidadeReserva::first();
            

            if ($is_altaTemporada == true) {
            
                $quantidadeReservas = \App\QuantidadeReserva::first();

                if($request->tipo == 12 or $request->tipo == 11){   


                    if($verificaQuantidadeHospedagemMesAnoCountTipo >= 2){



                        \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de 2 reservas para esse mês da alta temporada! Nas UH Camping e/ou Motor-Home", 'class'=>'danger']);
                        return redirect()->back();


                    }



                }else{


                    if($verificaQuantidadeHospedagemMesAnoCount >= $quantidadeReservas->reservas){

                        \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de ". $quantidadeReservas->reservas ." reserva para esse mês da alta temporada!", 'class'=>'danger']);
                        return redirect()->back();

                    }


                }

                //dd($verificaQuantidadeHospedagemMesAnoCountTipo);

                

            }else{
                //dd('baixa');
                if($request->tipo == 12 or $request->tipo == 11){

                    if($verificaQuantidadeHospedagemMesAnoCountTipo >= 2){



                        \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de 2 reservas para esse mês da baixa temporada! Nas UH Camping e/ou Motor-Home", 'class'=>'danger']);
                        return redirect()->back();


                    }




                }else{

                    if($verificaQuantidadeHospedagemMesAnoCount >= $quantidadeReservas->qnt_reservas_baixa_temporada){
                        //dd('entrou');
                            \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de ". $quantidadeReservas->qnt_reservas_baixa_temporada ." reservas para esse mês da baixa temporada!", 'class'=>'danger']);
                            return redirect()->back();

                    }

                }
                
            }      


            /*  
        


            VERIFICA SE JÁ TEM PEDIDO DE HOSPEDAGEM NAQUELE MêS  



            */
           



        

        $inicial1 = substr($request->peridoinicial, 0, -13);
        $inicial = $inicial1;
        //$inicial = $request->peridoinicial;

        $final1 = substr($request->peridoinicial, -10);
        $final = $final1;
        //$final = $request->final;
        //dd($final);


        $inicialData = strtotime($inicial);
        $finalData = strtotime($final);
        
         if ($inicialData === $finalData) {
             \Session::flash('message', ['msg'=>"Data Inicial igual Data final! Escolha outra Data!", 'class'=>'danger']);
           return redirect()->back();
        }
         if ($inicialData > $finalData) {
             \Session::flash('message', ['msg'=>"Data Final maior que data Inicial! Escolha outra Data!", 'class'=>'danger']);
           return redirect()->back();
        }
        if ($inicial == "" or $final == "") {
             \Session::flash('message', ['msg'=>"Data vazia!", 'class'=>'danger']);
           return redirect()->back();
        }

   
         $pi = Carbon::createFromFormat('d-m-Y', $inicial)->format('Y-m-d');    
     
         $pf = Carbon::createFromFormat('d-m-Y', $final)->format('Y-m-d');
        
        $mesInicial = new DateTime($pi);
        $mesFinal = new DateTime($pf);
        
        
  
        $mesInicial = $mesInicial->format('m');
        $mesFinal = $mesFinal->format('m');
 


        
        /*    Verifica o Proximo Mês     */
        $mesAtual = date("m");
        //$mesAtual = "11";
        
 
        if($mesAtual == 11){
            $proximoMes = "01";
            $proximoMes1 = "01";
 


        }
        
        if($mesAtual == 12){
            $proximoMes = "02";
            $proximoMes1 = "02";
 

        }

        if($mesAtual >= 1 and $mesAtual <= 10){
   
             $proximoMes = $mesAtual + 1;
    
            $proximoMes1 = $mesAtual + 1;
            if($mesAtual == 10){
                $proximoMes1 = "11";
            }
            if($mesAtual == 9){
                $proximoMes1 = "10";
            }
            
            if($proximoMes <= 9){
                    $proximoMes = '0' . $proximoMes;
                    $proximoMes1 = '0' .$proximoMes1;

            }
            
        }
        //dd($proximoMes);

        //$mesAtual = date("m");
        //$mesAtual = 11;
        //$mesBloqueado = $mesAtual + 2;
        //dd($mesBloqueado);
        /*
        if($mesInicial != $mesAtual and $mesInicial != $proximoMes){
            dd($proximoMes);
            \Session::flash('message', ['msg'=>"Data Inicial fora do período permitido!", 'class'=>'danger']);
                return redirect()->back();
        }

        if($mesFinal != $mesAtual and $mesFinal != $proximoMes){
            dd("teste1");
            \Session::flash('message', ['msg'=>"Data Final fora do período permitido!", 'class'=>'danger']);
                return redirect()->back();
        }
        /*    Verifica o Proximo Mês      */

            
        //dd('break');

        $datework = Carbon::createFromDate($pi);
        $diasHospedagem = $datework->diffInDays($pf);


        $pegaTemporadaAlta = \App\Temporada::where('tipo_temporada_id', 1)->first();
        $pegaTemporadaBaixa = \App\Temporada::where('tipo_temporada_id', 2)->first();


        $dataTemporadaAltaInicio = strtotime($pegaTemporadaAlta->data_inicio);
        $dataTemporadaAltaTermino = strtotime($pegaTemporadaAlta->data_termino);
       

        $dataTemporadaBaixaInicio = strtotime($pegaTemporadaBaixa->data_inicio);
        $dataTemporadaBaixaTermino = strtotime($pegaTemporadaBaixa->data_termino);

       
        $data = new \DateTime($pi);
       
        
        $pi1 = strtotime($pi);
        $pf1 = strtotime($pf);

        $a[] = $pi;

        $diasAltaTemporada = 0;
        $diasBaixaTemporada = 0;
        for ($i=0; $i < $diasHospedagem; $i++) { 
        
        $data1 = $data->add(new \DateInterval('P1D'));
        $data2 = $data1->format('Y-m-d');
        
        
        if ($dataTemporadaAltaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaAltaTermino) {
            $diasAltaTemporada = $diasAltaTemporada + 1;
           

        }elseif($dataTemporadaBaixaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaBaixaTermino) {
            $diasBaixaTemporada = $diasBaixaTemporada + 1;
           
        }else{

            // QUANDO O ADMINISTRADOR NAO ATUALIZA A DATA DE TEMPORADA ELE RETORNA ESSE ERRO , QUANDO O USUARIO TENTA REALIZAR A INSCRICAO!
                \Session::flash('message', ['msg'=>"Temporada não definida! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }


        $a[] = $data2;
       
        }
       
        /* VERIFICA SE HA UNIDADE TEM GRUPO CADASTRADO E RETORNA ERRO*/
        $grupoTarifa = \App\GrupoTarifa::where('unidade_habitacional_id', $request->tipo)->with('postos')->get();
        if($grupoTarifa->count() == 0){
            \Session::flash('message', ['msg'=>"Unidade Habitacional sem Grupo cadastrado! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }
        /* VERIFICA SE HA UNIDADE TEM GRUPO CADASRADO E RETORNA ERRO*/
        
            
       

        /* SE TIVER GRUPO CADASTRADO FAS O LOOPING VERIFICANDO QUAL GRUPO TEM QUAIS POSTOS */
        foreach ($grupoTarifa as $key => $value) {

            foreach ($value->postos as $posto) {
                            
                
                if($posto->id === Auth::user()->postograd_id){
                    

                    $tarifa = \App\Tarifas::where('tipoundhab_id', $request->tipo)
                    ->where('grupo_destinacao_id', $posto->pivot->grupotarifa_id)
                    ->first();

                  
                }
            }
            
        }
        /* SE TIVER GRUPO CADASTRADO FAS O LOOPING VERIFICANDO QUAL GRUPO TEM QUAIS POSTOS */



       
        //SE TIVER NENHUM POSTO SEM GRUPO CADASTRADO ELE RETORNA O USUARIO COM ESSA MENSAGEM ABAIXO
        if (empty($tarifa)) {
            
            \Session::flash('message', ['msg'=>"Posto sem grupo tarifa Cadastrado! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }
        /* SE TIVER GRUPO CADASTRADO FAS O LOOPING VERIFICANDO QUAL GRUPO*/
        
       


        /* FAZ OS CALCULOS DAS DIARIAS */
        $calculaDiariaAlta = 0;
        if($diasAltaTemporada > 0){
            $calculaDiariaAlta = $diasAltaTemporada * $tarifa->valor;
            $totalValor = $calculaDiariaAlta;
           
        }

        $calculaDiariaBaixa = 0;
        if($diasBaixaTemporada > 0){
            
            $calculaDiariaBaixa = $diasBaixaTemporada * $tarifa->valor_baixa;
            $totalValor = $calculaDiariaBaixa;
            
        }  
        
        if($calculaDiariaAlta > 0 and $calculaDiariaBaixa > 0){
            $totalValor = $calculaDiariaAlta + $calculaDiariaBaixa;
        }
        /* FAZ OS CALCULOS DAS DIARIAS */


       if($is_altaTemporada == true){
         $valorTarifa = $tarifa->valor;
   
       }else{
         $valorTarifa = $tarifa->valor_baixa;
       }
       // FIM DIAS HOSPEDAGEM

     
       
       $tarifas = \App\Tarifas::where('tipoundhab_id', $request->tipo)->get();
       $tipos = \App\TipoUndHab::all();
       $consulta = $request;
        
       $totalValor = Crypt::encrypt($totalValor);
        
       return view('hospedagem.confirmarantigo', compact('consulta', 'tipos', 'diasHospedagem', 'calculaDiariaAlta', 'calculaDiariaBaixa', 'totalValor', 'valorTarifa'));


    }
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
      public function solicitarinscricaoEditConfirmarNOVO(Request $request){


            dd('Confirmar Dados NOVO');
            date_default_timezone_set('America/Sao_Paulo');
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///////////////////   FUNÇÃO QUE PEGA O PRIMEIRO MES DA ALTA TEMPORADA E O ULTIMO MES ////////////////////////////
            $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
            $baixaTemporada = \App\Temporada::where('tipo_temporada_id', 2)->first();
            
            $altaTemporadaInicio = new DateTime($altaTemporada->data_inicio);
            $altaTemporadaTermino = new DateTime($altaTemporada->data_termino);
            
            
            $mesAnteriorAltaTemporadaInicio = new DateTime($altaTemporadaInicio->format('Y-m-d'));
            $mesAnteriorAltaTemporadaInicio = $mesAnteriorAltaTemporadaInicio->modify('last day of last month');
            $mesAnteriorAltaTemporadaInicio = $mesAnteriorAltaTemporadaInicio->format('m');
       
             
            
            $interval = $altaTemporadaInicio->diff($altaTemporadaTermino);

            

            // MONTA UM ARRAY COM OS MESES DA ALTA TEMPORADA
            $mesesAltaTemporada[] = $altaTemporadaInicio->format('m');       
            for ($i=0; $i < $interval->m; $i++) {

                    $altaTemporadaInicio->add(new \DateInterval('P1M'));                 
                    $mesesAltaTemporada[] = $altaTemporadaInicio->format('m');
                
                   
            }
            ///////////////////   FUNÇÃO QUE PEGA O PRIMEIRO MES DA ALTA TEMPORADA E O ULTIMO MES ////////////////////////////

            /*  VERIFICA SE É ALTA TEMPORADA */
            $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
            $baixaTemporada = \App\Temporada::where('tipo_temporada_id', 2)->first();

           
            $altaTemporadaInicio1 = strtotime($altaTemporada->data_inicio);
            $altaTemporadaTermino1 = strtotime($altaTemporada->data_termino);

            $baixaTemporadaInicio1 = strtotime($baixaTemporada->data_inicio);
            $baixaTemporadaTermino1 = strtotime($baixaTemporada->data_termino);

            

            $dataperidoinicial = substr($request->peridoinicial, 0, -13);
            //$dataperidoinicial = new DateTime($request->peridoinicial);
            $dataperidoinicial = new DateTime($dataperidoinicial);
            $dataperidoinicial = $dataperidoinicial->format('Y-m-d');
            //dd($dataperidoinicial);
            $dataperidoinicialToTime = strtotime($dataperidoinicial);




            $datafinal = substr($request->peridoinicial, -10);
            $datafinal = new DateTime($datafinal);
            //$datafinal = new DateTime($request->final);
            $datafinal = $datafinal->format('Y-m-d');
            $datafinalToTime = strtotime($datafinal);
            //dd($datafinalToTime);


            $dataPedido = date("Y-m-d");
            $dataPedido = strtotime($dataPedido);
            

           if ($dataperidoinicialToTime >= $altaTemporadaInicio1 and $dataperidoinicialToTime <= $altaTemporadaTermino1) {
                $is_altaTemporada = true;
                
            }elseif($datafinalToTime >= $altaTemporadaInicio1 and $datafinalToTime <= $altaTemporadaTermino1) {
                ///$is_altaTemporada = true;
                $is_altaTemporada = false;
               
            //dd('ok');

            }else{
                $is_altaTemporada = false;
            }
            
            //dd($is_altaTemporada);

            // VERIFICA SE A DATA DO PEDIDO ESTA NA ALTA TEMPORADA
            if($dataPedido >= $altaTemporadaInicio1 and $dataPedido <= $altaTemporadaTermino1){
                $pedidoDentroAltaTemporada = true;
            }else{
                $pedidoDentroAltaTemporada = false;
            }


            // VERIFICA SE A DATA DO PEDIDO ESTA NA ALTA TEMPORADA 
            



            /*  VERIFICA SE É ALTA TEMPORADA */


            ///////////////////////////////////////////////////////////////////////////////////////
            /* FUNCAO QUE BLOQUEIA A INSCRICAO ANTES DO DIA AGENDADO NOS MESES DE ALTA TEMPORADA */
            $diaBloqueado = \App\BloqueioDia::where('id', 1)->first();
            

            //$dataperidoinicial = substr($request->peridoinicial, 0, -13);
            $dataperidoinicial = new DateTime($dataperidoinicial);
            $dataperidoinicial = $dataperidoinicial->format('m');


            $datafinal = new DateTime($datafinal);
            //$datafinal = new DateTime($request->final);
            $datafinal = $datafinal->format('m');
            //dd($datafinal);


            $mes = date("m");
            $diaHoje = date("d");
            
            /*  PEDIDOS EM ALTA TEMPORADA ATÉ O DIA SALVO EX 10 DO MES */
            $ultimoMesdaAltaTemporada = $mesesAltaTemporada;
            $ultimoMesdaAltaTemporada = end($ultimoMesdaAltaTemporada);
            //dd($ultimoMesdaAltaTemporada);
            if($pedidoDentroAltaTemporada){
           
                // PEDIDOS REALIZADOS NO ULTIMO MES DA ALTA TEMPORADA SÓ SERÃO ACEITOS PARA O PROXIMO MES
                if($ultimoMesdaAltaTemporada == $mes){

                    
                    if($dataperidoinicial == $ultimoMesdaAltaTemporada or $datafinal == $ultimoMesdaAltaTemporada ){
                        //\Session::flash('message', ['msg'=>"Pedidos aceitos somente para o próximo mês.", 'class'=>'danger']);
                          //  return redirect()->back();
                        }
                
                }
             
            }             
            /*  PEDIDOS EM ALTA TEMPORADA ATÉ O DIA SALVO EX 10 DO MES */

            
            /* VERIFICA PEDIDOS DENTRO DO MES ANTERIOR AO PRIMEIRO MES DA ALTA TEMPORADA EXEMPLO MES NOVEMBRO */
            $primeiroMesdaAltaTemporada = $mesesAltaTemporada;
            $primeiroMesdaAltaTemporada = current($primeiroMesdaAltaTemporada);

            if($mes == $mesAnteriorAltaTemporadaInicio){
         
                if($diaHoje > $diaBloqueado->dia){
                                 
                    if($dataperidoinicial == $primeiroMesdaAltaTemporada or $datafinal == $primeiroMesdaAltaTemporada){
                    
                        //\Session::flash('message', ['msg'=>"Inscrições encerradas para o próximo mês.", 'class'=>'danger']);
                       // return redirect()->back();
                
                    }

                }

            }
            /* VERIFICA PEDIDOS DENTRO DO MES ANTERIOR AO PRIMEIRO MES DA ALTA TEMPORADA EXEMPLO MES NOVEMBRO */


            /* VERIFICA PEDIDOS DENTRO DO MES 12 */
            if($mes == $primeiroMesdaAltaTemporada){
            
                if($diaHoje > $diaBloqueado->dia){
       
                    // \Session::flash('message', ['msg'=>"Inscrições encerradas dia ". $diaBloqueado->dia ." do mês!", 'class'=>'danger']);
                    // return redirect()->back();
                                       
                   
                } 

                if($dataperidoinicial == $primeiroMesdaAltaTemporada or $datafinal == $primeiroMesdaAltaTemporada){
                  
                       // \Session::flash('message', ['msg'=>"Inscrições encerradas dia ". $diaBloqueado->dia ." do mês!", 'class'=>'danger']);
                       // return redirect()->back();
                
                        }                  
            }
            /* VERIFICA PEDIDOS DENTRO DO MES 12 */
            /* FIM DA FUNCAO*/





            /*  VERIFICA SE JÁ TEM PEDIDO DE HOSPEDAGEM NAQUELE MêS  */
            //$dataInicialPedido = new DateTime($request->peridoinicial);
            $dataInicialPedido1 = substr($request->peridoinicial, 0, -13);
            $dataInicialPedido = new DateTime($dataInicialPedido1);
            //$dataInicialPedido = $dataInicialPedido->format('Y-m-d');
            $dataInicialPedidoAno = $dataInicialPedido->format('Y');
            $dataInicialPedidoMes = $dataInicialPedido->format('m');

            $dataFinalPedido1 = substr($request->peridoinicial, -10);
            $dataFinalPedido = new DateTime($dataFinalPedido1);
            //$dataFinalPedido = $dataFinalPedido->format('Y-m-d');
            //dd($dataFinalPedido);
            $dataFinalPedidoAno = $dataFinalPedido->format('Y');
            $dataFinalPedidoMes = $dataFinalPedido->format('m');


            $idUsuario = Auth::user()->id;
       

            $verificaQuantidadeHospedagemMesAnoCount = \App\Hospede::where('user_id', $idUsuario)
                ->whereYear('data_inicio', '=', $dataInicialPedidoAno)
                ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
                ->count();


            $verificaQuantidadeHospedagemMesAnoCountTipo = \App\Hospede::where('user_id', $idUsuario)
                ->whereYear('data_inicio', '=', $dataInicialPedidoAno)
                ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
                ->whereIn('tipo_und_id', [11,12])
                ->count();

              
       
            $quantidadeReservas = \App\QuantidadeReserva::first();
            

            if ($is_altaTemporada == true) {
            
                $quantidadeReservas = \App\QuantidadeReserva::first();

                if($request->tipo == 12 or $request->tipo == 11){   


                    if($verificaQuantidadeHospedagemMesAnoCountTipo >= 2){



                        \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de 2 reservas para esse mês da alta temporada! Nas UH Camping e/ou Motor-Home", 'class'=>'danger']);
                        return redirect()->back();


                    }



                }else{


                    if($verificaQuantidadeHospedagemMesAnoCount >= $quantidadeReservas->reservas){

                        \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de ". $quantidadeReservas->reservas ." reserva para esse mês da alta temporada!", 'class'=>'danger']);
                        return redirect()->back();

                    }


                }

                //dd($verificaQuantidadeHospedagemMesAnoCountTipo);

                

            }else{
                //dd('baixa');
                if($request->tipo == 12 or $request->tipo == 11){

                    if($verificaQuantidadeHospedagemMesAnoCountTipo >= 2){



                        \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de 2 reservas para esse mês da baixa temporada! Nas UH Camping e/ou Motor-Home", 'class'=>'danger']);
                        return redirect()->back();


                    }




                }else{

                    if($verificaQuantidadeHospedagemMesAnoCount >= $quantidadeReservas->qnt_reservas_baixa_temporada){
                        //dd('entrou');
                            \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de ". $quantidadeReservas->qnt_reservas_baixa_temporada ." reservas para esse mês da baixa temporada!", 'class'=>'danger']);
                            return redirect()->back();

                    }

                }
                
            }      


            /*  
        


            VERIFICA SE JÁ TEM PEDIDO DE HOSPEDAGEM NAQUELE MêS  



            */
           



        

        $inicial1 = substr($request->peridoinicial, 0, -13);
        $inicial = $inicial1;
        //$inicial = $request->peridoinicial;

        $final1 = substr($request->peridoinicial, -10);
        $final = $final1;
        //$final = $request->final;
        //dd($final);


        $inicialData = strtotime($inicial);
        $finalData = strtotime($final);
        
         if ($inicialData === $finalData) {
             \Session::flash('message', ['msg'=>"Data Inicial igual Data final! Escolha outra Data!", 'class'=>'danger']);
           return redirect()->back();
        }
         if ($inicialData > $finalData) {
             \Session::flash('message', ['msg'=>"Data Final maior que data Inicial! Escolha outra Data!", 'class'=>'danger']);
           return redirect()->back();
        }
        if ($inicial == "" or $final == "") {
             \Session::flash('message', ['msg'=>"Data vazia!", 'class'=>'danger']);
           return redirect()->back();
        }

   
         $pi = Carbon::createFromFormat('d-m-Y', $inicial)->format('Y-m-d');    
         $pf = Carbon::createFromFormat('d-m-Y', $final)->format('Y-m-d');
        
        $mesInicial = new DateTime($pi);
        $mesFinal = new DateTime($pf);
        
        
  
        $mesInicial = $mesInicial->format('m');
        $mesFinal = $mesFinal->format('m');
 


        
        /*    Verifica o Proximo Mês     */
        $mesAtual = date("m");
        //$mesAtual = "11";
        
 
        if($mesAtual == 11){
            $proximoMes = "01";
            $proximoMes1 = "01";
 


        }
        
        if($mesAtual == 12){
            $proximoMes = "02";
            $proximoMes1 = "02";
 

        }

        if($mesAtual >= 1 and $mesAtual <= 10){
   
             $proximoMes = $mesAtual + 1;
    
            $proximoMes1 = $mesAtual + 1;
            if($mesAtual == 10){
                $proximoMes1 = "11";
            }
            if($mesAtual == 9){
                $proximoMes1 = "10";
            }
            
            if($proximoMes <= 9){
                    $proximoMes = '0' . $proximoMes;
                    $proximoMes1 = '0' .$proximoMes1;

            }
            
        }
        //dd($proximoMes);

        //$mesAtual = date("m");
        //$mesAtual = 11;
        //$mesBloqueado = $mesAtual + 2;
        //dd($mesBloqueado);
        /*
        if($mesInicial != $mesAtual and $mesInicial != $proximoMes){
            dd($proximoMes);
            \Session::flash('message', ['msg'=>"Data Inicial fora do período permitido!", 'class'=>'danger']);
                return redirect()->back();
        }

        if($mesFinal != $mesAtual and $mesFinal != $proximoMes){
            dd("teste1");
            \Session::flash('message', ['msg'=>"Data Final fora do período permitido!", 'class'=>'danger']);
                return redirect()->back();
        }
        /*    Verifica o Proximo Mês      */

            
        //dd('break');

        $datework = Carbon::createFromDate($pi);
        $diasHospedagem = $datework->diffInDays($pf);


        $pegaTemporadaAlta = \App\Temporada::where('tipo_temporada_id', 1)->first();
        $pegaTemporadaBaixa = \App\Temporada::where('tipo_temporada_id', 2)->first();


        $dataTemporadaAltaInicio = strtotime($pegaTemporadaAlta->data_inicio);
        $dataTemporadaAltaTermino = strtotime($pegaTemporadaAlta->data_termino);
       

        $dataTemporadaBaixaInicio = strtotime($pegaTemporadaBaixa->data_inicio);
        $dataTemporadaBaixaTermino = strtotime($pegaTemporadaBaixa->data_termino);

       
        $data = new \DateTime($pi);
       
        
        $pi1 = strtotime($pi);
        $pf1 = strtotime($pf);

        $a[] = $pi;

        $diasAltaTemporada = 0;
        $diasBaixaTemporada = 0;
        for ($i=0; $i < $diasHospedagem; $i++) { 
        
        $data1 = $data->add(new \DateInterval('P1D'));
        $data2 = $data1->format('Y-m-d');
        
        
        if ($dataTemporadaAltaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaAltaTermino) {
            $diasAltaTemporada = $diasAltaTemporada + 1;
           

        }elseif($dataTemporadaBaixaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaBaixaTermino) {
            $diasBaixaTemporada = $diasBaixaTemporada + 1;
           
        }else{

            // QUANDO O ADMINISTRADOR NAO ATUALIZA A DATA DE TEMPORADA ELE RETORNA ESSE ERRO , QUANDO O USUARIO TENTA REALIZAR A INSCRICAO!
                \Session::flash('message', ['msg'=>"Temporada não definida! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }


        $a[] = $data2;
       
        }
       
        /* VERIFICA SE HA UNIDADE TEM GRUPO CADASTRADO E RETORNA ERRO*/
        $grupoTarifa = \App\GrupoTarifa::where('unidade_habitacional_id', $request->tipo)->with('postos')->get();
        if($grupoTarifa->count() == 0){
            \Session::flash('message', ['msg'=>"Unidade Habitacional sem Grupo cadastrado! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }
        /* VERIFICA SE HA UNIDADE TEM GRUPO CADASRADO E RETORNA ERRO*/
        
            
       

        /* SE TIVER GRUPO CADASTRADO FAS O LOOPING VERIFICANDO QUAL GRUPO TEM QUAIS POSTOS */
        foreach ($grupoTarifa as $key => $value) {

            foreach ($value->postos as $posto) {
                            
                
                if($posto->id === Auth::user()->postograd_id){
                    

                    $tarifa = \App\Tarifas::where('tipoundhab_id', $request->tipo)
                    ->where('grupo_destinacao_id', $posto->pivot->grupotarifa_id)
                    ->first();

                  
                }
            }
            
        }
        /* SE TIVER GRUPO CADASTRADO FAS O LOOPING VERIFICANDO QUAL GRUPO TEM QUAIS POSTOS */



       
        //SE TIVER NENHUM POSTO SEM GRUPO CADASTRADO ELE RETORNA O USUARIO COM ESSA MENSAGEM ABAIXO
        if (empty($tarifa)) {
            
            \Session::flash('message', ['msg'=>"Posto sem grupo tarifa Cadastrado! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }
        /* SE TIVER GRUPO CADASTRADO FAS O LOOPING VERIFICANDO QUAL GRUPO*/
        
       


        /* FAZ OS CALCULOS DAS DIARIAS */
        $calculaDiariaAlta = 0;
        if($diasAltaTemporada > 0){
            $calculaDiariaAlta = $diasAltaTemporada * $tarifa->valor;
            $totalValor = $calculaDiariaAlta;
           
        }

        $calculaDiariaBaixa = 0;
        if($diasBaixaTemporada > 0){
            
            $calculaDiariaBaixa = $diasBaixaTemporada * $tarifa->valor_baixa;
            $totalValor = $calculaDiariaBaixa;
            
        }  
        
        if($calculaDiariaAlta > 0 and $calculaDiariaBaixa > 0){
            $totalValor = $calculaDiariaAlta + $calculaDiariaBaixa;
        }
        /* FAZ OS CALCULOS DAS DIARIAS */


       if($is_altaTemporada == true){
         $valorTarifa = $tarifa->valor;
   
       }else{
         $valorTarifa = $tarifa->valor_baixa;
       }
       // FIM DIAS HOSPEDAGEM

     
       
       $tarifas = \App\Tarifas::where('tipoundhab_id', $request->tipo)->get();

       $tipos = \App\TipoUndHab::all();
       $consulta = $request;
        
       $totalValor = Crypt::encrypt($totalValor);
        
       return view('hospedagem.confirmarantigo', compact('consulta', 'tipos', 'diasHospedagem', 'calculaDiariaAlta', 'calculaDiariaBaixa', 'totalValor', 'valorTarifa'));


    }





    //////////////////////////////////////////////////////////////////////////////////////////
    public function solicitarinscricaoEditConfirmar(Request $request){
        
            //dd("Edit Confirmar");
            date_default_timezone_set('America/Sao_Paulo');
            //dd($request->all());
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///////////////////   FUNÇÃO QUE PEGA O PRIMEIRO MES DA ALTA TEMPORADA E O ULTIMO MES ////////////////////////////
            $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
            $baixaTemporada = \App\Temporada::where('tipo_temporada_id', 2)->first();
            
            $altaTemporadaInicio = new DateTime($altaTemporada->data_inicio);
            $altaTemporadaTermino = new DateTime($altaTemporada->data_termino);
                 
            $mesAnteriorAltaTemporadaInicio = new DateTime($altaTemporadaInicio->format('Y-m-d'));
            $mesAnteriorAltaTemporadaInicio = $mesAnteriorAltaTemporadaInicio->modify('last day of last month');
            $mesAnteriorAltaTemporadaInicio = $mesAnteriorAltaTemporadaInicio->format('m');
                   
            $interval = $altaTemporadaInicio->diff($altaTemporadaTermino);

            // MONTA UM ARRAY COM OS MESES DA ALTA TEMPORADA
            $mesesAltaTemporada[] = $altaTemporadaInicio->format('m');       
            for ($i=0; $i < $interval->m; $i++) {

                    $altaTemporadaInicio->add(new \DateInterval('P1M'));                 
                    $mesesAltaTemporada[] = $altaTemporadaInicio->format('m');
                
                   
            }
            ///////////////////   FUNÇÃO QUE PEGA O PRIMEIRO MES DA ALTA TEMPORADA E O ULTIMO MES ////////////////////////////

            /*  VERIFICA SE É ALTA TEMPORADA */
            $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
            $baixaTemporada = \App\Temporada::where('tipo_temporada_id', 2)->first();
           
            $altaTemporadaInicio1 = strtotime($altaTemporada->data_inicio);
            $altaTemporadaTermino1 = strtotime($altaTemporada->data_termino);

            $baixaTemporadaInicio1 = strtotime($baixaTemporada->data_inicio);
            $baixaTemporadaTermino1 = strtotime($baixaTemporada->data_termino);


            $dataperidoinicial = substr($request->peridoinicial, 0, -13);
            //$dataperidoinicial = new DateTime($request->peridoinicial);
            $dataperidoinicial = new DateTime($dataperidoinicial);
            $dataperidoinicial = $dataperidoinicial->format('Y-m-d');
            $dataperidoinicialToTime = strtotime($dataperidoinicial);




            $datafinal = substr($request->peridoinicial, -10);
            $datafinal = new DateTime($datafinal);
            //$datafinal = new DateTime($request->final);
            $datafinal = $datafinal->format('Y-m-d');
            $datafinalToTime = strtotime($datafinal);
            //dd($datafinalToTime);


            $dataPedido = date("Y-m-d");
            $dataPedido = strtotime($dataPedido);
            

           if ($dataperidoinicialToTime >= $altaTemporadaInicio1 and $dataperidoinicialToTime <= $altaTemporadaTermino1) {
                $is_altaTemporada = true;
            
                
            }elseif($datafinalToTime >= $altaTemporadaInicio1 and $datafinalToTime <= $altaTemporadaTermino1) {
                $is_altaTemporada = true;
               

            }else{
                $is_altaTemporada = false;
            }
            

            // VERIFICA SE A DATA DO PEDIDO ESTA NA ALTA TEMPORADA
            if($dataPedido >= $altaTemporadaInicio1 and $dataPedido <= $altaTemporadaTermino1){
                $pedidoDentroAltaTemporada = true;
            }else{
                $pedidoDentroAltaTemporada = false;
            }

             
            // VERIFICA SE A DATA DO PEDIDO ESTA NA ALTA TEMPORADA 
            



            /*  VERIFICA SE É ALTA TEMPORADA */

         









             /*  VERIFICA SE JÁ TEM PEDIDO DE HOSPEDAGEM NAQUELE MêS  */
            //$dataInicialPedido = new DateTime($request->peridoinicial);
            $dataInicialPedido1 = substr($request->peridoinicial, 0, -13);
            $dataInicialPedido = new DateTime($dataInicialPedido1);
            //$dataInicialPedido = $dataInicialPedido->format('Y-m-d');
            $dataInicialPedidoAno = $dataInicialPedido->format('Y');
            $dataInicialPedidoMes = $dataInicialPedido->format('m');

            $dataFinalPedido1 = substr($request->peridoinicial, -10);
            $dataFinalPedido = new DateTime($dataFinalPedido1);
            //$dataFinalPedido = $dataFinalPedido->format('Y-m-d');
            //dd($dataFinalPedido);
            $dataFinalPedidoAno = $dataFinalPedido->format('Y');
            $dataFinalPedidoMes = $dataFinalPedido->format('m');

            $idUsuario = Auth::user()->id;
           
            /*
            $verificaQuantidadeHospedagemMesAnoCount = \App\Hospede::where('user_id', $idUsuario)
            ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
            ->count();
            */

          
            
           

            /*


            COMECA AQUI


            */
            //dd($dataInicialPedidoMes);
            $verificaQuantidadeHospedagemMesAnoCount = \App\Hospede::where('user_id', $idUsuario)
                ->whereYear('data_inicio', '=', $dataInicialPedidoAno)
                ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
                ->count();

             $verificaQuantidadeHospedagemMesAno = \App\Hospede::where('user_id', $idUsuario)
                ->whereYear('data_inicio', '=', $dataInicialPedidoAno)
                ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
                ->where('id', $request->id)
                ->count();


            //dd($verificaQuantidadeHospedagemMesAno); 

            $verificaQuantidadeHospedagemMesAnoCountTipo = \App\Hospede::where('user_id', $idUsuario)
                ->whereYear('data_inicio', '=', $dataInicialPedidoAno)
                ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
                ->whereIn('tipo_und_id', [11,12])
                ->count();

            
           
            $quantidadeReservas = \App\QuantidadeReserva::first();
            

            if ($is_altaTemporada == true) {


                $quantidadeReservas = \App\QuantidadeReserva::first();
                
                if($request->tipo == 12 or $request->tipo == 11){   


                    if($verificaQuantidadeHospedagemMesAnoCountTipo >= 2){


                        \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de 2 reservas para esse mês da alta temporada! Nas UH Camping e/ou Motor-Home", 'class'=>'danger']);
                        return redirect()->back();


                    }



                }else{


                    if($verificaQuantidadeHospedagemMesAnoCount >= $quantidadeReservas->reservas){

                        
                    
                        
                         if($verificaQuantidadeHospedagemMesAno == 0){

                            

                            \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de ". $quantidadeReservas->reservas ." reserva para esse mês da alta temporada!", 'class'=>'danger']);
                        return redirect()->back();

                        
                         }



                        











                    
                    }


                }

                //dd($verificaQuantidadeHospedagemMesAnoCountTipo);

                

            }else{
                //dd('baixa');
                if($request->tipo == 12 or $request->tipo == 11){

                    if($verificaQuantidadeHospedagemMesAnoCountTipo >= 2){




                        \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de 2 reservas para esse mês da baixa temporada! Nas UH Camping e/ou Motor-Home", 'class'=>'danger']);
                        return redirect()->back();


                    }




                }else{

                    if($verificaQuantidadeHospedagemMesAnoCount >= $quantidadeReservas->qnt_reservas_baixa_temporada){
                        //dd('entrou');

                        if($verificaQuantidadeHospedagemMesAno == 0){

                            

                            \Session::flash('message', ['msg'=>"Você alcançou o limite máximo de ". $quantidadeReservas->qnt_reservas_baixa_temporada ." reservas para esse mês da baixa temporada!", 'class'=>'danger']);
                            return redirect()->back();


                         }
                           

                    }

                }
                
            }      


            /*  
        


            VERIFICA SE JÁ TEM PEDIDO DE HOSPEDAGEM NAQUELE MêS  



            */




















































            ///////////////////////////////////////////////////////////////////////////////////////
            /* FUNCAO QUE BLOQUEIA A INSCRICAO ANTES DO DIA AGENDADO NOS MESES DE ALTA TEMPORADA */
            $diaBloqueado = \App\BloqueioDia::where('id', 1)->first();
            

            //$dataperidoinicial = substr($request->peridoinicial, 0, -13);
            $dataperidoinicial = new DateTime($dataperidoinicial);
            $dataperidoinicial = $dataperidoinicial->format('m');


            $datafinal = new DateTime($datafinal);
            //$datafinal = new DateTime($request->final);
            $datafinal = $datafinal->format('m');
            //dd($datafinal);


            $mes = date("m");
            $diaHoje = date("d");
            
            /*  PEDIDOS EM ALTA TEMPORADA ATÉ O DIA SALVO EX 10 DO MES */
            $ultimoMesdaAltaTemporada = $mesesAltaTemporada;
            $ultimoMesdaAltaTemporada = end($ultimoMesdaAltaTemporada);
            
            if($pedidoDentroAltaTemporada){
           
                // PEDIDOS REALIZADOS NO ULTIMO MES DA ALTA TEMPORADA SÓ SERÃO ACEITOS PARA O PROXIMO MES
                if($ultimoMesdaAltaTemporada == $mes){

                    
                    if($dataperidoinicial == $ultimoMesdaAltaTemporada or $datafinal == $ultimoMesdaAltaTemporada ){
                       // \Session::flash('message', ['msg'=>"Pedidos aceitos somente para o próximo mês.", 'class'=>'danger']);
                       //     return redirect()->back();
                        }
                
                }
             
            }             
            /*  PEDIDOS EM ALTA TEMPORADA ATÉ O DIA SALVO EX 10 DO MES */

            
            /* VERIFICA PEDIDOS DENTRO DO MES ANTERIOR AO PRIMEIRO MES DA ALTA TEMPORADA EXEMPLO MES NOVEMBRO */
            $primeiroMesdaAltaTemporada = $mesesAltaTemporada;
            $primeiroMesdaAltaTemporada = current($primeiroMesdaAltaTemporada);
            
            //dd($mes);
            /*
            if($mes == $mesAnteriorAltaTemporadaInicio){
         
                if($diaHoje > $diaBloqueado->dia){
                                 
                    if($dataperidoinicial == $primeiroMesdaAltaTemporada or $datafinal == $primeiroMesdaAltaTemporada){
                    
                        \Session::flash('message', ['msg'=>"Inscrições encerradas para o próximo mês.", 'class'=>'danger']);
                        return redirect()->back();
                
                    }

                }

            }
            */
            /* VERIFICA PEDIDOS DENTRO DO MES ANTERIOR AO PRIMEIRO MES DA ALTA TEMPORADA EXEMPLO MES NOVEMBRO */


            /* VERIFICA PEDIDOS DENTRO DO MES 12 */
            if($mes == $primeiroMesdaAltaTemporada){
            
                if($diaHoje > $diaBloqueado->dia){
       
                     // \Session::flash('message', ['msg'=>"Inscrições encerradas dia ". $diaBloqueado->dia ." do mês!", 'class'=>'danger']);
                      //  return redirect()->back();
                                       
                   
                } 

                if($dataperidoinicial == $primeiroMesdaAltaTemporada or $datafinal == $primeiroMesdaAltaTemporada){
                  
                    //    \Session::flash('message', ['msg'=>"Inscrições encerradas dia ". $diaBloqueado->dia ." do mês!", 'class'=>'danger']);
                     //   return redirect()->back();
                
                        }                  
            }
            /* VERIFICA PEDIDOS DENTRO DO MES 12 */
            /* FIM DA FUNCAO*/





            /*  VERIFICA SE JÁ TEM PEDIDO DE HOSPEDAGEM NAQUELE MêS  */
            //$dataInicialPedido = new DateTime($request->peridoinicial);
            $dataInicialPedido1 = substr($request->peridoinicial, 0, -13);
            $dataInicialPedido = new DateTime($dataInicialPedido1);
            //$dataInicialPedido = $dataInicialPedido->format('Y-m-d');
            $dataInicialPedidoAno = $dataInicialPedido->format('Y');
            $dataInicialPedidoMes = $dataInicialPedido->format('m');

            $dataFinalPedido1 = substr($request->peridoinicial, -10);
            $dataFinalPedido = new DateTime($dataFinalPedido1);
            //$dataFinalPedido = $dataFinalPedido->format('Y-m-d');
            //dd($dataFinalPedido);
            $dataFinalPedidoAno = $dataFinalPedido->format('Y');
            $dataFinalPedidoMes = $dataFinalPedido->format('m');


            $idUsusario = Auth::user()->id;
           
            
            /*
            $verificaQuantidadeHospedagemMesAnoCount = \App\Hospede::where('user_id', $idUsusario)
            ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
            ->count();
            */

          
            
           

       

            $verificaQuantidadeHospedagemMesAnoCount = \App\Hospede::where('user_id', $idUsusario)
                ->whereYear('data_inicio', '=', $dataInicialPedidoAno)
                 ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
                 ->count();

       
            $quantidadeReservas = \App\QuantidadeReserva::first();
            //dd($quantidadeReservas->reservas);

            if ($is_altaTemporada == true) {
            
                 $quantidadeReservas = \App\QuantidadeReserva::first();
                if($verificaQuantidadeHospedagemMesAnoCount >= $quantidadeReservas->reservas){

                //\Session::flash('message', ['msg'=>"Você alcançou o limite máxima de ". $quantidadeReservas->reservas ." reservas para esse mês da alta temporada!", 'class'=>'danger']);
                //return redirect()->back();

                }

            }else{
                
                 if($verificaQuantidadeHospedagemMesAnoCount >= $quantidadeReservas->qnt_reservas_baixa_temporada){
                    //dd('entrou');
                //\Session::flash('message', ['msg'=>"Você alcançou o limite máximo de ". $quantidadeReservas->qnt_reservas_baixa_temporada ." reservas para esse mês da baixa temporada!", 'class'=>'danger']);
               // return redirect()->back();

                }
            }      

            //$verificaCount = \App\Hospede::where('und_habitacionais_id', 6)
            //->whereDate('data_inicio', '<=', $dataInicialPedido)
            //->whereDate('data_termino', '>=', $dataFinalPedido)

            //whereBetween('data_inicio', [$dataInicialPedido, $dataFinalPedido])
            //->orWhereBetween('data_termino', [$dataInicialPedido, $dataFinalPedido])
            //->where('user_id', $idUsusario)
            //->get();
            //dd($verificaQuantidadeHospedagemMesAnoCount);
            
            












            /*  

            Reservation::whereBetween('reservation_from', [$from1, $to1])
            ->orWhereBetween('reservation_to', [$from2, $to2])
            ->whereNotBetween('reservation_to', [$from3, $to3])
            ->get();
        


            VERIFICA SE JÁ TEM PEDIDO DE HOSPEDAGEM NAQUELE MêS  


            */
           



        

        $inicial1 = substr($request->peridoinicial, 0, -13);
        $inicial = $inicial1;
        //$inicial = $request->peridoinicial;

        $final1 = substr($request->peridoinicial, -10);
        $final = $final1;
        //$final = $request->final;
        //dd($final);


        $inicialData = strtotime($inicial);
        $finalData = strtotime($final);
        
         if ($inicialData === $finalData) {
             \Session::flash('message', ['msg'=>"Data Inicial igual Data final! Escolha outra Data!", 'class'=>'danger']);
           return redirect()->back();
        }
         if ($inicialData > $finalData) {
             \Session::flash('message', ['msg'=>"Data Final maior que data Inicial! Escolha outra Data!", 'class'=>'danger']);
           return redirect()->back();
        }
        if ($inicial == "" or $final == "") {
             \Session::flash('message', ['msg'=>"Data vazia!", 'class'=>'danger']);
           return redirect()->back();
        }

        //dd($final);
        
        $mesInicial = new DateTime($inicial);
        $mesFinal = new DateTime($final);
        
        
  
        $mesInicial = $mesInicial->format('m');
        $mesFinal = $mesFinal->format('m');
 


        
        /*    Verifica o Proximo Mês     */
        $mesAtual = date("m");
        //$mesAtual = "11";
        
 
        if($mesAtual == 11){
            $proximoMes = "01";
            $proximoMes1 = "01";
 


        }
        
        if($mesAtual == 12){
            $proximoMes = "02";
            $proximoMes1 = "02";
 

        }

        if($mesAtual >= 1 and $mesAtual <= 10){
   
             $proximoMes = $mesAtual + 1;
    
            $proximoMes1 = $mesAtual + 1;
            if($mesAtual == 10){
                $proximoMes1 = "11";
            }
            if($mesAtual == 9){
                $proximoMes1 = "10";
            }
            
            if($proximoMes <= 9){
                    $proximoMes = '0' . $proximoMes;
                    $proximoMes1 = '0' .$proximoMes1;

            }
            
        }
        //dd($proximoMes);

        //$mesAtual = date("m");
        //$mesAtual = 11;
        //$mesBloqueado = $mesAtual + 2;
        //dd($mesBloqueado);
        /*
        if($mesInicial != $mesAtual and $mesInicial != $proximoMes){
            dd($proximoMes);
            \Session::flash('message', ['msg'=>"Data Inicial fora do período permitido!", 'class'=>'danger']);
                return redirect()->back();
        }

        if($mesFinal != $mesAtual and $mesFinal != $proximoMes){
            dd("teste1");
            \Session::flash('message', ['msg'=>"Data Final fora do período permitido!", 'class'=>'danger']);
                return redirect()->back();
        }
        /*    Verifica o Proximo Mês      */

            
        //dd('break');

        $datework = Carbon::createFromDate($inicial);
        $diasHospedagem = $datework->diffInDays($final);


        $pegaTemporadaAlta = \App\Temporada::where('tipo_temporada_id', 1)->first();
        $pegaTemporadaBaixa = \App\Temporada::where('tipo_temporada_id', 2)->first();


        $dataTemporadaAltaInicio = strtotime($pegaTemporadaAlta->data_inicio);
        $dataTemporadaAltaTermino = strtotime($pegaTemporadaAlta->data_termino);
       

        $dataTemporadaBaixaInicio = strtotime($pegaTemporadaBaixa->data_inicio);
        $dataTemporadaBaixaTermino = strtotime($pegaTemporadaBaixa->data_termino);

       
        $data = new \DateTime($inicial);
       
        
        $pi1 = strtotime($inicial);
        $pf1 = strtotime($final);

        $a[] = $inicial;

        $diasAltaTemporada = 0;
        $diasBaixaTemporada = 0;
        for ($i=0; $i < $diasHospedagem; $i++) { 
        
        $data1 = $data->add(new \DateInterval('P1D'));
        $data2 = $data1->format('Y-m-d');
        
        
        if ($dataTemporadaAltaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaAltaTermino) {
            $diasAltaTemporada = $diasAltaTemporada + 1;
           

        }elseif($dataTemporadaBaixaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaBaixaTermino) {
            $diasBaixaTemporada = $diasBaixaTemporada + 1;
           
        }else{

            // QUANDO O ADMINISTRADOR NAO ATUALIZA A DATA DE TEMPORADA ELE RETORNA ESSE ERRO , QUANDO O USUARIO TENTA REALIZAR A INSCRICAO!
                \Session::flash('message', ['msg'=>"Temporada não definida! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }


        $a[] = $data2;
       
        }
       
        /* VERIFICA SE HA UNIDADE TEM GRUPO CADASTRADO E RETORNA ERRO*/
        $grupoTarifa = \App\GrupoTarifa::where('unidade_habitacional_id', $request->tipo)->with('postos')->get();
        if($grupoTarifa->count() == 0){
            \Session::flash('message', ['msg'=>"Unidade Habitacional sem Grupo cadastrado! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }
        /* VERIFICA SE HA UNIDADE TEM GRUPO CADASRADO E RETORNA ERRO*/
        
            
       

        /* SE TIVER GRUPO CADASTRADO FAS O LOOPING VERIFICANDO QUAL GRUPO TEM QUAIS POSTOS */
        foreach ($grupoTarifa as $key => $value) {

            foreach ($value->postos as $posto) {
                            
                
                if($posto->id === Auth::user()->postograd_id){
                    

                    $tarifa = \App\Tarifas::where('tipoundhab_id', $request->tipo)
                    ->where('grupo_destinacao_id', $posto->pivot->grupotarifa_id)
                    ->first();

                  
                }
            }
            
        }
        /* SE TIVER GRUPO CADASTRADO FAS O LOOPING VERIFICANDO QUAL GRUPO TEM QUAIS POSTOS */



       
        //SE TIVER NENHUM POSTO SEM GRUPO CADASTRADO ELE RETORNA O USUARIO COM ESSA MENSAGEM ABAIXO
        if (empty($tarifa)) {
            
            \Session::flash('message', ['msg'=>"Posto sem grupo tarifa Cadastrado! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }
        /* SE TIVER GRUPO CADASTRADO FAS O LOOPING VERIFICANDO QUAL GRUPO*/
        
       


        /* FAZ OS CALCULOS DAS DIARIAS */
        $calculaDiariaAlta = 0;
        if($diasAltaTemporada > 0){
            $calculaDiariaAlta = $diasAltaTemporada * $tarifa->valor;
            $totalValor = $calculaDiariaAlta;
           
        }

        $calculaDiariaBaixa = 0;
        if($diasBaixaTemporada > 0){
            
            $calculaDiariaBaixa = $diasBaixaTemporada * $tarifa->valor_baixa;
            $totalValor = $calculaDiariaBaixa;
            
        }  
        
        if($calculaDiariaAlta > 0 and $calculaDiariaBaixa > 0){
            $totalValor = $calculaDiariaAlta + $calculaDiariaBaixa;
        }
        /* FAZ OS CALCULOS DAS DIARIAS */


       if($is_altaTemporada == true){
         $valorTarifa = $tarifa->valor;
   
       }else{
         $valorTarifa = $tarifa->valor_baixa;
       }
       // FIM DIAS HOSPEDAGEM

     
       
       $tarifas = \App\Tarifas::where('tipoundhab_id', $request->tipo)->get();

       $tipos = \App\TipoUndHab::all();
       $consulta = $request;
       
       //dd($totalValor);

       $totalValor = Crypt::encrypt($totalValor);
      

        return view('hospedagem.confirmarantigoEdit', compact('consulta', 'tipos', 'diasHospedagem', 'calculaDiariaAlta', 'calculaDiariaBaixa', 'totalValor', 'valorTarifa'));







    }
    public function solicitarinscricaoEditConfirmar2(Request $request){


            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///////////////////   FUNÇÃO QUE PEGA O PRIMEIRO MES DA ALTA TEMPORADA E O ULTIMO MES ////////////////////////////
            $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
            $baixaTemporada = \App\Temporada::where('tipo_temporada_id', 2)->first();
            
            $altaTemporadaInicio = new DateTime($altaTemporada->data_inicio);
            $altaTemporadaTermino = new DateTime($altaTemporada->data_termino);
            
            
            $mesAnteriorAltaTemporadaInicio = new DateTime($altaTemporadaInicio->format('Y-m-d'));
            $mesAnteriorAltaTemporadaInicio = $mesAnteriorAltaTemporadaInicio->modify('last day of last month');
            $mesAnteriorAltaTemporadaInicio = $mesAnteriorAltaTemporadaInicio->format('m');
       
             
            
            $interval = $altaTemporadaInicio->diff($altaTemporadaTermino);

              

            // MONTA UM ARRAY COM OS MESES DA ALTA TEMPORADA
            $mesesAltaTemporada[] = $altaTemporadaInicio->format('m');       
            for ($i=0; $i < $interval->m; $i++) {

                    $altaTemporadaInicio->add(new \DateInterval('P1M'));                 
                    $mesesAltaTemporada[] = $altaTemporadaInicio->format('m');
                
                   
            }
            ///////////////////   FUNÇÃO QUE PEGA O PRIMEIRO MES DA ALTA TEMPORADA E O ULTIMO MES ////////////////////////////

             
           



           

            /*  VERIFICA SE É ALTA TEMPORADA */
            $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();
            $baixaTemporada = \App\Temporada::where('tipo_temporada_id', 2)->first();

           
            $altaTemporadaInicio1 = strtotime($altaTemporada->data_inicio);
            $altaTemporadaTermino1 = strtotime($altaTemporada->data_termino);

            $baixaTemporadaInicio1 = strtotime($baixaTemporada->data_inicio);
            $baixaTemporadaTermino1 = strtotime($baixaTemporada->data_termino);

            
            $dataperidoinicial = new DateTime($request->peridoinicial);
            $dataperidoinicial = $dataperidoinicial->format('Y-m-d');
            $dataperidoinicialToTime = strtotime($dataperidoinicial);

            $datafinal = new DateTime($request->final);
            $datafinal = $datafinal->format('Y-m-d');
            $datafinalToTime = strtotime($datafinal);


            $dataPedido = date("Y-m-d");
            $dataPedido = strtotime($dataPedido);
            


           if ($dataperidoinicialToTime >= $altaTemporadaInicio1 and $dataperidoinicialToTime <= $altaTemporadaTermino1) {
                $is_altaTemporada = true;
               
                
            
            }elseif($datafinalToTime >= $altaTemporadaInicio1 and $datafinalToTime <= $altaTemporadaTermino1) {
                $is_altaTemporada = true;
               

            }else{
                $is_altaTemporada = false;
            }
            

            // VERIFICA SE A DATA DO PEDIDO ESTA NA ALTA TEMPORADA
            if($dataPedido >= $altaTemporadaInicio1 and $dataPedido <= $altaTemporadaTermino1){
                $pedidoDentroAltaTemporada = true;
            }else{
                $pedidoDentroAltaTemporada = false;
            }

             
            // VERIFICA SE A DATA DO PEDIDO ESTA NA ALTA TEMPORADA 
            



            /*  VERIFICA SE É ALTA TEMPORADA */

         



            ///////////////////////////////////////////////////////////////////////////////////////
            /* FUNCAO QUE BLOQUEIA A INSCRICAO ANTES DO DIA AGENDADO NOS MESES DE ALTA TEMPORADA */
            $diaBloqueado = \App\BloqueioDia::where('id', 1)->first();
            
            $dataperidoinicial = new DateTime($request->peridoinicial);
            $dataperidoinicial = $dataperidoinicial->format('m');

            $datafinal = new DateTime($request->final);
            $datafinal = $datafinal->format('m');


            $mes = date("m");
            $diaHoje = date("d");
            


       
            /*  PEDIDOS EM ALTA TEMPORADA ATÉ O DIA SALVO EX 10 DO MES */
            $ultimoMesdaAltaTemporada = $mesesAltaTemporada;
            $ultimoMesdaAltaTemporada = end($ultimoMesdaAltaTemporada);
            
            if($pedidoDentroAltaTemporada){
           
                // PEDIDOS REALIZADOS NO ULTIMO MES DA ALTA TEMPORADA SÓ SERÃO ACEITOS PARA O PROXIMO MES
                if($ultimoMesdaAltaTemporada == $mes){

                    
                    if($dataperidoinicial == $ultimoMesdaAltaTemporada or $datafinal == $ultimoMesdaAltaTemporada ){
                        \Session::flash('message', ['msg'=>"Pedidos aceitos somente para o próximo mês.", 'class'=>'danger']);
                            return redirect()->back();
                        }
                
                }
             
            }             
            /*  PEDIDOS EM ALTA TEMPORADA ATÉ O DIA SALVO EX 10 DO MES */

            
            /* VERIFICA PEDIDOS DENTRO DO MES ANTERIOR AO PRIMEIRO MES DA ALTA TEMPORADA EXEMPLO MES NOVEMBRO */
            $primeiroMesdaAltaTemporada = $mesesAltaTemporada;
            $primeiroMesdaAltaTemporada = current($primeiroMesdaAltaTemporada);

            if($mes == $mesAnteriorAltaTemporadaInicio){
         
                if($diaHoje > $diaBloqueado->dia){
                                 
                    if($dataperidoinicial == $primeiroMesdaAltaTemporada or $datafinal == $primeiroMesdaAltaTemporada){
                    
                        //\Session::flash('message', ['msg'=>"Inscrições encerradas para o próximo mês.", 'class'=>'danger']);
                        //return redirect()->back();
                
                    }

                }

            }
            /* VERIFICA PEDIDOS DENTRO DO MES ANTERIOR AO PRIMEIRO MES DA ALTA TEMPORADA EXEMPLO MES NOVEMBRO */


            /* VERIFICA PEDIDOS DENTRO DO MES 12 */
            if($mes == $primeiroMesdaAltaTemporada){
            
                if($diaHoje > $diaBloqueado->dia){
       
                      //\Session::flash('message', ['msg'=>"Inscrições encerradas dia ". $diaBloqueado->dia ." do mês!", 'class'=>'danger']);
                      //  return redirect()->back();
                                       
                   
                } 

                if($dataperidoinicial == $primeiroMesdaAltaTemporada or $datafinal == $primeiroMesdaAltaTemporada){
                  
                       // \Session::flash('message', ['msg'=>"Inscrições encerradas dia ". $diaBloqueado->dia ." do mês!", 'class'=>'danger']);
                        //return redirect()->back();
                
                        }                  
            }
            /* VERIFICA PEDIDOS DENTRO DO MES 12 */
            /* FIM DA FUNCAO*/





            /*  VERIFICA SE JÁ TEM PEDIDO DE HOSPEDAGEM NAQUELE MêS  */
            $dataInicialPedido = new DateTime($request->peridoinicial);
            $dataInicialPedidoAno = $dataInicialPedido->format('Y');
            $dataInicialPedidoMes = $dataInicialPedido->format('m');

            $dataFinalPedido = new DateTime($request->final);
           
            $dataFinalPedidoAno = $dataFinalPedido->format('Y');
            $dataFinalPedidoMes = $dataFinalPedido->format('m');


            $idUsusario = Auth::user()->id;
           
       

            $verificaQuantidadeHospedagemMesAnoCount = \App\Hospede::where('user_id', $idUsusario)
                ->whereYear('data_inicio', '=', $dataInicialPedidoAno)
                 ->whereMonth('data_inicio', '=', $dataInicialPedidoMes)
                 ->count();

       

            $quantidadeReservas = \App\QuantidadeReserva::first();
            if ($is_altaTemporada == true) {
            
                 $quantidadeReservas = \App\QuantidadeReserva::first();
                if($verificaQuantidadeHospedagemMesAnoCount >= $quantidadeReservas->reservas){

                //\Session::flash('message', ['msg'=>"Você alcançou o limite máxima de ". $quantidadeReservas->reservas ." reservas para esse mês da alta temporada!", 'class'=>'danger']);
                //return redirect()->back();

                }

            }else{

                 if($verificaQuantidadeHospedagemMesAnoCount >= $quantidadeReservas->qnt_reservas_baixa_temporada){

                //\Session::flash('message', ['msg'=>"Você alcançou o limite máximo de ". $quantidadeReservas->qnt_reservas_baixa_temporada ." reservas para esse mês da baixa temporada!", 'class'=>'danger']);
                //return redirect()->back();

                }
            }
        

        


        $inicial = $request->peridoinicial;
        $final = $request->final;
        $inicialData = strtotime($inicial);
        $finalData = strtotime($final);
        
        //dd($inicialData);
        if ($inicialData === $finalData) {
             \Session::flash('message', ['msg'=>"Data Inicial igual Data final! Escolha outra Data!", 'class'=>'danger']);
           return redirect()->back();
        }
         if ($inicialData > $finalData) {
             \Session::flash('message', ['msg'=>"Data Final maior que data Inicial! Escolha outra Data!", 'class'=>'danger']);
           return redirect()->back();
        }
        if ($inicial == "" or $final == "") {
             \Session::flash('message', ['msg'=>"Data vazia!", 'class'=>'danger']);
           return redirect()->back();
        }

        //$pi = Carbon::createFromFormat('d/m/Y', $inicial)->format('Y-m-d');    
        $pi = Carbon::createFromFormat('d-m-Y', $inicial)->format('Y-m-d');    
        //$pf = Carbon::createFromFormat('d/m/Y', $final)->format('Y-m-d');
        $pf = Carbon::createFromFormat('d-m-Y', $final)->format('Y-m-d');
        
        $mesInicial = new DateTime($pi);
        $mesFinal = new DateTime($pf);
        
        
        //$mesAnterior = new DateInterval("P1M"); 
        //$mesProximo = new DateInterval('P1M'); 
       
        
        //$dia15->add($mesProximo);
        $mesInicial = $mesInicial->format('m');
        $mesFinal = $mesFinal->format('m');
        //dd($mesFinal);


        
        /*    Verifica o Proximo Mês         */
        $mesAtual = date("m");
        
        //$mesAtual = 1;
        if($mesAtual == 11){
            $proximoMes = "01";
            $proximoMes1 = "01";
            //dd('entrou 11');


        }
        
        if($mesAtual == 12){
            $proximoMes = "02";
            $proximoMes1 = "02";
            //dd('entrou 12');
            
        }

        if($mesAtual >= 1 and $mesAtual <= 10){
            //$proximoMes = Carbon::now()->addMonths(1)->format('Y-m-01');
            $proximoMes = $mesAtual + 1;
            //dd("teste");
            $proximoMes1 = $mesAtual + 1;
            if($mesAtual == 10){
                $proximoMes1 = "11";
            }
            if($mesAtual == 9){
                $proximoMes1 = "10";
            }
            
            if($proximoMes <= 9){
                    $proximoMes = '0' . $proximoMes;
                    $proximoMes1 = '0' .$proximoMes1;

            }
            
        }
        
        $mesAtual = date("m");
        $mesBloqueado = $mesAtual + 2;
        //dd($proximoMes);

        if($mesInicial != $mesAtual and $mesInicial != $proximoMes){
            //dd("teste");
            \Session::flash('message', ['msg'=>"Data Inicial fora do período permitido!", 'class'=>'danger']);
                return redirect()->back();
        }
        if($mesFinal != $mesAtual and $mesFinal != $proximoMes){
            //dd("teste1");
            \Session::flash('message', ['msg'=>"Data Final fora do período permitido!", 'class'=>'danger']);
                return redirect()->back();
        }
        /*    Verifica o Proximo Mês         */

            
        

        $datework = Carbon::createFromDate($pi);
        $diasHospedagem = $datework->diffInDays($pf);


        $pegaTemporadaAlta = \App\Temporada::where('tipo_temporada_id', 1)->first();
        $pegaTemporadaBaixa = \App\Temporada::where('tipo_temporada_id', 2)->first();


        $dataTemporadaAltaInicio = strtotime($pegaTemporadaAlta->data_inicio);
        $dataTemporadaAltaTermino = strtotime($pegaTemporadaAlta->data_termino);
        
        $dataTemporadaBaixaInicio = strtotime($pegaTemporadaBaixa->data_inicio);
        $dataTemporadaBaixaTermino = strtotime($pegaTemporadaBaixa->data_termino);


       
        $data = new \DateTime($pi);
       
        
        $pi1 = strtotime($pi);
        $pf1 = strtotime($pf);

        $a[] = $pi;

        $diasAltaTemporada = 0;
        $diasBaixaTemporada = 0;
        for ($i=0; $i < $diasHospedagem; $i++) { 
        
        $data1 = $data->add(new \DateInterval('P1D'));
        $data2 = $data1->format('Y-m-d');
        
        
        if ($dataTemporadaAltaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaAltaTermino) {
            $diasAltaTemporada = $diasAltaTemporada + 1;
           

        }elseif($dataTemporadaBaixaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaBaixaTermino) {
            $diasBaixaTemporada = $diasBaixaTemporada + 1;
           
        }else{

            // QUANDO O ADMINISTRADOR NAO ATUALIZA A DATA DE TEMPORADA ELE RETORNA ESSE ERRO , QUANDO O USUARIO TENTA REALIZAR A INSCRICAO!
                \Session::flash('message', ['msg'=>"Temporada não definida! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }


        $a[] = $data2;
       
        }

       

        $grupoTarifa = \App\GrupoTarifa::where('unidade_habitacional_id', $request->tipo)->with('postos')->get();
        
        
        if($grupoTarifa->count() == 0){
            \Session::flash('message', ['msg'=>"Unidade Habitacional sem Grupo cadastrado! Favor Contactar o Administrador!", 'class'=>'danger']);
                return redirect()->back();
        }
        
       


        foreach ($grupoTarifa as $key => $value) {

            foreach ($value->postos as $posto) {
                            
                
                if($posto->id === Auth::user()->postograd_id){
                    

                    $tarifa = \App\Tarifas::where('tipoundhab_id', $request->tipo)
                    ->where('grupo_destinacao_id', $posto->pivot->grupotarifa_id)
                    ->first();

                  
                }
            }
            
        }
        if($is_altaTemporada == true){
         $valorTarifa = $tarifa->valor;
   
       }else{
         $valorTarifa = $tarifa->valor_baixa;
       }
       
        
        if (empty($tarifa)) {
            
            \Session::flash('message', ['msg'=>"Posto sem grupo tarifa Cadastrado! Favor Contactar o Administrador!", 'class'=>'danger']);
            return redirect()->back();
        }
        
       


        // CALCULA DIARIAS
        $calculaDiariaAlta = 0;
        if($diasAltaTemporada > 0){
            $calculaDiariaAlta = $diasAltaTemporada * $tarifa->valor;
            $totalValor = $calculaDiariaAlta;
           
        }

        $calculaDiariaBaixa = 0;
        if($diasBaixaTemporada > 0){
            
            $calculaDiariaBaixa = $diasBaixaTemporada * $tarifa->valor_baixa;
            $totalValor = $calculaDiariaBaixa;
            
        }  
        
        if($calculaDiariaAlta > 0 and $calculaDiariaBaixa > 0){
            $totalValor = $calculaDiariaAlta + $calculaDiariaBaixa;
        }

        //FIM DIARIAS





        // FIM DIAS HOSPEDAGEM

        
       

        $tarifas = \App\Tarifas::where('tipoundhab_id', $request->tipo)->get();


        $tipos = \App\TipoUndHab::all();
        $consulta = $request;
        

        

        //$valorTarifa = "0";

        $totalValor = Crypt::encrypt($totalValor);

        return view('hospedagem.confirmarantigo', compact('consulta', 'tipos', 'diasHospedagem', 'calculaDiariaAlta', 'calculaDiariaBaixa', 'totalValor', 'valorTarifa'));

    }

    public function meuspedidos(){

       
        $consulta = \App\Hospede::where('user_id', Auth::user()->id)
        ->orderBy('id', 'DESC')
        ->with('tipouh')
        ->paginate(20);
        
        //dd($consulta);

        return view('meuspedidos.index', compact('consulta'));

    }


public function meuspedido($id)
    {
    date_default_timezone_set('America/Sao_Paulo');

    $id = Crypt::decrypt($id);

    $hospedagem = \App\Hospede::with('user')->findOrFail($id);

    $cancelar = Auth::user()->id === $hospedagem->user->id ? 1 : 0;

    $comprovante = \App\Comprovante::where('hospedagem_id', $hospedagem->id)
        ->orderBy('created_at', 'desc')
        ->first();

    $arquivo = isset($comprovante->arquivo) ? $comprovante->arquivo : "";

    $unidades_habitacionais = \App\UnidadeHabitacional::where('disponivel', 1)->get();

    $horario = \App\Horario::first();

    $hoje = Carbon::now()->format('Y-m-d');

    $CheckInAntecipado = false;
    $CheckOutAtrasado = false;
    $valorPagarRestante = 0;

    if ($hospedagem->checkin_at != null && $hospedagem->checkin == 1) {

        $calculoService = new \App\Services\CalculoHospedagemService();

        $calculo = $calculoService->calcular($hospedagem);

        $CheckInAntecipado = $calculo['checkin_antecipado'];
        $CheckOutAtrasado = $calculo['checkout_atrasado'];
        $valorPagarRestante = $calculo['valor_restante'];

        $hospedagem->valor_restante = $calculo['valor_restante'];
        $hospedagem->qntdiarias = $calculo['dias'];
        $hospedagem->valor = $calculo['valor_total'];
        $hospedagem->update();
    }

    return view('meuspedidos.meu_pedido', compact(
        'CheckInAntecipado',
        'CheckOutAtrasado',
        'hospedagem',
        'unidades_habitacionais',
        'horario',
        'comprovante',
        'arquivo',
        'cancelar',
        'hoje',
        'valorPagarRestante'
    ));
}
    public function meuspedido_OLD($id){



        date_default_timezone_set('America/Sao_Paulo');
        $id = Crypt::decrypt($id);

        $hospedagem = \App\Hospede::where('id', $id)->with('user')->first();

       // dd($hospedagem);

        if (Auth::user()->id === $hospedagem->user->id){
            $cancelar = 1;
        }else{
            $cancelar = 0;
        }
        if($hospedagem == null){
            \Session::flash('message', ['msg'=>"Hospedagem não encontrada!", 'class'=>'danger']);
            return redirect()->back();       
        }
        //dd($hospedagem->undHB->classe->classe);

        $comprovante = \App\Comprovante::where('hospedagem_id', $hospedagem->id)->orderBy('created_at', 'desc')->first();

        if(isset($comprovante->arquivo)){
            $arquivo = $comprovante->arquivo;
        }else{
            $arquivo = "";
        }
    
        $unidades_habitacionais = \App\UnidadeHabitacional::where('disponivel', 1)
        ->get();
        $horario = \App\Horario::first();       
        

        $hoje = Carbon::now()->format('Y-m-d');
        $data_fim = new DateTime($hoje);

        if($hospedagem->checkin_at == null){

            $DataCheckin = Carbon::parse($hospedagem->data_inicio)->format('Y-m-d');
            $DataTermino = Carbon::parse($hospedagem->data_termino)->format('Y-m-d');
            $data_inicio = new DateTime($DataCheckin);
            $data_fim = new DateTime($DataTermino);
            $dateInterval = $data_inicio->diff($data_fim);
            $dias = $dateInterval->days;

        }else{

            $DataCheckin = Carbon::parse($hospedagem->checkin_at)->format('Y-m-d');
            $data_inicio = new DateTime($DataCheckin);
            $dateInterval = $data_inicio->diff($data_fim);
            $dias = $dateInterval->days;

        
        }

        //dd($dias);


        // CALCULA VALOR RESTANTE
        $ValorTarifa = $hospedagem->valortarifa; 
        $ValorPago = $hospedagem->valor_pago; 
        $valorResto = $hospedagem->valor_restante;
        
        $dataInicioPedido = Carbon::parse($hospedagem->data_inicio)->format('Y-m-d');
        $dataFimPedido = Carbon::parse($hospedagem->data_termino)->format('Y-m-d');
        $dataCheckInParaCobrar = Carbon::parse($hospedagem->checkin_at)->format('Y-m-d');
        


            $dataCheckIn = $hospedagem->checkin_at;
            $dataCheckIn = new DateTime($dataCheckIn);
            $dataCheckIna       =   $dataCheckIn->format('H:i:s');
            //dd($dataCheckIna);
            $dataCheckInToTime  =   strtotime($dataCheckIna);
           
            $hojeHoras = date("H:i:s");
            
            $hojeHorasToTime = strtotime($hojeHoras);

            $horarios = \App\Horario::first();

            $HorarioEntrada = $horarios->entrada;
            $HorarioEntrada = new DateTime($HorarioEntrada);
            $HorarioEntradaTolerancia = $HorarioEntrada->sub(new DateInterval('PT'.$horarios->tolerancia.'H'));
            $HorarioEntradaTolerancia = $HorarioEntrada->format('H:i:s');

            $HorarioEntradaToleranciaToTime = strtotime($HorarioEntradaTolerancia);
            //dd($HorarioEntradaToleranciaToTime);
            

            $HorarioEntradaSEMTolerancia = new DateTime($horarios->entrada);
            $HorarioEntradaSEMTolerancia = $HorarioEntradaSEMTolerancia->format('H:i:s');
            //dd($HorarioEntradaSEMTolerancia);
            $HorarioEntradaSEMToleranciaToTime = strtotime($HorarioEntradaSEMTolerancia);


            $HorarioSaida = $horarios->saida;
            $HorarioSaida = new DateTime($HorarioSaida);
            $HorarioSaidaTolerancia = $HorarioSaida->add(new DateInterval('PT'.$horarios->tolerancia.'H'));           
            $HorarioSaidaTolerancia = $HorarioSaida->format('H:i:s');
            $HorarioSaidaToleranciaToTime = strtotime($HorarioSaidaTolerancia);


            $HorarioSaidaSEMTolerancia = new DateTime($horarios->saida);
            $HorarioSaidaSEMTolerancia = $HorarioSaidaSEMTolerancia->format('H:i:s');
            //dd($HorarioSaidaSEMTolerancia);
            $HorarioSaidaSEMTolerancia = strtotime($HorarioSaidaSEMTolerancia);

            $restante = 0;

        

       
        if($ValorPago == null){

        $valorPagarRestante = $ValorTarifa * $dias; 
        //dd($dias);
        //$CheckOutAtrasado = false;
        
        }else{
        

            // SE TIVER UMA DIARIA ELE ZERA
            if($dias == 1){
                //if($dias > 1){
                //dd('ok');
                $valorPagarRestante = $ValorTarifa * $dias;
                $valorPagarRestante = $valorPagarRestante - $ValorPago;

            }else{
            //$valorPagarRestante = $ValorTarifa;
            }


            $valorPagarRestante = 0;
            $valorPagarRestante = $ValorTarifa * $dias;
            $valorPagarRestante = $valorPagarRestante - $ValorPago;
            
        }
        

        if($dias == 0){
            $valorPagarRestante = 0;
        }
        
        $CheckOutAtrasado = false;
        if($hospedagem->checkin_at != null){
        if(strtotime($dataInicioPedido) < strtotime($hoje) and strtotime($dataFimPedido) > strtotime($hoje)){              
                
                //DENTRO DO PERIODO DE RESERVA NAO COBRA TAXA
                $CheckOutAtrasado = false;
                
                
                 if($hojeHorasToTime > $HorarioSaidaToleranciaToTime) {
                    //COBRAR MAIS UMA DIÁRIA
                    $restante = $restante + $hospedagem->valortarifa;
                    $dias = $dias + 1;
        
                }





        }else{
                
                //FORA DO PERIODO DE RESERVA COBRA TAXA
                $CheckOutAtrasado = false;
                
                 if($hojeHorasToTime > $HorarioSaidaToleranciaToTime) {
                    //COBRAR MAIS UMA DIÁRIA
                    $CheckOutAtrasado = true;
                    $restante = $restante + $hospedagem->valortarifa;
                    $dias = $dias + 1;
        
                }

        }
        }

                
        $HoraMeiaNoite = "00:00:00";
        $HoraMeiaNoite = new DateTime($HoraMeiaNoite);
        $HoraMeiaNoite = $HoraMeiaNoite->format('H:i:s');
        $HoraMeiaNoite = strtotime($HoraMeiaNoite);


        $CheckInAntecipado = false;
        if($hospedagem->checkin_at != null){

        // SE INICIO DO CHECK IN ESTA DENTRO DO PERIODO CADASTRADO    
        if(strtotime($dataInicioPedido) < strtotime($dataCheckInParaCobrar) and strtotime($dataFimPedido) > strtotime($dataCheckInParaCobrar)){

                   $CheckInAntecipado = false;
                   // COBRA UMA DIARIA ANTERIORHorarioEntradaToleranciaToTime
                   if($HoraMeiaNoite <= $dataCheckInToTime and $dataCheckInToTime <= $HorarioEntradaToleranciaToTime){
                        $restante = $restante + $hospedagem->valortarifa;
                        $dias = $dias + 1;
                    }else{

                    }


        }else{
 



                    if(strtotime($dataFimPedido) <= strtotime($dataCheckInParaCobrar)){
                    }
                        $CheckInAntecipado = false;
                        //SE ENTRAR ANTES DO HORARIO ADICIONA UM DIA ENTRADA   
                
                    if($dataCheckInToTime < $HorarioEntradaToleranciaToTime){
                        $restante = $restante + $hospedagem->valortarifa;
                        $CheckInAntecipado = true;
                        $dias = $dias + 1;
                    }
        }

        }
                           
        if($dias > 1){
           
            $valorPagarRestante = $ValorTarifa * $dias;
            $valorPagarRestante = $valorPagarRestante - $ValorPago;

        }
        
        if(strtotime($dataCheckInParaCobrar) > strtotime($hoje)){ 
        
            \Session::flash('message', ['msg'=>'Data do Check-In depois da data de HOJE!', 'class'=>'danger']);
            $dias = 0;
            $valorPagarRestante = 0;
       
        }
        
        if($valorPagarRestante == null){ $valorPagarRestante = 0; }
      
        $valorTotal = $ValorTarifa * $dias;
       
        if($hospedagem->checkin_at != null and $hospedagem->checkin == 1){
        $hospedagem->valor_restante = $valorPagarRestante;
        $hospedagem->qntdiarias = $dias;
        $hospedagem->valor = $valorTotal;
        $hospedagem->update();
        }
        


        //dd($comprovante);
        return view('meuspedidos.meu_pedido', compact('CheckInAntecipado','CheckOutAtrasado','hospedagem','unidades_habitacionais', 'horario', 'comprovante', 'arquivo', 'cancelar', 'hoje', 'valorPagarRestante'));
       
    }


    public function uploadComprovantePagamento(Request $request){


    //dd($request->all());
    //$path = $request->file('documento')->getRealPath();
    //$logo = file_get_contents($path);
    //$base64 = base64_encode($logo);
    //dd($base64);
    
    //dd($base64);

        




        if(!$request->hasFile('documento')){
        
            return back()->withInput()->withErrors(['Falta anexar o comprovante de pagamento!']);
        
        }else{
                
                $type = $request->documento->extension();
                $file = $request->file('documento');
                $contents = $file->openFile()->fread($file->getSize());
                $contents = base64_encode($contents);

                if(!$request->file('documento')->isValid()){ 
                    return back()->withInput()->withErrors(['Arquivo Inválido!']);
                }

                if($type != 'jpg' and $type != 'jpeg' and $type != 'png' and $type != 'pdf'){
                    return back()->withInput()->withErrors(['Formato de Arquivo Inválido!']);
                }
        
        }
        
        $customMessages = [          
            'documento.required' => 'Falta anexar o comprovante de pagamento!',
        ];

        $validatedData = [
           'documento' => 'required|mimes:jpeg,png,jpg,pdf|max:4048',         
        ];

        $validatedData = $request->validate($validatedData, $customMessages);
        
        $verifica = \App\Comprovante::where('hospedagem_id', $request->hospedagem_id)->count();
        if($verifica > 0){           
             $comprovante = \App\Comprovante::where('hospedagem_id', $request->hospedagem_id)->first();
        }else{
             $comprovante = new \App\Comprovante;
        }
        
        $comprovante->hospedagem_id = $request->hospedagem_id;    
        if($request->hasFile('documento')){
            $comprovante->arquivo = $contents;
            $comprovante->tipo_doc = $type;
        }
        


        if($verifica > 0){
            
            if($comprovante->update()){

            $hospedagem = \App\Hospede::findOrFail($request->hospedagem_id);
            $hospedagem->status = 4;
            $hospedagem->update();
            \Session::flash('message', ['msg'=>'Comprovante atualizado com sucesso.', 'class'=>'success']);
            return redirect()->back();

            }

        }else{
            
            if($comprovante->save()){

            $hospedagem = \App\Hospede::findOrFail($request->hospedagem_id);
            $hospedagem->status = 4;
            $hospedagem->update();
            \Session::flash('message', ['msg'=>'Comprovante cadastrado com sucesso.', 'class'=>'success']);
            return redirect()->back();

            } 
        
        }
        

            \Session::flash('message', ['msg'=>'Ocorreu um erro durante o salvamento! Tente de novo ou contacte o Administrador!', 'class'=>'danger']);
            return redirect()->back();

    }


    public function deleteInscricao($id){

        $id = Crypt::decrypt($id);
        $hospedagem = \App\Hospede::findOrFail($id);
        $hospedagem->delete();
        \Session::flash('message', ['msg'=>'Deletado com sucesso!!', 'class'=>'success']);
        return redirect()->back();
        //dd($hospedagem);

    }

    public function teste(){

        $a = "teste";
        return $a;

    }
    private function diaBloqueado(){



           ///////////////////////////////////////////////////////////////////////////////////////
            /* FUNCAO QUE BLOQUEIA A INSCRICAO ANTES DO DIA AGENDADO NOS MESES DE ALTA TEMPORADA */
            $diaBloqueado = \App\BloqueioDia::where('id', 1)->first();
            $mes = date("m");
            //$mes = '11';
            //dd('ok');
            if($mes === "11" or $mes === "12" or $mes === "01"){

                $diaHoje = date("d");             
                if($diaHoje > $diaBloqueado->dia){
                \Session::flash('message', ['msg'=>"Inscrições Abertas até o dia ". $diaBloqueado->dia ." do mês!", 'class'=>'danger']);
                return redirect()->back();

            }
            }
            /* FIM DA FUNCAO*/
            ///////////////////////////////////////////////////////////////////////////////////////

        
    }


    public function cancelarhospedagem($id){

        $id = Crypt::decrypt($id);
        $hospedagem = \App\Hospede::findOrFail($id); 
        if($hospedagem->status == 7){
            $email = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\CancelaFilaEsperaUsuarioMail($hospedagem));
            \Session::flash('message', ['msg'=>'Reserva cancelada com sucesso. Saiu da Fila de Espera', 'class'=>'success']);
        }else{
            $email = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\CancelaMail($hospedagem));
            \Session::flash('message', ['msg'=>'Reserva cancelada com sucesso.', 'class'=>'success']);
        }
        $hospedagem->status = 6;
        $hospedagem->update();
        
        return redirect()->route('hospede.meuspedidos');
        
    }



    public function deletePreCadastro($id){

        $id = Crypt::decrypt($id);
        $user = \App\User::findOrFail($id);
        $user->delete();
        \Session::flash('message', ['msg'=>'Deletado com sucesso!!', 'class'=>'success']);
        return redirect()->back();
        

    }



}
