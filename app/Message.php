<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    

    protected $table = "message";


    public static function newMessage($to){

    	//return 'ok';
    	//dd('message');
    	$verifica = \App\Message::where('lido', null)->where('to_user_id', $to)->get();
    	return $verifica;
    	//dd($verifica);

    }

    public static function countMsg($to){

    	$count = \App\Message::where('to_user_id', $to)->where('lido', null)->count();
    	return $count;
    }
}
