<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\GrupoTarifa;

class PostoGraduacao extends Model
{
    public $timestamps = false;
    protected $table = "posto_graduacao";


     public static function listAll($search){
        //$search = "admin";
        //dd($search);
        if ($search == null) {           
            return PostoGraduacao::paginate(10);
        } else {
            //return 'nao null';
            return PostoGraduacao::where('descricao', 'LiKE', '%'.$search.'%')->paginate(10);
        }
    }

    public function grupotarifa() {
        
        return $this->belongsToMany(GrupoTarifa::class, 'grupo_tarifa_posto_graduacao', 'posto_id', 'grupotarifa_id');
    }
     public function grupotarifas() {

        return $this->belongsToMany('App\GrupoTarifa');
    }

}
