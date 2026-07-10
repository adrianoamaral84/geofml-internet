<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \App\User;
use \App\Hospede;
use Crypt;


class hospedagemLiberada extends Mailable
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
       
        $mensagem = \App\GerenciarEmails::where('id', 1)->first();
        $this->subject($mensagem->assunto);
        $this->to($this->hospede->user->email);
        $nome = $this->hospede->user->name;
        $posto = $this->hospede->user->posto->sigla; 

        $assinaturaCMT = \App\Assinatura::where('id', 1)->first();
        $assinaturaGestor = \App\Assinatura::where('id', 2)->first();
        

        return $this->markdown('mail.hospedagemLiberada')->with([
              
                    'user' => $this->hospede->id,
                    'nome' => $nome,
                    'unidade' => $this->hospede->tipouh->descricao,
                    'data_inicio' => $this->hospede->data_inicio,
                    'data_termino' => $this->hospede->data_termino,
                    'tipo_unidade' => $this->hospede->und_habitacionais_id,
                    'adultos' => $this->hospede->adultos,
                    'criancas' => $this->hospede->criancas,
                    'pne' => $this->hospede->pne,
                    'pet' => $this->hospede->pet,
                    'valor' => $this->hospede->valor,
                    'valortarifa' => $this->hospede->valortarifa,
                    'diarias' => $this->hospede->qntdiarias,
                    'uh' => $this->hospede->undHB->sigla,
                    'capacidade_ocupacao' => $this->hospede->undHB->capacidade_ocupacao,
                    'classe' => $this->hospede->undHB->classe,
                    'uh' => $this->hospede->undHB->sigla,
                    'capacidade_ocupacao' => $this->hospede->undHB->capacidade_ocupacao,
                    'posto' => $posto,
                    'assinaturaCMT' => $assinaturaCMT,
                    'assinaturaGestor' => $assinaturaGestor,
                    'cabecalho' => $mensagem->cabecalho,  
                    'corpo' => $mensagem->corpo,    
                    
                    ]);
    }
}
