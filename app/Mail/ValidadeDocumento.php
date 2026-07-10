<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \App\User;


class ValidadeDocumento extends Mailable
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
        $mensagem = \App\GerenciarEmails::where('id', 11)->first();
        $this->subject($mensagem->assunto);
        $this->to($this->user->email);
        $name = $this->user->name;
        $posto = $this->user->posto->sigla;
        $assinaturaCMT = \App\Assinatura::where('id', 1)->first();
        $assinaturaGestor = \App\Assinatura::where('id', 2)->first();
        $corpo = \App\GerenciarEmails::where('id', 1)->first();

        return $this->markdown('mail.enviaDocumentoVencido')->with([
                    'user' => $this->user->id,
                    'nome' => $name,        
                    'posto' => $posto,
                    'assinaturaCMT' => $assinaturaCMT,
                    'assinaturaGestor' => $assinaturaGestor,        
                    'cabecalho' => $mensagem->cabecalho,  
                    'corpo' => $mensagem->corpo,                    
                ]);
    }
    }

