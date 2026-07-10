<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \App\Hospede;

class CancelaFilaEsperaUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    private $hospede;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Hospede $hospede)
    {
       
        return $this->hospede = $hospede;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        $this->subject('Hospedagem Cancelada pelo Usuário na Fila de Espera!');
        $this->to('fmarechalluz@gmail.com');
        $this->bcc('adriano.amaral84@gmail.com');
        $this->cc('fml@badmap5rm.eb.mil.br');
        $name = $this->hospede->user->name; 
        $posto = $this->hospede->user->posto->sigla;

        $assinaturaCMT = \App\Assinatura::where('id', 1)->first();
        $assinaturaGestor = \App\Assinatura::where('id', 2)->first();
           
        return $this->markdown('mail.hospedagemCanceladaFiladeEsperaUsuario')->with([
                    'user' => $this->hospede->id,
                    'nome' => $name,
                    'unidade' => $this->hospede->tipouh->descricao,
                    'data_inicio' => $this->hospede->data_inicio,
                    'data_termino' => $this->hospede->data_termino,
                    'adultos' => $this->hospede->adultos,
                    'criancas' => $this->hospede->criancas,
                    'pne' => $this->hospede->pne,
                    'pet' => $this->hospede->pet,
                    'valor' => $this->hospede->valor,
                    'valortarifa' => $this->hospede->valortarifa,
                    'diarias' => $this->hospede->qntdiarias,
                    'posto' => $posto,
                    'assinaturaCMT' => $assinaturaCMT,
                    'assinaturaGestor' => $assinaturaGestor,
        ]);
        
    }
}
