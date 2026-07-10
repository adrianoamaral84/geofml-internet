<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GerenciarEmails extends Model
{
   	protected $table = "gerenciador_emails";
    //

    public function getTestimonialExcerptAttribute()
    {
        return Str::words($this->consulta, '25');
    }
}
