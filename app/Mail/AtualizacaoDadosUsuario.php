<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \App\User;
use \App\Hospede;
use Crypt;


class AtualizacaoDadosUsuario extends Mailable
{
    use Queueable, SerializesModels;


    private $user;
    private $campos2;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $campos)
    {
        $camposs = $this->campos2 = $campos;
       
         return $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        


        $this->subject('Atualização de Usuário!');
        $this->to('fmarechalluz@gmail.com');
        $this->bcc('adriano.amaral84@gmail.com');
        
        $this->cc('fml@badmap5rm.eb.mil.br');

        
        $name = $this->user->name;
        $posto = $this->user->posto->sigla;
        
        $assinaturaCMT = \App\Assinatura::where('id', 1)->first();
        $assinaturaGestor = \App\Assinatura::where('id', 2)->first();
       
        return $this->markdown('mail.atualizadoDadosUsuario')->with([
                    'user' => $this->user,
                    'name' => $this->user->name,
                    'cpf' => $this->user->cpf,
                    'telefone' => $this->user->telefone,
                    'posto' => $posto,
                    'campos' => $this->campos2,
                    'assinaturaCMT' => $assinaturaCMT,
                    'assinaturaGestor' => $assinaturaGestor,
                    
                ]);
    }
}
