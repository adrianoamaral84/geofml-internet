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

    public function cancelarHospedagem($id)
    {
        $hospedagemId = Crypt::decrypt($id);

        $hospedagem = \App\Hospede::where('id', $hospedagemId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $statusPermitidos = [2, 3, 5, 7];

        if (!in_array((int) $hospedagem->status, $statusPermitidos, true)) {
            abort(403, 'Esta reserva não pode ser cancelada neste status.');
        }

        if (!is_null($hospedagem->checkin)) {
            abort(403, 'Não é possível cancelar uma reserva após o check-in.');
        }

        if ((int) $hospedagem->status === 7) {
            \Illuminate\Support\Facades\Mail::queue(
                new \App\Mail\CancelaFilaEsperaUsuarioMail($hospedagem)
            );

            \Session::flash('message', [
                'msg' => 'Reserva cancelada com sucesso. Saiu da Fila de Espera.',
                'class' => 'success',
            ]);
        } else {
            \Illuminate\Support\Facades\Mail::queue(
                new \App\Mail\CancelaMail($hospedagem)
            );

            \Session::flash('message', [
                'msg' => 'Reserva cancelada com sucesso.',
                'class' => 'success',
            ]);
        }

        $hospedagem->status = 6;
        $hospedagem->update();

        return redirect()->route('hospede.meuspedidos');
    }
}
