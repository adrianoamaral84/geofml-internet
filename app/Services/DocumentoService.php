<?php

namespace App\Services;

use App\User;
use App\UserDocumento;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $conteudo = file_get_contents($arquivo->getRealPath());

        if ($conteudo === false) {
            throw new \RuntimeException('Não foi possível ler o documento enviado.');
        }

        $extensao = strtolower($arquivo->getClientOriginalExtension() ?: $arquivo->extension() ?: 'bin');
        $nome = $tipo . '-' . Str::random(40) . '.' . $extensao;
        $diretorio = 'documentos/' . $user->id;
        $caminho = $diretorio . '/' . $nome;

        if (!Storage::disk('local')->put($caminho, $conteudo)) {
            throw new \RuntimeException('Não foi possível armazenar o documento enviado.');
        }

        $existente = UserDocumento::where('user_id', $user->id)
            ->where('tipo', $tipo)
            ->first();

        $caminhoAnterior = $existente ? $existente->arquivo : null;

        try {
            $documento = UserDocumento::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'tipo' => $tipo,
                ],
                [
                    'arquivo' => $caminho,
                    'mime' => $arquivo->getMimeType() ?: 'application/octet-stream',
                    'tamanho' => strlen($conteudo),
                    'hash' => hash('sha256', $conteudo),
                ]
            );
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($caminho);
            throw $e;
        }

        // Remove somente arquivos do padrão novo. Conteúdo legado em base64
        // permanece intocado até ser substituído/migrado com segurança.
        if (
            !empty($caminhoAnterior)
            && $caminhoAnterior !== $caminho
            && strpos($caminhoAnterior, 'documentos/') === 0
        ) {
            Storage::disk('local')->delete($caminhoAnterior);
        }

        return $documento;
    }

    public function obterFrente(User $user)
    {
        $documento = UserDocumento::where('user_id', $user->id)
            ->where('tipo', 'frente')
            ->first();

        if ($documento) {
            return $documento;
        }

        // Compatibilidade com banco antigo.
        if (!empty($user->documento)) {
            return (object) [
                'arquivo' => $user->documento,
                'mime' => $user->tipo_doc,
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
            return (object) [
                'arquivo' => $user->documento_verso,
                'mime' => $user->tipo_doc_verso,
            ];
        }

        return null;
    }

    public function existeDocumento(User $user)
    {
        return UserDocumento::where('user_id', $user->id)->exists();
    }

    public function excluir(User $user)
    {
        $documentos = UserDocumento::where('user_id', $user->id)->get();

        foreach ($documentos as $documento) {
            if (!empty($documento->arquivo) && strpos($documento->arquivo, 'documentos/') === 0) {
                Storage::disk('local')->delete($documento->arquivo);
            }
        }

        UserDocumento::where('user_id', $user->id)->delete();
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
