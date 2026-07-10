<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hospede extends Model
{
    protected $table = "hospedagem";

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function tipouh(){
        return $this->belongsTo(TipoUndHab::class, 'tipo_und_id');
    }

    public function undHB(){
        return $this->belongsTo(UnidadeHabitacional::class, 'und_habitacionais_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comprovante(){
        return $this->hasOne(Comprovante::class, 'hospedagem_id');
    }

    public function status_hospedagem(){
        return $this->belongsTo(Status_hospedagem::class, 'status');
    }
    
    public function valorTarifaComDesconto()
{
    if (!$this->user) {
        return $this->valortarifa;
    }

    return $this->user->aplicarDesconto($this->valortarifa);
}

}
