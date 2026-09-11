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

    public function cancelarReserva($id)
    {
        $hospedagemId = Crypt::decrypt($id);

        $hospedagem = \App\Hospede::where('id', $hospedagemId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!in_array((int) $hospedagem->status, [2, 3, 5, 7], true)) {
            abort(403, 'Esta reserva não pode ser cancelada no estado atual.');
        }

        if ($hospedagem->checkin !== null) {
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
        $hospedagem->save();

        return redirect()->route('hospede.meuspedidos');
    }

    public function checkin($id)
    {
        $hospedagemId = Crypt::decrypt($id);
        $hospedagem = \App\Hospede::findOrFail($hospedagemId);

        if ((int) $hospedagem->status !== 2) {
            abort(403, 'O check-in só pode ser realizado em uma reserva aprovada.');
        }

        if ($hospedagem->checkin !== null) {
            abort(409, 'Esta hospedagem já possui movimentação de check-in/check-out.');
        }

        $hospedagem->checkin = 1;
        $hospedagem->checkin_at = now();
        $hospedagem->save();

        \Session::flash('message', [
            'msg' => 'Check-In realizado com sucesso!',
            'class' => 'success',
        ]);

        return redirect()->route('checkIn');
    }

    public function checkout($id)
    {
        $hospedagemId = Crypt::decrypt($id);
        $hospedagem = \App\Hospede::findOrFail($hospedagemId);

        if ((int) $hospedagem->status !== 2) {
            abort(403, 'O check-out só pode ser realizado em uma reserva aprovada.');
        }

        if ((int) $hospedagem->checkin !== 1) {
            abort(409, 'O check-out exige um check-in ativo.');
        }

        $hospedagem->checkin = 2;
        $hospedagem->checkout_user_id = Auth::id();
        $hospedagem->checkout_at = now();
        $hospedagem->save();

        \Session::flash('message', [
            'msg' => 'Check-Out realizado com sucesso!',
            'class' => 'success',
        ]);

        return redirect()->route('checkOut');
    }
}
