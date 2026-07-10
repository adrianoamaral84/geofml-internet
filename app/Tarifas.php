<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarifas extends Model
{
   protected $table = "tarifas_und";
   public $timestamps = false;

   public function tipoundhab(){
   	return $this->belongsTo(TipoUndHab::class, 'tipoundhab_id');
   }
   public function undhab(){
      return $this->belongsTo(UnidadeHabitacional::class, 'tipoundhab_id');
   }
   public function grupodestinacao(){
   	return $this->belongsTo(GrupoTarifa::class, 'grupo_destinacao_id');
   }
   public function temporada(){
   	return $this->belongsTo(Temporada::class, 'temporada_id');
   }
}
