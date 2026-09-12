<?php

namespace App\Http\Controllers\UserDocumento;

use App\User;
use App\UserDocumento;
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

        $doc = UserDocumento::where('user_id', $user->id)
            ->where('tipo', $tipo)
            ->firstOrFail();

        if (!Storage::disk('local')->exists($doc->arquivo)) {
            abort(404);
        }

        return response()->file(storage_path('app/' . $doc->arquivo));
    }
}
