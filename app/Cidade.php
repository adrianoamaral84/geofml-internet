<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    
	protected $table = "cidade";
	public $timestamps = false;

    protected $fillable = [
        'descricao',
    ];
    protected $hidden = [
        'updated_at', 'created_at',
    ];


    public function uf(){
        return $this->belongsTo(Uf::class);
    }

    public static function listAll($search, $id){
        //$search = "admin";
        //dd($id);
        if ($search == null) {           
            $uf = \App\Uf::find($id);
            
            return $uf->cidade()->get();
        } else {
            //return 'nao null';
            $uf = \App\Uf::find($id);
            return $uf->cidade()->where('descricao', 'LiKE', '%'.$search.'%')->paginate(10);
        }
    }
}
