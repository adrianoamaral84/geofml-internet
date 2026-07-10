<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Uf extends Model
{
    
    protected $table = "uf";
    
    public $timestamps = false;

    protected $fillable = [
        'sigla',
        'descricao',
    ];

    public static function listAll($search){
        //$search = "admin";
        //dd($search);
        if ($search == null) {           
            return Uf::paginate(10);
        } else {
            //return 'nao null';
            return Uf::where('descricao', 'LiKE', '%'.$search.'%')->paginate(10);
        }
    }

    public function cidade()
    {
    //return $this->belongsTo(User::class, 'campo_id');
    return $this->hasMany(Cidade::class, 'uf_id');
    }

    public function guarnicao()
    {
    //return $this->belongsTo(User::class, 'campo_id');
    return $this->hasMany(Guarnicao::class, 'uf_id');
    }

    
}
