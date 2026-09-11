<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \App\User;
use Crypt;

class MailNovoCadastro extends Mailable
{
    use Queueable, SerializesModels;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $this->subject('Cadastro realizado - GEOFML');
        $this->to($this->user->email, $this->user->name);

        $posto = $this->user->posto->sigla;
        $id = Crypt::encrypt($this->user->id);

        $assinaturaCMT = \App\Assinatura::where('id', 1)->first();
        $assinaturaGestor = \App\Assinatura::where('id', 2)->first();
        $corpo = \App\GerenciarEmails::where('id', 1)->first();

        return $this->markdown('mail.novocadastro')->with([
            'user' => $this->user,
            'id' => $id,
            'posto' => $posto,
            'assinaturaCMT' => $assinaturaCMT,
            'assinaturaGestor' => $assinaturaGestor,
            'corpo' => optional($corpo)->corpo,
        ]);
    }

    public function gerarCodInscricao($sigla)
    {
        $number = mt_rand(100000, 99999999);
        if ($this->existeCodInscricao($number, $sigla)) {
            return $this->gerarCodInscricao($sigla);
        }

        return $sigla . $number;
    }

    public function existeCodInscricao($number, $sigla)
    {
        $cod = $sigla . $number;
        return Inscricao::where('codigo', '=', $cod)->exists();
    }
}
