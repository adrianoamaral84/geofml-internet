<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Forca extends Model
{
    protected $table = "forca";
    
    public $timestamps = false;

    protected $fillable = [
        'forca',
    ];

    public static function listAll($search){
        //$search = "admin";
        //dd($search);
        if ($search == null) {           
            return Forca::paginate(10);
        } else {
            //return 'nao null';
            return Forca::where('forca', 'LiKE', '%'.$search.'%')->paginate(10);
        }
    }



/*
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
*/
public function OMS()
    {
    //return $this->belongsTo(User::class, 'campo_id');
    return $this->hasMany(GerenciarOm::class, 'forca_id');
    }
}
