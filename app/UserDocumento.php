<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDocumento extends Model
{
    protected $table = 'user_documentos';

    protected $fillable = [
        'user_id',
        'tipo',
        'arquivo',
        'mime',
        'tamanho',
        'hash',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}