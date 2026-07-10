<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \App\User;
use Crypt;

class MailTeste extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        //dd($this->user);
        $this->subject('Cadastro Realizado!');
        $this->to("adriano.amaral84@gmail.com");
        $name = "Adriano";
        
        
        $senha = mt_rand(100000,99999999);
        //dd($number);

        return $this->markdown('mail.teste');

    }

    public function gerarCodInscricao($sigla) {
        $number = mt_rand(100000,99999999);
        if ($this->existeCodInscricao($number, $sigla))
                return $this->gerarCodInscricao();
        return $sigla . $number;
    }

    public function existeCodInscricao($number, $sigla) {
        $cod = $sigla. $number;
        return Inscricao::where('codigo',  '=', $cod)->exists();
    }
}
