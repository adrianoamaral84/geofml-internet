<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class SecureHospedeActionsController extends Controller
{
    public function deletePedido($id)
    {
        $pedidoId = Crypt::decrypt($id);

        $hospedagem = \App\Hospede::where('id', $pedidoId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ((int) $hospedagem->status !== 0) {
            abort(403, 'Somente solicitações aguardando aprovação podem ser excluídas.');
        }

        $hospedagem->delete();

        \Session::flash('message', [
            'msg' => 'Solicitação excluída com sucesso.',
            'class' => 'success',
        ]);

        return redirect()->route('hospede.meuspedidos');
    }
}
