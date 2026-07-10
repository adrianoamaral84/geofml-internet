<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PostoGraduacao;

class GrupoTarifa extends Model
{
    protected $table = "grupo_tarifa";
    public $timestamps = false;


    public function postos() 
    {
        return $this->belongsToMany(PostoGraduacao::class, 'grupo_tarifa_posto_graduacao', 'grupotarifa_id', 'posto_id');
    }
    
    public function teste()
    {
        return $this->hasMany(PostoGraduacao::class);
    }

    public function unidadehabitacional()
    {    
        return $this->belongsTo(UnidadeHabitacional::class, 'unidade_habitacional_id');
    }
    
    public function postoss() 
    {
        return $this->belongsToMany('App\PostoGraduacao');
    }

    public function tipoundhabitacao()
    {
        return $this->belongsTo(TipoUndHab::class, 'unidade_habitacional_id');
    }


    


}
