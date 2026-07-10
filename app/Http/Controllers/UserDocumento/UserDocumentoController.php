<?php


namespace App\Http\Controllers\UserDocumento;

use App\User;
use App\UserDocumento;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;


class UserDocumentoController extends Controller
{

public function show(User $user, $tipo)
{
// Ajuste esta regra conforme os perfis do GeoFML.
// Exemplo simples: usuario logado ou administrador.
if (auth()->id() !== $user->id && !auth()->user()->perfil_id) {
abort(403);
}

$doc = UserDocumento::where('user_id', $user->id)
->where('tipo', $tipo)
->firstOrFail();

if (!Storage::disk('local')->exists($doc->arquivo)) {
abort(404);
}

return response()->file(storage_path('app/' . $doc->arquivo));

}
}