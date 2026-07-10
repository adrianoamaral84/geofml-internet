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
        //$this->to('adriano.amaral84@gmail.com');
        //dd($this->hospede->undHB);
        $name = $this->hospede->user->name;
        
        //$classe = $this->hospede->undHB->classe->classe;
        //dd($this->hospede->undHB->sigla);
        $posto = $this->hospede->user->posto->sigla;
        //dd($classe);
        
        return $this->markdown('mail.hospedagemCanceladaFiladeEsperaUsuario')->with([
                    'user' => $this->hospede->id,
                    'nome' => $name,
                    'unidade' => $this->hospede->tipouh->descricao,
                    'data_inicio' => $this->hospede->data_inicio,
                    'data_termino' => $this->hospede->data_termino,
                    //'tipo_unidade' => $this->hospede->und_habitacionais_id,
                    'adultos' => $this->hospede->adultos,
                    'criancas' => $this->hospede->criancas,
                    'pne' => $this->hospede->pne,
                    'pet' => $this->hospede->pet,
                    'valor' => $this->hospede->valor,
                    'valortarifa' => $this->hospede->valortarifa,
                    'diarias' => $this->hospede->qntdiarias,
                    
                    //'classe' => $classe,
                    'posto' => $posto,
                ]);
        
    }
}
