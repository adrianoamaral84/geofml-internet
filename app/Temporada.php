<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
   protected $table = "temporada";
   public $timestamps = false;

   public function tipotemporadas(){
   	return $this->belongsTo(TipoTemporada::class, 'tipo_temporada_id');
   }
}
