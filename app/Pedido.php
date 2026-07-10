<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = "pedido";



    public function status()
    {
        return $this->belongsTo(Status::class);
    }

}
