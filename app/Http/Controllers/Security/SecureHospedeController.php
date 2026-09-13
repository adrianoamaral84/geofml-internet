<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Services\CalculoHospedagemService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SecureHospedeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function meuspedidos()
    {
        $consulta = \App\Hospede::where('user_id', Auth::id())
            ->orderByDesc('id')
            ->with('tipouh')
            ->paginate(20);

        return view('meuspedidos.index', compact('consulta'));
    }

    public function meupedido($id)
    {
        try {
            $hospedagemId = Crypt::decrypt($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        $hospedagem = \App\Hospede::with('user')
            ->where('id', $hospedagemId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $comprovante = \App\Comprovante::where('hospedagem_id', $hospedagem->id)
            ->orderByDesc('created_at')
            ->first();

        $arquivo = $comprovante->arquivo ?? '';
        $unidades_habitacionais = \App\UnidadeHabitacional::where('disponivel', 1)->get();
        $horario = \App\Horario::first();
        $hoje = Carbon::now()->format('Y-m-d');

        $CheckInAntecipado = false;
        $CheckOutAtrasado = false;
        $valorPagarRestante = 0;

        if ($hospedagem->checkin_at !== null && (int) $hospedagem->checkin === 1) {
            $calculo = (new CalculoHospedagemService())->calcular($hospedagem);

            $CheckInAntecipado = $calculo['checkin_antecipado'];
            $CheckOutAtrasado = $calculo['checkout_atrasado'];
            $valorPagarRestante = $calculo['valor_restante'];

            $hospedagem->valor_restante = $calculo['valor_restante'];
            $hospedagem->qntdiarias = $calculo['dias'];
            $hospedagem->valor = $calculo['valor_total'];
            $hospedagem->save();
        }

        $cancelar = 1;

        return view('meuspedidos.meu_pedido', compact(
            'CheckInAntecipado',
            'CheckOutAtrasado',
            'hospedagem',
            'unidades_habitacionais',
            'horario',
            'comprovante',
            'arquivo',
            'cancelar',
            'hoje',
            'valorPagarRestante'
        ));
    }

    public function uploadComprovantePagamento(Request $request)
    {
        $validated = $request->validate([
            'hospedagem_id' => 'required|integer',
            'documento' => 'required|file|mimes:jpeg,jpg,png,pdf|max:4048',
        ], [
            'documento.required' => 'Falta anexar o comprovante de pagamento!',
        ]);

        $hospedagem = \App\Hospede::where('id', $validated['hospedagem_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $arquivo = $request->file('documento');

        if (!$arquivo->isValid()) {
            return back()->withInput()->withErrors([
                'documento' => 'Arquivo inválido.',
            ]);
        }

        $conteudo = $arquivo->openFile()->fread($arquivo->getSize());
        $conteudoBase64 = base64_encode($conteudo);
        $extensao = strtolower($arquivo->extension());

        DB::transaction(function () use ($hospedagem, $conteudoBase64, $extensao) {
            \App\Comprovante::updateOrCreate(
                ['hospedagem_id' => $hospedagem->id],
                [
                    'arquivo' => $conteudoBase64,
                    'tipo_doc' => $extensao,
                ]
            );

            $hospedagem->status = 4;
            $hospedagem->save();
        });

        \Session::flash('message', [
            'msg' => 'Comprovante salvo com sucesso.',
            'class' => 'success',
        ]);

        return redirect()->back();
    }
}
