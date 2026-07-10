<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnidadeHabitacional extends Model
{
    protected $table = "unidades_habitacionais";  
    //public $timestamps = false;
	public function tipohabitacao(){
   		return $this->belongsTo(TipoUndHab::class, 'tipo_und_hab_id');
   	}
   	public function classe(){
   		return $this->belongsTo(ClasseHabitacional::class, 'classe_habitacional_id');
   	}
   	public function destino(){
   		return $this->belongsTo(GrupoDestinacao::class, 'grupo_destinacao_id');
   	}
}
