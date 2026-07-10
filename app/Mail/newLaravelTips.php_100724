<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \App\User;
use Crypt;

class newLaravelTips extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        return $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        //dd($this->user);
        $this->subject('Solicitação de Acesso!');
        $this->to($this->user->email, $this->user->name);
        $name = $this->user->name;
        //$posto = $this->user->posto->sigla;
        
        $id = Crypt::encrypt($this->user->id);
        
        $senha = mt_rand(100000,99999999);
        //dd($number);

        return $this->markdown('mail.newLaravelTips')->with([
                    'user' => $this->user,
                    'id' => $id,
                    'senha' => $senha,
                    //'posto' => $posto,
                ]);

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
