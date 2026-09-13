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

    public function index()
    {
        date_default_timezone_set('America/Sao_Paulo');

        $usuarioAutenticado = Auth::user();
        $today = Carbon::today();

        if (
            (int) $usuarioAutenticado->indeterminado !== 1 &&
            !empty($usuarioAutenticado->validade)
        ) {
            $validade = Carbon::parse($usuarioAutenticado->validade)->startOfDay();

            if ($validade->lt($today)) {
                \Session::flash('message', [
                    'msg' => 'Seu documento de identidade está com a data de validade vencida! Favor atualizar o documento para prosseguir.',
                    'class' => 'danger',
                ]);

                return redirect()->route('home.home');
            }
        }

        $grupoTarifaIds = \App\GrupoTarifaPostoGraduacao::where(
            'posto_id',
            $usuarioAutenticado->postograd_id
        )
            ->orderBy('grupotarifa_id', 'DESC')
            ->pluck('grupotarifa_id')
            ->unique()
            ->values();

        if ($grupoTarifaIds->isEmpty()) {
            \Session::flash('message', [
                'msg' => 'Grupo de Tarifa não cadastrado! Contacte o administrador!',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

        $gruposTarifa = \App\GrupoTarifa::with('tipoundhabitacao')
            ->whereIn('id', $grupoTarifaIds)
            ->get();

        $unidadess = $gruposTarifa
            ->pluck('tipoundhabitacao')
            ->filter()
            ->unique('id')
            ->sortBy(function ($unidade) {
                return mb_strtolower($unidade->descricao);
            })
            ->map(function ($unidade) {
                return [
                    'id' => $unidade->id,
                    'value' => $unidade->descricao,
                ];
            })
            ->values()
            ->toArray();

        if (empty($unidadess)) {
            \Session::flash('message', [
                'msg' => 'Nenhuma unidade habitacional está vinculada ao grupo de tarifa do usuário.',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

        $diaBloqueado = \App\BloqueioDia::where('id', 1)->first();

        $diaAtual = (int) $today->day;
        $diaCorte = (int) $diaBloqueado->dia;
        $diaLimite = (int) $diaBloqueado->limitedia;
        $dataBase = $today->copy()->startOfMonth();

        $temporadaAtual = \App\Temporada::whereDate(
            'data_inicio',
            '<=',
            $today->format('Y-m-d')
        )
            ->whereDate(
                'data_termino',
                '>=',
                $today->format('Y-m-d')
            )
            ->first();

        if (!$temporadaAtual) {
            \Session::flash('message', [
                'msg' => 'Não existe temporada cadastrada para a data atual.',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

        $isAltaTemporada = (int) $temporadaAtual->tipo_temporada_id === 1;
        $isBaixaTemporada = (int) $temporadaAtual->tipo_temporada_id === 2;

        if ($diaAtual <= $diaCorte) {
            $mesAberto = $dataBase->copy()->addMonthNoOverflow();
        } else {
            $mesAberto = $dataBase->copy()->addMonthsNoOverflow(2);
        }

        $mesDoLimite = $mesAberto
            ->copy()
            ->addMonthNoOverflow()
            ->startOfMonth();

        $ultimoDiaMesLimite = $mesDoLimite
            ->copy()
            ->endOfMonth()
            ->day;

        $diaLimiteValido = min($diaLimite, $ultimoDiaMesLimite);

        $maxDate = $mesDoLimite
            ->copy()
            ->day($diaLimiteValido)
            ->format('Y-m-d');

        if ($isBaixaTemporada) {
            $minDate = $today->format('Y-m-d');
        } elseif ($isAltaTemporada) {
            $minDate = $mesAberto
                ->copy()
                ->startOfMonth()
                ->format('Y-m-d');
        } else {
            \Session::flash('message', [
                'msg' => 'Tipo de temporada inválido.',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

        $a = [];

        $bloquearDias = \App\LockDays::where('tipo', 1)->get();

        foreach ($bloquearDias as $bloqueio) {
            $a[] = [
                $bloqueio->data_inicio,
                $bloqueio->data_fim,
            ];
        }

        $bloquearDias2 = \App\LockDays::where('tipo', 2)->get();

        foreach ($bloquearDias2 as $bloqueio) {
            $a[] = $bloqueio->data_inicio;
        }

        if ($today->month === 11 && $diaAtual > $diaCorte) {
            $dezembro = $today
                ->copy()
                ->startOfMonth()
                ->addMonthNoOverflow();

            $a[] = [
                $dezembro->copy()->startOfMonth()->format('Y-m-d'),
                $dezembro->copy()->endOfMonth()->format('Y-m-d'),
            ];
        }

        $a = json_encode($a);

        $horario = \App\Horario::first();

        if (!$horario) {
            \Session::flash('message', [
                'msg' => 'Horários de entrada e saída não cadastrados.',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

        $minYear = Carbon::parse($minDate)->format('Y');
        $maxYear = Carbon::parse($maxDate)->format('Y');

        return view('hospedagem.create', compact(
            'diaBloqueado',
            'horario',
            'unidadess',
            'minDate',
            'maxDate',
            'minYear',
            'maxYear',
            'a'
        ));
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
            $comprovante = \App\Comprovante::where('hospedagem_id', $hospedagem->id)->first();

            if (!$comprovante) {
                $comprovante = new \App\Comprovante();
                $comprovante->hospedagem_id = $hospedagem->id;
            }

            $comprovante->arquivo = $conteudoBase64;
            $comprovante->tipo_doc = $extensao;
            $comprovante->save();

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
