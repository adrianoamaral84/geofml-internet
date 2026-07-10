<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoUndHab extends Model
{
   protected $table = "tipoundhab";
   public $timestamps = false;
   
    public function findByBatata($type, $date, $format = 'Y-m') {
    // Considerando data no formato 2016-02
    $dateTime = \DateTime::createFromFormat($format, $date);
    //dd($dateTime);
    // o primeiro dia do mês hard coded
    $dateStart = $dateTime->format('Y-m-01');
    //dd($dateStart);

    // t é equivalente ao último dia do mês
    $dateEnd = $dateTime->format('Y-m-t');

    return \App\Hospede::
         whereBetween('data_inicio', [$dateStart, $dateEnd])
        ->where('user_id', $type)
         ->get();
    }
}
