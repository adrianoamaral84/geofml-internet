<?php

namespace App\Http\Controllers\UserDocumento;

use App\User;
use App\UserDocumento;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class UserDocumentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(User $user, $tipo)
    {
        if ((int) auth()->id() !== (int) $user->id) {
            abort(403, 'Você não tem autorização para acessar este documento.');
        }

        if (!in_array($tipo, ['frente', 'verso'], true)) {
            abort(404);
        }

        $doc = UserDocumento::where('user_id', $user->id)
            ->where('tipo', $tipo)
            ->firstOrFail();

        if ($this->ehCaminhoPrivado($doc->arquivo)) {
            if (!Storage::disk('local')->exists($doc->arquivo)) {
                abort(404);
            }

            $conteudo = Storage::disk('local')->get($doc->arquivo);
            $mime = $this->mimeSeguro($doc->mime);

            return response($conteudo, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="documento-' . $tipo . '"',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store, max-age=0',
            ]);
        }

        // Compatibilidade temporária com registros antigos que armazenavam
        // o arquivo completo em base64 no banco.
        $conteudo = base64_decode((string) $doc->arquivo, true);

        if ($conteudo === false || $conteudo === '') {
            abort(404);
        }

        return response($conteudo, 200, [
            'Content-Type' => $this->mimeSeguro($doc->mime),
            'Content-Disposition' => 'inline; filename="documento-' . $tipo . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    public function showLegacy($id, $doc, $tipo)
    {
        try {
            $userId = Crypt::decrypt($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        // O parâmetro $doc existia apenas para transportar MIME na rota antiga.
        // Ele não é confiável e é deliberadamente ignorado.
        $tipoDocumento = (string) $tipo === '1' ? 'frente' : ((string) $tipo === '2' ? 'verso' : null);

        if ($tipoDocumento === null) {
            abort(404);
        }

        $user = User::findOrFail($userId);

        return $this->show($user, $tipoDocumento);
    }

    private function ehCaminhoPrivado($arquivo)
    {
        return is_string($arquivo)
            && strpos($arquivo, 'documentos/') === 0
            && strpos($arquivo, '..') === false;
    }

    private function mimeSeguro($mime)
    {
        $mapa = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'image/jpeg' => 'image/jpeg',
            'image/png' => 'image/png',
            'application/pdf' => 'application/pdf',
        ];

        $mime = strtolower((string) $mime);

        return $mapa[$mime] ?? 'application/octet-stream';
    }
}
