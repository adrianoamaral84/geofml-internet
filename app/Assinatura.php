<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{
    protected $table = "assinatura";
    //


    public function posto()
    {
        return $this->belongsTo(PostoGraduacao::class, 'posto_id');
    }
}
