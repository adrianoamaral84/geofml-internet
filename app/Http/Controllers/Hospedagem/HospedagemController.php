<?php

namespace App\Http\Controllers\Hospedagem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Crypt;
use Carbon\Carbon;
use App\Http\Controllers\Mail\MailController;
use Illuminate\Support\Facades\DB;


class HospedagemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){

    	$consulta = \App\Hospede::where('status', 4)
        ->with('user')->get()->sortBy('user.postograd_id');

    	return view('hospedagem.aguardandoliberacao', compact('consulta', $consulta));
    }
    public function distribuicaoGen(){

        session(['link' => url()->current()]);

        $ontem = Carbon::now()->subday(1);
        $ontem = $ontem->format('Y-m-d');
        $umMes = Carbon::now()->addmonths(1)->endOfMonth()->format('Y-m-d');
        
        
        $genAtiva = new \stdClass;    
        $genAtiva = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        WHERE h.status IN (0,7) AND u.postograd_id IN (1,2,3) AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $genReservaPttc = new \stdClass;    
        $genReservaPttc = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        WHERE h.status IN (0,7) AND u.postograd_id IN (27,28,29) AND u.pttc = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        
        
        $genReserva = new \stdClass;    
        $genReserva = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        WHERE h.status IN (0,7) AND u.postograd_id IN (27,28,29) AND u.pttc = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        
        
        $genPensionista = new \stdClass;    
        $genPensionista = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        WHERE h.status IN (0,7) AND u.postograd_id IN (40,41,42) AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        return view('hospedagem.distribuicao.gen', compact('genAtiva', 'genReserva', 'genPensionista', 'genReservaPttc'));

    }
    

    public function distribuicaoOfSup(){

        session(['link' => url()->current()]);

        $ontem = Carbon::now()->subday(1);
        $ontem = $ontem->format('Y-m-d');
        $umMes = Carbon::now()->addmonths(1)->endOfMonth()->format('Y-m-d');
        

        // MILITARES DA ATIVA
        $ofSupAtivaCMS = new \stdClass;    
        $ofSupAtivaCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (4,5,6) AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        


        $ofSupAtivaNOCMS = new \stdClass;    
        $ofSupAtivaNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (4,5,6) AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        


        $ofSupAtivaForcaAerea = new \stdClass;    
        $ofSupAtivaForcaAerea = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (4,5,6) AND g.forca_id = 5 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        

        $ofSupAtivaMarinha = new \stdClass;    
        $ofSupAtivaMarinha = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (4,5,6) AND g.forca_id = 4 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        


        // MILITARES DA RESERVA PTTC
        $ofSupReservaPttcCMS = new \stdClass;    
        $ofSupReservaPttcCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND u.pttc = 1 AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupReservaPttcNOCMS = new \stdClass;    
        $ofSupReservaPttcNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND u.pttc = 1 AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupReservaPttcForcaAerea = new \stdClass;    
        $ofSupReservaPttcForcaAerea = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND g.forca_id = 5 AND u.pttc = 1 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupReservaPttcMarinha = new \stdClass;    
        $ofSupReservaPttcMarinha = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND g.forca_id = 4 AND u.pttc = 1 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

       
        // MILITARES DA RESERVA
        $ofSupReserva = new \stdClass;    
        $ofSupReserva = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND c.uf_id IN (18,23,24) AND u.pttc = 0 AND g.forca_id NOT IN (5,4) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        


        $ofSupReservaNOCMSR1 = new \stdClass;    
        $ofSupReservaNOCMSR1 = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        


        $ofSupReservaMARINHAR1 = new \stdClass;    
        $ofSupReservaMARINHAR1 = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND g.forca_id = 4 AND u.pttc = 0 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupReservaFORCAR1 = new \stdClass;    
        $ofSupReservaFORCAR1 = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND g.forca_id = 5 AND u.pttc = 0 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        //
        $ofSupPensionista = new \stdClass;    
        $ofSupPensionista = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
         LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (43,44,45)  AND c.uf_id IN (18,23,24) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        $ofSupPensionistaNOCMS = new \stdClass;    
        $ofSupPensionistaNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (43,44,45) AND c.uf_id NOT IN (18,23,24) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        //PET
        $ofSupAtivaCMSPET = new \stdClass;    
        $ofSupAtivaCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (4,5,6) AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupAtivaNOCMSPET = new \stdClass;    
        $ofSupAtivaNOCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (4,5,6) AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupAtivaForcaAereaPET = new \stdClass;    
        $ofSupAtivaForcaAereaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (4,5,6) AND g.forca_id = 5 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupAtivaMarinhaPET = new \stdClass;    
        $ofSupAtivaMarinhaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (4,5,6) AND g.forca_id = 4 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        
      
        $ofSupReservaPttcCMSPET = new \stdClass;    
        $ofSupReservaPttcCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND u.pttc = 1 AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupReservaPttcNOCMSPET = new \stdClass;    
        $ofSupReservaPttcNOCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND u.pttc = 1 AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupReservaPttcForcaAereaPET = new \stdClass;    
        $ofSupReservaPttcForcaAereaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND g.forca_id = 5 AND u.pttc = 1 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        $ofSupReservaForcaAereaPET = new \stdClass;    
        $ofSupReservaForcaAereaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND g.forca_id = 5 AND u.pttc = 0 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupReservaPttcMarinhaPET = new \stdClass;    
        $ofSupReservaPttcMarinhaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND g.forca_id = 4 AND u.pttc = 1 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupReservaPET = new \stdClass;    
        $ofSupReservaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND g.forca_id NOT IN (4,5) AND u.pttc = 0 AND c.uf_id IN (18,23,24) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        $ofSupReservaMARINHAPET = new \stdClass;    
        $ofSupReservaMARINHAPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND g.forca_id = 4 AND u.pttc = 0 AND c.uf_id IN (18,23,24) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


        $ofSupReservaMARINHAPETPTTC = new \stdClass;    
        $ofSupReservaMARINHAPETPTTC = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND g.forca_id = 4 AND u.pttc = 1 AND c.uf_id IN (18,23,24) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");






        $ofSupReservaPETNOCMS = new \stdClass;    
        $ofSupReservaPETNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (30,31,32) AND u.pttc = 0 AND c.uf_id NOT IN (18,23,24) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $ofSupPensionistaPET = new \stdClass;    
        $ofSupPensionistaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (43,44,45) AND g.forca_id NOT IN (4,5) AND c.uf_id IN (18,23,24) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        $ofSupPensionistaPETNOCMS = new \stdClass;    
        $ofSupPensionistaPETNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (43,44,45) AND c.uf_id NOT IN (18,23,24) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        




        return view('hospedagem.distribuicao.gen_of_sup', compact('ofSupReservaFORCAR1','ofSupReservaMARINHAR1','ofSupReservaNOCMSR1','ofSupReserva', 'ofSupPensionista', 'ofSupAtivaCMS', 'ofSupAtivaNOCMS', 'ofSupReservaPttcCMS', 'ofSupReservaPttcNOCMS', 'ofSupAtivaMarinha', 'ofSupAtivaForcaAerea', 'ofSupReservaPttcForcaAerea', 'ofSupReservaPttcMarinha', 'ofSupAtivaCMSPET', 'ofSupAtivaNOCMSPET', 'ofSupAtivaForcaAereaPET', 'ofSupAtivaMarinhaPET', 'ofSupReservaPttcCMSPET', 'ofSupReservaPttcNOCMSPET', 'ofSupReservaPttcForcaAereaPET', 'ofSupReservaPttcMarinhaPET', 'ofSupReservaPET', 'ofSupPensionistaPET', 'ofSupReservaPETNOCMS', 'ofSupPensionistaPETNOCMS', 'ofSupReservaMARINHAPET', 'ofSupReservaMARINHAPETPTTC', 'ofSupReservaForcaAereaPET', 'ofSupPensionistaNOCMS'));

    }
    public function distribuicaoCapTen(){

        session(['link' => url()->current()]);

        $ontem = Carbon::now()->subday(1);
        $ontem = $ontem->format('Y-m-d');
        $umMes = Carbon::now()->addmonths(1)->endOfMonth()->format('Y-m-d');
        


        // MILITARES ATIVA
        $tenCapCMS = new \stdClass;    
        $tenCapCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (7,8,9,10) AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapNOCMS = new \stdClass;    
        $tenCapNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (7,8,9,10) AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapForcaAerea = new \stdClass;    
        $tenCapForcaAerea = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (7,8,9,10) AND g.forca_id = 5 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapMarinha = new \stdClass;    
        $tenCapMarinha = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (7,8,9,10) AND g.forca_id = 4 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        // MILITARES PTTC
        $tenCapPttcCMS = new \stdClass;    
        $tenCapPttcCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND c.uf_id IN (18,23,24) AND u.pttc = 1 AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        


        $tenCapPttcNOCMS = new \stdClass;    
        $tenCapPttcNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND c.uf_id NOT IN (18,23,24) AND u.pttc = 1 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapReservaForcaAereaPTTC = new \stdClass;    
        $tenCapReservaForcaAereaPTTC = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND g.forca_id = 5 AND u.pttc = 1 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapReservaMarinhaPTTC = new \stdClass;    
        $tenCapReservaMarinhaPTTC = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND g.forca_id = 4 AND u.pttc = 1 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        


        // MILITARES DA RESERVA
        $tenCapReserva = new \stdClass;    
        $tenCapReserva = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND c.uf_id IN (18,23,24) AND u.pttc = 0 AND g.forca_id NOT IN (5,4) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


        
        $tenCapReservaNOCMSR1 = new \stdClass;    
        $tenCapReservaNOCMSR1 = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND u.pttc = 0 AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        
       
        $tenCapReservaFORCAAEREAR1 = new \stdClass;    
        $tenCapReservaFORCAAEREAR1 = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND g.forca_id = 5 AND u.pttc = 0 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapReservaMARINHAR1 = new \stdClass;    
        $tenCapReservaMARINHAR1 = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND g.forca_id = 4 AND u.pttc = 0 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        
        // SERVIDOR CIVIL
        $ServidorCivilNS = new \stdClass;    
        $ServidorCivilNS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        WHERE h.status IN (0,7) AND u.postograd_id IN (54,57) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        $tenCapPensionista = new \stdClass;    
        $tenCapPensionista = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (46,47,48,49) AND h.pet = 0 AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (5,4) AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        $tenCapPensionistaNOCMS = new \stdClass;    
        $tenCapPensionistaNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (46,47,48,49) AND h.pet = 0 AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (5,4) AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        
        // PET
        $tenCapCMSPET = new \stdClass;    
        $tenCapCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (7,8,9,10) AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        
        
        $tenCapNOCMSPET = new \stdClass;    
        $tenCapNOCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (7,8,9,10) AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapForcaAereaPET = new \stdClass;    
        $tenCapForcaAereaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (7,8,9,10) AND g.forca_id = 5 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapMarinhaPET = new \stdClass;    
        $tenCapMarinhaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (7,8,9,10) AND g.forca_id = 4 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        //dd($tenCapMarinhaPET);

        $tenCapPttcCMSPET = new \stdClass;    
        $tenCapPttcCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND c.uf_id IN (18,23,24) AND u.pttc = 1 AND g.forca_id NOT IN (4,5) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapPttcNOCMSPET = new \stdClass;    
        $tenCapPttcNOCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND c.uf_id NOT IN (18,23,24) AND u.pttc = 1 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        
        $tenCapReservaForcaAereaPTTCPET = new \stdClass;    
        $tenCapReservaForcaAereaPTTCPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND g.forca_id = 5 AND u.pttc = 1 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapReservaMarinhaPTTCPET = new \stdClass;    
        $tenCapReservaMarinhaPTTCPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND g.forca_id = 4 AND u.pttc = 1 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


        $tenCapReservaPET = new \stdClass;    
        $tenCapReservaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND u.pttc = 0 AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (5,4) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


        $tenCapReservaPETNOCMS = new \stdClass;    
        $tenCapReservaPETNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND u.pttc = 0 AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (5,4) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

       
        $tenCapReservaFORCAPET = new \stdClass;    
        $tenCapReservaFORCAPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND g.forca_id IN (5) AND u.pttc = 0 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


        $tenCapReservaMARINHAPET = new \stdClass;    
        $tenCapReservaMARINHAPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (33,34,35) AND g.forca_id IN (4) AND u.pttc = 0 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        //dd($tenCapReservaFORCAPET);
    
   
        $ServidorCivilNSPET = new \stdClass;    
        $ServidorCivilNSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        WHERE h.status IN (0,7) AND u.postograd_id IN (54,57) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $tenCapPensionistaPET = new \stdClass;    
        $tenCapPensionistaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (46,47,48,49) AND h.pet = 1 AND g.forca_id NOT IN (5,4) AND c.uf_id IN (18,23,24) AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        
/*
        $tenCapMarinhaPET = new \stdClass;    
        $tenCapMarinhaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (46,47,48,49) AND g.forca_id = 4 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        dd($tenCapMarinhaPET);
*/
        $tenCapPensionistaPETNOCMS = new \stdClass;    
        $tenCapPensionistaPETNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (46,47,48,49) AND h.pet = 1 AND c.uf_id NOT IN (18,23,24) AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        


        return view('hospedagem.distribuicao.cap_ten', compact('tenCapReservaMARINHAR1','tenCapReservaFORCAAEREAR1','tenCapReservaNOCMSR1','tenCapCMS', 'tenCapNOCMS', 'tenCapPttcCMS', 'tenCapPttcNOCMS', 'tenCapReserva', 'tenCapPensionista', 'ServidorCivilNS', 'tenCapForcaAerea', 'tenCapMarinha','tenCapReservaMarinhaPTTC', 'tenCapReservaForcaAereaPTTC', 'tenCapCMSPET', 'tenCapNOCMSPET', 'tenCapForcaAereaPET', 'tenCapMarinhaPET', 'tenCapPttcCMSPET', 'tenCapPttcNOCMSPET', 'tenCapReservaForcaAereaPTTCPET', 'tenCapReservaMarinhaPTTCPET', 'tenCapReservaPET', 'tenCapReservaFORCAPET', 'ServidorCivilNSPET', 'tenCapPensionistaPET', 'tenCapReservaPETNOCMS', 'tenCapPensionistaPETNOCMS', 'tenCapPensionistaNOCMS', 'tenCapReservaMARINHAPET'));


    }



    public function distribuicaoSubSgt(){

        session(['link' => url()->current()]);
               
        $ontem = Carbon::now()->subday(1);
        $ontem = $ontem->format('Y-m-d');
        $umMes = Carbon::now()->addmonths(1)->endOfMonth()->format('Y-m-d');
        

        // MILITARES ATIVA
        $StSgtAtivaCMS = new \stdClass;    
        $StSgtAtivaCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (11,24,25,26) AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
     
        $StSgtAtivaNOCMS = new \stdClass;    
        $StSgtAtivaNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (11,24,25,26) AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        $StSgtAtivaCMSFORCAAEREA = new \stdClass;    
        $StSgtAtivaCMSFORCAAEREA = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (11,24,25,26) AND g.forca_id = 5 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


        $StSgtAtivaMarinha = new \stdClass;    
        $StSgtAtivaMarinha = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (11,24,25,26) AND g.forca_id = 4 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
       

        // MILITARES DA RESERVA PTTC
        $StSgtReservaPttcCMS = new \stdClass;    
        $StSgtReservaPttcCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND c.uf_id IN (18,23,24) AND u.pttc = 1 AND g.forca_id = 3 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $StSgtReservaPttcNOCMS = new \stdClass;    
        $StSgtReservaPttcNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND c.uf_id NOT IN (18,23,24) AND u.pttc = 1 AND g.forca_id = 3 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        


        $StSgtReservaPttcFORCAAEREA = new \stdClass;    
        $StSgtReservaPttcFORCAAEREA = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND u.pttc = 1 AND g.forca_id = 5 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $StSgtReservaPttcMARINHA = new \stdClass;    
        $StSgtReservaPttcMARINHA = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND u.pttc = 1 AND g.forca_id = 4 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


       
        $StSgtReservaPttc = new \stdClass;    
        $StSgtReservaPttc = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND u.pttc = 1 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        
        // MILITARES DA RESERVA 
        $StSgtReserva = new \stdClass;    
        $StSgtReserva = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND u.pttc = 0 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


        $StSgtReservaNOCMSR1 = new \stdClass;    
        $StSgtReservaNOCMSR1 = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND u.pttc = 0 AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $StSgtReservaFORCAAEREAR1 = new \stdClass;    
        $StSgtReservaFORCAAEREAR1 = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND g.forca_id = 5 AND u.pttc = 0 AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $StSgtReservaMARINHAR1 = new \stdClass;    
        $StSgtReservaMARINHAR1 = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND g.forca_id = 4 AND h.pet = 0 AND u.pttc = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
      
        
        // SERVIDOR CIVIL
        $ServidorCivilNM = new \stdClass;    
        $ServidorCivilNM = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        WHERE h.status IN (0,7) AND u.postograd_id IN (55,58) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        // PENSONISTAS
        $PensionistaSTSgt = new \stdClass;    
        $PensionistaSTSgt = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h

        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (50,51,52,53)  AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        
        $PensionistaSTSgtNOMCS = new \stdClass;    
        $PensionistaSTSgtNOMCS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (50,51,52,53) AND c.uf_id NOT IN (18,23,24) AND h.pet = 0 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        //dd($PensionistaSTSgt);

        // PET
        $StSgtAtivaCMSPET = new \stdClass;    
        $StSgtAtivaCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (11,24,25,26) AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $StSgtAtivaNOCMSPET = new \stdClass;    
        $StSgtAtivaNOCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (11,24,25,26) AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");



        $StSgtReservaPETCMS = new \stdClass;    
        $StSgtReservaPETCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND c.uf_id IN (18,23,24) AND g.forca_id NOT IN (4,5) AND u.pttc = 0 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


        $StSgtReservaPETNOCMS = new \stdClass;    
        $StSgtReservaPETNOCMS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND u.pttc = 0 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");















        $StSgtReservaR1CMSPET = new \stdClass;    
        $StSgtReservaR1CMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (11,24,25,26) AND c.uf_id NOT IN (18,23,24) AND g.forca_id NOT IN (4,5) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
                

        $StSgtAtivaCMSFORCAAEREAPET = new \stdClass;    
        $StSgtAtivaCMSFORCAAEREAPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (11,24,25,26) AND g.forca_id = 5 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");



        $StSgtAtivaNOCMSMARINHAPET = new \stdClass;    
        $StSgtAtivaNOCMSMARINHAPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (11,24,25,26) AND c.uf_id NOT IN (18,23,24) AND g.forca_id = 4 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        

        $StSgtAtivaMarinhaPET = new \stdClass;    
        $StSgtAtivaMarinhaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (11,24,25,26) AND g.forca_id = 4 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $StSgtReservaPttcCMSPET = new \stdClass;    
        $StSgtReservaPttcCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND c.uf_id IN (18,23,24) AND u.pttc = 1 AND g.forca_id = 3 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $StSgtReservaPttcNOCMSPET = new \stdClass;    
        $StSgtReservaPttcNOCMSPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND c.uf_id NOT IN (18,23,24) AND u.pttc = 1 AND g.forca_id = 3 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        
        $StSgtReservaPttcFORCAAEREAPET = new \stdClass;    
        $StSgtReservaPttcFORCAAEREAPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND u.pttc = 1 AND g.forca_id = 5 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $StSgtReservaPttcMARINHAPET = new \stdClass;    
        $StSgtReservaPttcMARINHAPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND u.pttc = 1 AND g.forca_id = 4 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


        $StSgtReservaMarinhaPET = new \stdClass;    
        $StSgtReservaMarinhaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND u.pttc = 0 AND g.forca_id = 4 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $StSgtReservaForcaPET = new \stdClass;    
        $StSgtReservaForcaPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (36,37,38,39) AND u.pttc = 0 AND g.forca_id = 5 AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");

        $ServidorCivilNMPET = new \stdClass;    
        $ServidorCivilNMPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status 
        WHERE h.status IN (0,7) AND u.postograd_id IN (55,58) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        $PensionistaSTSgtPET = new \stdClass;    
        $PensionistaSTSgtPET = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        

        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 

        WHERE h.status IN (0,7) AND u.postograd_id IN (50,51,52,53) AND c.uf_id IN (18,23,24) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");


        $PensionistaSTSgtPETNOMCS = new \stdClass;    
        $PensionistaSTSgtPETNOMCS = DB::select("SELECT h.*, u.name, p.sigla, t.descricao, s.status FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        left JOIN tipoundhab t ON t.id = h.tipo_und_id
        left JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id 
        WHERE h.status IN (0,7) AND u.postograd_id IN (50,51,52,53) AND c.uf_id NOT IN (18,23,24) AND h.pet = 1 AND h.tipo_und_id NOT IN (11,12) AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

        //dd($PensionistaSTSgt);

       




        $anoAtual = Carbon::now();
        $proximoAno = Carbon::now()->addYears(1);
        $anoAtual = $anoAtual->year;
        $proximoAno = $proximoAno->year;
               
        return view('hospedagem.distribuicao.sub_sgt', compact('StSgtReservaMARINHAR1','StSgtReservaFORCAAEREAR1','StSgtReservaNOCMSR1','StSgtAtivaCMS', 'StSgtAtivaNOCMS', 'StSgtAtivaCMSFORCAAEREA', 'StSgtReservaPttcCMS', 'StSgtReservaPttcNOCMS', 'StSgtReserva', 'ServidorCivilNM', 'PensionistaSTSgt', 'StSgtReservaPttcFORCAAEREA', 'StSgtReservaPttcMARINHA', 'StSgtAtivaMarinha', 'StSgtAtivaCMSPET', 'StSgtAtivaNOCMSPET', 'StSgtAtivaCMSFORCAAEREAPET', 'StSgtAtivaMarinhaPET', 'StSgtReservaPttcCMSPET', 'StSgtReservaPttcNOCMSPET', 'StSgtReservaPttcFORCAAEREAPET', 'StSgtReservaPttcMARINHAPET', 'StSgtReservaMarinhaPET', 'StSgtReservaForcaPET', 'ServidorCivilNMPET', 'PensionistaSTSgtPET', 'anoAtual', 'proximoAno', 'StSgtAtivaNOCMSMARINHAPET', 'StSgtReservaPETCMS', 'StSgtReservaPETNOCMS', 'PensionistaSTSgtPETNOMCS', 'PensionistaSTSgtNOMCS'));

    }

    public function distribuicaoMotorhomeCamping(){

        session(['link' => url()->current()]);

        $ontem = Carbon::now()->subday(1);
        $ontem = $ontem->format('Y-m-d');
        $umMes = Carbon::now()->addmonths(1)->endOfMonth()->format('Y-m-d');
        

        $Motorhome = new \stdClass;    
        $Motorhome = DB::select("SELECT h.*, u.name, p.sigla, t.id as ID_TUH, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND t.id  = 12 AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        
        return view('hospedagem.distribuicao.motorhomecamping', compact('Motorhome'));

    }

    public function distribuicaoCamping(){

        session(['link' => url()->current()]);

        $ontem = Carbon::now()->subday(1);
        $ontem = $ontem->format('Y-m-d');
        $umMes = Carbon::now()->addmonths(1)->endOfMonth()->format('Y-m-d');
        
        

        $Camping = new \stdClass;    
        $Camping = DB::select("SELECT h.*, u.name, p.sigla, t.id as ID_TUH, t.descricao, s.status, g.forca_id, g.id as ID_OM, g.cidade_id, f.id as ID_UF, g.sigla as FORCA  FROM hospedagem h 
        INNER JOIN user u ON u.id = h.user_id 
        LEFT JOIN tipoundhab t ON t.id = h.tipo_und_id
        LEFT JOIN posto_graduacao p ON p.id = u.postograd_id
        LEFT JOIN status_hospedagem s ON s.id = h.status
        LEFT JOIN gerenciar_oms g ON g.id = u.om_id
        LEFT JOIN cidade c ON c.id = g.cidade_id
        LEFT JOIN uf f ON f.id = c.uf_id
        WHERE h.status IN (0,7) AND t.id  = 11 AND h.data_inicio >= '$ontem' AND h.data_inicio <= '$umMes'
        ORDER BY u.postograd_id, u.dtUltPromo ASC");
        

       

        return view('hospedagem.distribuicao.camping', compact('Camping'));
 

    }



    public function aguardando_liberacao(){

        $consulta = \App\Hospede::where('status', 4)
         ->with('user')->get()->sortBy('user.postograd_id');
     

        return view('hospedagem.aguardandoliberacao', compact('consulta', $consulta));
    }


    public function liberar(Request $request){

        
        //dd($request->all());
        date_default_timezone_set('America/Sao_Paulo');
        $tipoUND = \App\UnidadeHabitacional::where('id', $request->unidadeshabitacionais)->first();
        $tipoUND = $tipoUND->tipo_und_hab_id;
     
        $customMessages = [

            'final1.required' => 'Campo obrigatório',
            'peridoinicial1.required' => 'Campo obrigatório',
            'peridounidadeshabitacionais.required' => 'Campo obrigatório',
             
        ];

        $validatedData = [
            'final1' => 'required',
            'peridoinicial1' => 'required',
            'unidadeshabitacionais' => 'required', 
        ];

        $validatedData = $request->validate($validatedData, $customMessages);




    //////////////////////////////////////////////////////////////////////////////////////////////////





        





    ////////////////////////////////////////////////////////////////////////////////////////////////



        ///////////////////////////////////////////////////////////////////////
        $pi = Carbon::parse($validatedData['peridoinicial1'])->format('Y-m-d');    
        $pf = Carbon::parse($validatedData['final1'])->format('Y-m-d');
  
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
        
        $data1 = $data;
        
        if($i == 0){
            $data2 = $data1->format('Y-m-d');
        
            $VerificaHospedagem = \App\Hospede::
                where('data_inicio', $data2)
                ->where('und_habitacionais_id', '13')
                ->where('status', 2)
                ->orderBy('data_inicio')
                ->count(); 
        
        }
        
        $data2 = $data1->add(new \DateInterval('P1D'));
        $data2 = $data2->format('Y-m-d');
        
        if ($dataTemporadaAltaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaAltaTermino) {
            $diasAltaTemporada = $diasAltaTemporada + 1;
        }else{
            $diasBaixaTemporada = $diasBaixaTemporada + 1;
        }

        if ($dataTemporadaBaixaInicio <= strtotime($data2) and strtotime($data2) <= $dataTemporadaBaixaTermino) {
           // $diasBaixaTemporada = $diasBaixaTemporada + 1;
        }

        $a[] = $data2;

        }

        //dd($a);

        foreach ($a as $key => $value) {
        
        $VerificaHospedagem = \App\Hospede::
        where('data_inicio', $value)
        ->where('und_habitacionais_id', '13')
        ->where('status', 2)
        ->orderBy('data_inicio')
        ->count(); 
        $CountVerificaHospedagem[] = $VerificaHospedagem;
        
        }

        //dd($CountVerificaHospedagem);
        //dd(count($CountVerificaHospedagem));

        $CountVerificaHospedagem1 = array_shift($CountVerificaHospedagem);
        $CountVerificaHospedagem2 = array_pop($CountVerificaHospedagem);

        foreach ($CountVerificaHospedagem as $key => $value) {
            if($value == 1){
                //dd('Tem 1');
            }
        }

        //dd($CountVerificaHospedagem);

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////





















        // verificar mais tarde


        //dd($request->all()); 
        /*
        // VERIFICA SE JÁ TEM HOSPEDE CADASTRADO NESSA DATA
        $pedidoInicial  =   Carbon::parse($validatedData['peridoinicial1'])->format('Y-m-d');
        $pedidoFinal    =   Carbon::parse($validatedData['final1'])->format('Y-m-d');
        //dd($pedidoInicial);
        $pedidoInicial = '2023-10-13';
        $pedidoFinal   = '2023-10-21';

        $VerificaHospedagem = \App\Hospede::whereDate('data_inicio', '>=', $pedidoInicial)
        ->whereDate('data_termino', '<=', $pedidoFinal)
        //->whereDate('data_inicio', '<', $pedidoFinal)

        //->where('data_inicio', $pedidoInicial)
        ->where('und_habitacionais_id', '13')
        ->orderBy('data_inicio')
        ->get();
        dd($VerificaHospedagem);
        */

        /*
        if($VerificaHospedagem->count() > 0){
        foreach ($VerificaHospedagem as $key => $value) {
            
            $dia[] = $value->data_inicio;
        }
            dd($dia);
        }
        
            dd($VerificaHospedagem->count());
        
        */

        // veirifcar mais tarde

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        //dd($a);    

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $UnidadeHabitacional = \App\UnidadeHabitacional::where('id', $request->unidadeshabitacionais)->first();
        //$grupoTarifa = \App\GrupoTarifa::where('unidade_habitacional_id', $request->unidade_habitacional_id)->with('postos')->get();
        $grupoTarifa = \App\GrupoTarifa::where('unidade_habitacional_id', $UnidadeHabitacional->tipo_und_hab_id)->with('postos')->get();
        //dd($grupoTarifa);
          
        foreach ($grupoTarifa as $key => $value) {
       
            foreach ($value->postos as $posto) {
                $p[] = $posto->id;
                if($posto->id == $request->posto_id){
                   
                  

                    //$tarifa = \App\Tarifas::where('tipoundhab_id', $request->unidade_habitacional_id)
                    $tarifa = \App\Tarifas::where('tipoundhab_id', $UnidadeHabitacional->tipo_und_hab_id)
                    ->where('grupo_destinacao_id', $posto->pivot->grupotarifa_id)
                    ->first();


                        
                }
            }    
        }
       
         
        if (empty($tarifa)) {
            
            \Session::flash('message', ['msg'=>"Tarifa não encontrada para essa UH! Favor Contactar o Administrador!", 'class'=>'danger']);
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
        //dd($totalValor);
        // TROCA DE HABITAÇAO ENTRA NESSA VERIFICAÇÃO
        $hospedagem = \App\Hospede::findOrFail($request->id1);
        if($hospedagem->und_habitacionais_id <> null){
            //dd('ja foi distribuido');
            if($hospedagem->und_habitacionais_id <> $validatedData['unidadeshabitacionais']){




                //dd($diasBaixaTemporada);

                if($hospedagem->valortarifa <> $tarifa->valor_baixa){
                    

                    //dd('tarifaa diferente');

                    //$hospedagem->status = 3;
                    //\Session::flash('message', ['msg'=>"Encaminhado para Pagamento de nova diária!", 'class'=>'success']);
                    //$mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\HospedagemLiberadaPagamento($hospedagem));
                
                }

            //$hospedagem->status = 3;
            //\Session::flash('message', ['msg'=>"Encaminhado para Pagamento de nova diária!", 'class'=>'success']); 
            //$mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\HospedagemLiberadaPagamento($hospedagem)); 

            }

        }




        //dd('fora');



        $hospedagem->data_inicio = Carbon::parse($validatedData['peridoinicial1'])->format('Y-m-d');
        $hospedagem->data_termino = Carbon::parse($validatedData['final1'])->format('Y-m-d');
        $hospedagem->und_habitacionais_id = $validatedData['unidadeshabitacionais'];
        $hospedagem->tipo_und_id = $tipoUND;
        $hospedagem->status = 3;

        if($diasBaixaTemporada > 0){
        $hospedagem->valortarifa = $tarifa->valor_baixa; // Pedido Liberado
        }
        if($diasAltaTemporada > 0){
        $hospedagem->valortarifa = $tarifa->valor; // Pedido Liberado
        }
        $hospedagem->valor = $totalValor; // Pedido Liberado
        $hospedagem->qntdiarias = $diasHospedagem; // Pedido Liberado

        $hospedagem->update();
        \Session::flash('message', ['msg'=>"Pedido liberado com sucesso!", 'class'=>'success']);
        \Illuminate\Support\Facades\Mail::queue(new \App\Mail\HospedagemLiberadaPagamento($hospedagem)); 



/*        
        if($hospedagem->status == 5){
            $mail = 5;
            $hospedagem->status = 5;
            $hospedagem->update();
            \Session::flash('message', ['msg'=>"Pedido Atualizado com sucesso!", 'class'=>'success']);
            $mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\HospedagemAlteradoPeriodoAdm($hospedagem));         
        }

        if($hospedagem->status == 6){
            $mail = 6;
            $hospedagem->status = 5;
            $hospedagem->update(); 
            \Session::flash('message', ['msg'=>"Pedido Atualizado com sucesso!", 'class'=>'success']);  

        }
        if($hospedagem->status == 4){
            $mail = 4;
            $hospedagem->status = 5; 
            $hospedagem->update();
            \Session::flash('message', ['msg'=>"Pedido liberado com sucesso!", 'class'=>'success']);
            //$mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\HospedagemAlteradoPeriodoAdm($hospedagem));
        }
        if($hospedagem->status == 3){
            $mail = 3;
            $hospedagem->status = 3;
            $hospedagem->update();
            \Session::flash('message', ['msg'=>"Pedido Atualizado com sucesso!", 'class'=>'success']); 
            $mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\HospedagemLiberadaPagamento($hospedagem));         

        }
        if($hospedagem->status == 2){
            $mail = 2;
            $hospedagem->status = 5;
            \Session::flash('message', ['msg'=>"Pedido Atualizado com sucesso!", 'class'=>'success']); 

            $hospedagem->update(); 

        }
        if($hospedagem->status == 1){
            $mail = 1;
            $hospedagem->status = 3;
            $hospedagem->update();
            \Session::flash('message', ['msg'=>"Pedido Atualizado com sucesso!", 'class'=>'success']); 

            $mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\HospedagemLiberadaPagamento($hospedagem));         

        }
*/ 
        
        
        
        

        
        return redirect()->route('hospedagem.index');

    }



    public function negar($id){

        //dd('negado');
    	$id = Crypt::decrypt($id);
    	$hospedagem = \App\Hospede::findOrFail($id);
    	$hospedagem->status = 1; // Pedido Negado
    	$hospedagem->update();
        $mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\PedidoHospedagemNegado($hospedagem));

        \Session::flash('message', ['msg'=>"Pedido Negado com sucesso!", 'class'=>'success']);
    	
        return redirect()->route('hospedagem.index');

    }


    public function verdados($id){

        date_default_timezone_set('America/Sao_Paulo');
        $id = Crypt::decrypt($id);

        $hospedagem = \App\Hospede::where('id', $id)->with('user')->first();
        $hospede = \App\User::where('id', $hospedagem->user_id)->first();
        
        $grupo = DB::select("SELECT * FROM grupo_tarifa");
        $grupo_posto = DB::select("SELECT gtpg.*, gt.*, gt.unidade_habitacional_id, th.descricao FROM grupo_tarifa_posto_graduacao gtpg 
            INNER JOIN grupo_tarifa gt ON gt.id = gtpg.grupotarifa_id
            INNER JOIN tipoundhab th ON th.id = gt.unidade_habitacional_id
            WHERE posto_id = $hospede->postograd_id");

        if($hospede->postograd_id == 33){
            // Oficial
            $porClasse = DB::select("SELECT * FROM unidades_habitacionais WHERE classe_habitacional_id IN (1,3,4) AND disponivel = 1");
        }else{
            // PRACA
            $porClasse = DB::select("SELECT * FROM unidades_habitacionais WHERE classe_habitacional_id IN (2,3,4) AND disponivel = 1");
        }

        $unidades_habitacionais = \App\UnidadeHabitacional::where('disponivel', 1)
        ->where('classe_habitacional_id',1)
        ->get();
        
        if($hospedagem->status == 4){
            $comprovanteCount = \App\Comprovante::where('hospedagem_id', $hospedagem->id)->count();
            if($comprovanteCount > 0){
                $comprovante = \App\Comprovante::where('hospedagem_id', $hospedagem->id)->first();
            }else{
            }
        }
        ////////////// INICIO/////////////
        
        $tipos = \App\TipoUndHab::all();
        $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();

        $data_inicio = $altaTemporada->data_inicio;
        $data_termino = $altaTemporada->data_termino;
        
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
            
                //\Session::flash('message', ['msg'=>"Pedido de hospedagem até dia 10 do mês! Para Alta Temporada!", 'class'=>'danger']);
                //return redirect()->back();

            }

               
        }else{
            

            
            
        }

        $minDate = Carbon::now()->format('Y-m-d');            
        $maxDate = Carbon::today()->addMonths(1)->format('Y-m-t');
            

      

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
          $a= json_encode($a);
      

        $hospedagens = \App\Hospede::with('usuario')
        ->where('und_habitacionais_id', 13)
        ->whereIn(DB::RAW('month(data_inicio)'), [$mesAtual,$proximoMes])
        ->whereIn('status', [2,3,4,5])
        ->orderBy('data_inicio', 'asc')
        ->get();
    
        $mes1 = date_create("$hospedagem->data_inicio");
        $mes1 = date_format($mes1, "m");
        $mes = date('m');
        if($mesAtual == $mes1){        
            $liberaDistribuir = 1;
        }elseif($mes1 == $proximoMes){
            
            $liberaDistribuir = 1;
        }else{
           $liberaDistribuir = 0; 
        }

        ////////////// FIM
        if($hospedagem->tipo_und_id == 11){

        $grupoDestino = \App\GrupoDestinacao::where('id', 7)->get();

        }elseif ($hospedagem->tipo_und_id == 12) {
            
        $grupoDestino = \App\GrupoDestinacao::where('id', 7)->get();
        

        }else{

        $grupoDestino = \App\GrupoDestinacao::whereIn('id', [1,2,3,4,5])->get();

        }
        $tipoUND = $hospedagem->tipo_und_id;

        //dd($liberaDistribuir);

         if($hospedagem->status == 4){
           return view('hospedagem.verdados_aguardando', compact('hospedagem', 'hospedagens', 'unidades_habitacionais', 'minDate', 'maxDate', 'hoje','a', 'comprovante', 'grupo_posto', 'grupoDestino', 'mes','tipoUND', 'liberaDistribuir'));
        }else{
            return view('hospedagem.verdados_aguardando', compact('hospedagem', 'hospedagens', 'unidades_habitacionais', 'minDate', 'maxDate', 'hoje','a', 'grupo_posto','grupoDestino', 'mes','tipoUND', 'liberaDistribuir'));
        }
        
    }


     public function verdados_aguardando_liberacao($id){

        date_default_timezone_set('America/Sao_Paulo');
        //dd($id);

        $id = Crypt::decrypt($id);
        $hospedagem = \App\Hospede::where('id', $id)->with('user')->first();
        
        //dd($hospedagem->data_termino);
        $unidades_habitacionais = \App\UnidadeHabitacional::where('disponivel', 1)
        //->where('tipo_und_hab_id', $hospedagem->und_habitacionais_id)
        ->get();

        if($hospedagem->status == 4){
            $comprovante = \App\Comprovante::where('hospedagem_id', $hospedagem->id)->first();
            //dd($comprovante);
        }
        ////////////// INICIO


        $tipos = \App\TipoUndHab::all();
        $altaTemporada = \App\Temporada::where('tipo_temporada_id', 1)->first();

        $data_inicio = $altaTemporada->data_inicio;
        $data_termino = $altaTemporada->data_termino;
        
     
        $mesAtual = date("m");
        $ano = date("Y");
      
        //$mesAtual = "02";
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


        
            

         
            $minDate = Carbon::now()->format('Y-m-d');            
            $maxDate = Carbon::today()->addMonths(1)->format('Y-m-t');

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
          $a= json_encode($a);
      





        ////////////// FIM



          //dd($mesAtual);

         if($hospedagem->status == 4){
           return view('hospedagem.verdados_aguardando_liberacao', compact('hospedagem', 'unidades_habitacionais', 'minDate', 'maxDate', 'hoje','a', 'comprovante'));
        }else{
            return view('hospedagem.verdados_aguardando_liberacao', compact('hospedagem', 'unidades_habitacionais', 'minDate', 'maxDate', 'hoje','a'));
        }
        
    }

    public function liberar_uso($id){

        date_default_timezone_set('America/Sao_Paulo');
        $id = Crypt::decrypt($id);
        //dd($id);
        $hospedagem = \App\Hospede::where('id',$id)->with('undHB')->first();

        //$mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\hospedagemLiberada($hospedagem));
        //dd("ok");

        $hospedagem->status = 2;
        
        if($hospedagem->update() == true){

        //dd($hospedagem->undHB->tipo_und_hab_id);

        if($hospedagem->undHB->pet == 1){

            
            if($hospedagem->undHB->tipo_und_hab_id == 11){
            
            // CAMPING  
            $mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\hospedagemLiberadaCampingComPet($hospedagem));

            }elseif($hospedagem->undHB->tipo_und_hab_id == 12){
            
            // MOTOR HOME 
            $mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\hospedagemLiberadaMotorHomeComPet($hospedagem));

            }else{
              
            // APARTAMENTO , CASA 
            $mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\hospedagemLiberadaComPet($hospedagem));

            }



        }else{
        

        //return new \App\Mail\hospedagemLiberada($hospedagem);

        $mail = \Illuminate\Support\Facades\Mail::queue(new \App\Mail\hospedagemLiberada($hospedagem));
        
        }

        \Session::flash('message', ['msg'=>"Hospedagem liberada com sucesso!", 'class'=>'success']);
        return redirect()->route('hospedagem.aguardando_liberacao');

        }else{
        \Session::flash('message', ['msg'=>"Falha ao gravar!", 'class'=>'danger']);
        return redirect()->back();


        }
        
        //return redirect()->back();
        return redirect()->route('hospedagem.aguardando_liberacao');
    
    }

    public function negar_uso($id){
        $id = Crypt::decrypt($id);
        //dd($id);

        $hospedagem = \App\Hospede::findOrFail($id);
        $hospedagem->status = 1;
        $hospedagem->update();
        //dd($hospedagem);
        \Session::flash('message', ['msg'=>"Hospedagem NEGADA com sucesso!", 'class'=>'danger']);
        \Illuminate\Support\Facades\Mail::queue(new \App\Mail\PedidoHospedagemNegado($hospedagem));
        return redirect()->route('hospedagem.aguardando_liberacao');
     }

     public function retornarDistribuicao($id){
        
        //$data = session()->all();
        $ano = session()->get('ano');
        $mes = session()->get('mes');
        $status = session()->get('status');
        //dd(session()->get('ano'));
        $id = Crypt::decrypt($id);
        $hospedagem = \App\Hospede::findOrFail($id);
        $hospedagem->status = 0;
        $hospedagem->update();

        \Session::flash('message', ['msg'=>"Hospedagem voltou para Distribuição!", 'class'=>'success']);
        return redirect()->route('relatorio.view', ['ano' => $ano, 'mes' => $mes, 'status' => $status]);


        //dd($id);


     }

     public function retornarEnvioComprovante(Request $request){


        
        $user = \App\Hospede::find($request->id);

        //dd($request->motivo);
        $dataAtual = Carbon::now()->locale('pt_BR');
        $user->updated_at = $dataAtual;
        //$user->motivo = $request->motivo;
        $user->status = 5;
        //$user->syncRoles(['5']);
        $user->update();
        //return new \App\Mail\MailReenviarComprovante($user, $request->motivo);
        \Illuminate\Support\Facades\Mail::queue(new \App\Mail\MailReenviarComprovante($user, $request->motivo));
        \Session::flash('message', ['msg'=>'Retornou com sucesso!', 'class'=>'success']);
        return redirect()->route('hospedagem.aguardando_liberacao');


     }
}
