<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrupoTarifaPostoGraduacao extends Model
{
    protected $table = "grupo_tarifa_posto_graduacao";
    public $timestamps = false;
    public $incrementing = false;


    public function grupotarifa()
    {
        return $this->belongsToMany(GrupoTarifa::class);
    }

}
