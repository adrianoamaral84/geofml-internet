<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GerenciarOm extends Model
{
    
    public $timestamps = false;
    
    public static function listAll($search){
        //$search = "admin";
        //dd($search);
        if ($search == null) {           
            return GerenciarOm::paginate(10);
        } else {
            //return 'nao null';
            return GerenciarOm::where('descricao', 'LiKE', '%'.$search.'%')->paginate(10);
        }
    }
    
    
    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }

    public function forca()
    {
        return $this->belongsTo(Forca::class);
    }

    
}
