<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class SecureEditarPedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        date_default_timezone_set('America/Sao_Paulo');

        try {
            $hospedagemId = Crypt::decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404);
        }

        $usuarioAutenticado = Auth::user();
        $today = Carbon::today();

        $hospedagem = \App\Hospede::where('id', $hospedagemId)
            ->where('user_id', $usuarioAutenticado->id)
            ->first();

        if (!$hospedagem) {
            abort(403, 'Pedido não encontrado ou você não possui permissão para editá-lo.');
        }

        $statusPermitidos = [0];

        if (!in_array($hospedagem->status, $statusPermitidos, true)) {
            \Session::flash('message', [
                'msg' => 'Este pedido não pode mais ser editado.',
                'class' => 'danger',
            ]);

            return redirect()->route('hospede.meuspedidos');
        }

        if (
            (int) $usuarioAutenticado->indeterminado !== 1 &&
            !empty($usuarioAutenticado->validade)
        ) {
            try {
                $validade = Carbon::parse($usuarioAutenticado->validade)->startOfDay();
            } catch (\Throwable $e) {
                \Session::flash('message', [
                    'msg' => 'A data de validade do documento está inválida.',
                    'class' => 'danger',
                ]);

                return redirect()->route('home.home');
            }

            if ($validade->lt($today)) {
                \Session::flash('message', [
                    'msg' => 'Seu documento de identidade está com a data de validade vencida! Favor atualizar o documento para prosseguir.',
                    'class' => 'danger',
                ]);

                return redirect()->route('home.home');
            }
        }

        $tipos = \App\TipoUndHab::all();

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

        $unidades = $gruposTarifa
            ->pluck('tipoundhabitacao')
            ->filter()
            ->unique('id')
            ->sortBy(function ($unidade) {
                return mb_strtolower((string) $unidade->descricao);
            })
            ->values();

        $unidadess = $unidades
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

        $diaBloqueado = \App\BloqueioDia::find(1);

        if (!$diaBloqueado) {
            \Session::flash('message', [
                'msg' => 'Configuração do período de inscrições não encontrada.',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

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

        if (!in_array((int) $temporadaAtual->tipo_temporada_id, [1, 2], true)) {
            \Session::flash('message', [
                'msg' => 'Tipo de temporada inválido.',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

        try {
            $janela = $this->calcularJanelaInscricao($today, $diaBloqueado, $temporadaAtual);
        } catch (\Throwable $e) {
            report($e);

            \Session::flash('message', [
                'msg' => 'Não foi possível calcular o período permitido para hospedagem.',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

        $minDate = $janela['minDate'];
        $maxDate = $janela['maxDate'];

        $diaAtual = (int) $today->day;
        $diaCorte = (int) $diaBloqueado->dia;
        $dataBase = $today->copy()->startOfMonth();
        $bloqueios = [];

        foreach (\App\LockDays::where('tipo', 1)->get() as $bloqueio) {
            if (!empty($bloqueio->data_inicio) && !empty($bloqueio->data_fim)) {
                $bloqueios[] = [$bloqueio->data_inicio, $bloqueio->data_fim];
            }
        }

        foreach (\App\LockDays::where('tipo', 2)->get() as $bloqueio) {
            if (!empty($bloqueio->data_inicio)) {
                $bloqueios[] = $bloqueio->data_inicio;
            }
        }

        if ((int) $today->month === 11 && $diaAtual > $diaCorte) {
            $dezembro = $dataBase->copy()->addMonthNoOverflow()->startOfMonth();

            $bloqueios[] = [
                $dezembro->copy()->startOfMonth()->format('Y-m-d'),
                $dezembro->copy()->endOfMonth()->format('Y-m-d'),
            ];
        }

        $a = json_encode(
            $bloqueios,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        $horario = \App\Horario::first();

        if (!$horario) {
            \Session::flash('message', [
                'msg' => 'Horários de entrada e saída não cadastrados.',
                'class' => 'danger',
            ]);

            return redirect()->back();
        }

        try {
            $peridoinicial = Carbon::parse($hospedagem->data_inicio)->format('d-m-Y')
                . ' - '
                . Carbon::parse($hospedagem->data_termino)->format('d-m-Y');
        } catch (\Throwable $e) {
            \Session::flash('message', [
                'msg' => 'O pedido possui uma data de hospedagem inválida.',
                'class' => 'danger',
            ]);

            return redirect()->route('hospede.meuspedidos');
        }

        $minYear = (int) Carbon::parse($minDate)->format('Y');
        $maxYear = (int) Carbon::parse($maxDate)->format('Y');

        return view('hospedagem.edit_inscricao', compact(
            'hospedagem',
            'peridoinicial',
            'tipos',
            'unidades',
            'unidadess',
            'diaBloqueado',
            'horario',
            'minDate',
            'maxDate',
            'minYear',
            'maxYear',
            'a'
        ));
    }

    private function calcularJanelaInscricao(
        Carbon $today,
        $diaBloqueado,
        $temporadaAtual
    ): array {
        $diaAtual = (int) $today->day;
        $diaCorte = (int) $diaBloqueado->dia;
        $diaLimite = (int) $diaBloqueado->limitedia;

        $dataBase = $today->copy()->startOfMonth();

        if ($diaAtual <= $diaCorte) {
            $mesAberto = $dataBase->copy()->addMonthNoOverflow();
        } else {
            $mesAberto = $dataBase->copy()->addMonthsNoOverflow(2);
        }

        $mesDoLimite = $mesAberto
            ->copy()
            ->addMonthNoOverflow()
            ->startOfMonth();

        $diaLimiteValido = min(
            $diaLimite,
            $mesDoLimite->copy()->endOfMonth()->day
        );

        $maxDate = $mesDoLimite->copy()->day($diaLimiteValido);

        $isAltaTemporada = (int) $temporadaAtual->tipo_temporada_id === 1;
        $isBaixaTemporada = (int) $temporadaAtual->tipo_temporada_id === 2;

        if ($isBaixaTemporada) {
            $minDate = $today->copy();
        } elseif ($isAltaTemporada) {
            $minDate = $mesAberto->copy()->startOfMonth();
        } else {
            throw new \RuntimeException('Tipo de temporada inválido.');
        }

        if ((int) $today->month === 2 && $diaAtual > $diaCorte) {
            $minDate = $dataBase->copy()->addMonthNoOverflow()->startOfMonth();
        }

        return [
            'minDate' => $minDate->format('Y-m-d'),
            'maxDate' => $maxDate->format('Y-m-d'),
        ];
    }
}
