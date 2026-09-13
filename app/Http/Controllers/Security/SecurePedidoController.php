<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SecurePedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
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

                return redirect()->route('home');
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

        if (!$diaBloqueado) {
            \Session::flash('message', [
                'msg' => 'Configuração do período de inscrições não encontrada.',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

        $diaAtual = (int) $today->day;
        $diaCorte = (int) $diaBloqueado->dia;
        $diaLimite = (int) $diaBloqueado->limitedia;
        $dataBase = $today->copy()->startOfMonth();

        $temporadaAtual = \App\Temporada::whereDate('data_inicio', '<=', $today->format('Y-m-d'))
            ->whereDate('data_termino', '>=', $today->format('Y-m-d'))
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

        $ultimoDiaMesLimite = $mesDoLimite->copy()->endOfMonth()->day;
        $diaLimiteValido = min($diaLimite, $ultimoDiaMesLimite);

        $maxDate = $mesDoLimite
            ->copy()
            ->day($diaLimiteValido)
            ->format('Y-m-d');

        if ($isBaixaTemporada) {
            $minDate = $today->format('Y-m-d');
        } elseif ($isAltaTemporada) {
            $minDate = $mesAberto->copy()->startOfMonth()->format('Y-m-d');
        } else {
            \Session::flash('message', [
                'msg' => 'Tipo de temporada inválido.',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

        $bloqueios = [];

        foreach (\App\LockDays::where('tipo', 1)->get() as $bloqueio) {
            $bloqueios[] = [$bloqueio->data_inicio, $bloqueio->data_fim];
        }

        foreach (\App\LockDays::where('tipo', 2)->get() as $bloqueio) {
            $bloqueios[] = $bloqueio->data_inicio;
        }

        if ($today->month === 11 && $diaAtual > $diaCorte) {
            $dezembro = $today->copy()->startOfMonth()->addMonthNoOverflow();

            $bloqueios[] = [
                $dezembro->copy()->startOfMonth()->format('Y-m-d'),
                $dezembro->copy()->endOfMonth()->format('Y-m-d'),
            ];
        }

        $a = json_encode($bloqueios);
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

        return view('hospedagem.cadastrar_pedido', compact(
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
}
