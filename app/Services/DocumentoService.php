<?php

namespace App\Services;

use App\User;
use App\UserDocumento;
use Illuminate\Http\UploadedFile;

class DocumentoService
{
    public function salvarFrente(User $user, UploadedFile $arquivo)
    {
        return $this->salvar($user, $arquivo, 'frente');
    }

    public function salvarVerso(User $user, UploadedFile $arquivo)
    {
        return $this->salvar($user, $arquivo, 'verso');
    }

    private function salvar(User $user, UploadedFile $arquivo, string $tipo)
{
    $conteudo = $arquivo->openFile()->fread($arquivo->getSize());
    $conteudoBase64 = base64_encode($conteudo);

    return UserDocumento::updateOrCreate(
        [
            'user_id' => $user->id,
            'tipo' => $tipo,
        ],
        [
            'arquivo' => $conteudoBase64,
            'mime' => $arquivo->extension(),
            'tamanho' => strlen($conteudoBase64),
            'hash' => hash('sha256', $conteudoBase64),
        ]
    );
}
    public function obterFrente(User $user)
{
    $documento = UserDocumento::where('user_id', $user->id)
        ->where('tipo', 'frente')
        ->first();

    if ($documento) {
        return $documento;
    }

    // Compatibilidade com banco antigo
    if (!empty($user->documento)) {

        return (object)[
            'arquivo' => $user->documento,
            'mime' => $user->tipo_doc
        ];
    }

    return null;
}
    

    public function obterVerso(User $user)
{
    $documento = UserDocumento::where('user_id', $user->id)
        ->where('tipo', 'verso')
        ->first();

    if ($documento) {
        return $documento;
    }

    if (!empty($user->documento_verso)) {

        return (object)[
            'arquivo' => $user->documento_verso,
            'mime' => $user->tipo_doc_verso
        ];
    }

    return null;
}

public function existeDocumento(User $user)
{
    return UserDocumento::where('user_id',$user->id)->exists();
}
public function excluir(User $user)
{
    UserDocumento::where('user_id',$user->id)->delete();
}

public function mimeFrente(User $user)
{
    $doc = $this->obterFrente($user);

    return $doc ? $doc->mime : null;
}
public function mimeVerso(User $user)
{
    $doc = $this->obterVerso($user);

    return $doc ? $doc->mime : null;
}
}