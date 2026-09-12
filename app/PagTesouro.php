<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagTesouro extends Model
{
    protected $table = 'pagtesouros';
    public $timestamps = false;

    /**
     * Nunca expõe o token persistido na tabela em serializações.
     */
    protected $hidden = ['token'];

    /**
     * O Portal Internet não utiliza mais o token armazenado no banco.
     * O segredo deve existir somente na configuração do ambiente.
     */
    public function getTokenAttribute($value)
    {
        $token = config('services.pagtesouro.token');

        if (config('services.pagtesouro.modo_teste')) {
            return $token;
        }

        if (!is_string($token) || trim($token) === '') {
            throw new \RuntimeException(
                'PAGTESOURO_TOKEN não está configurado no ambiente.'
            );
        }

        return trim($token);
    }
}
