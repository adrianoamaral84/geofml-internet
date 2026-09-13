<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

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

    public function confirmar(Request $request)
    {
        $usuario = Auth::user();
        $today = Carbon::today();

        $validator = Validator::make($request->all(), [
            'peridoinicial' => ['required', 'string'],
            'tipo' => ['required', 'integer'],
            'adultos' => ['required', 'integer', 'min:1'],
            'criancas' => ['required', 'integer', 'min:0'],
            'pne' => ['required', 'in:0,1'],
            'pet' => ['required', 'in:0,1'],
            'observacao' => ['nullable', 'string', 'max:250'],
        ], [
            'peridoinicial.required' => 'Informe o período de hospedagem.',
            'tipo.required' => 'Selecione o tipo de unidade habitacional.',
            'tipo.integer' => 'O tipo de unidade habitacional é inválido.',
            'adultos.required' => 'Informe a quantidade de adultos.',
            'adultos.integer' => 'A quantidade de adultos deve ser um número inteiro.',
            'adultos.min' => 'Deve haver pelo menos um adulto.',
            'criancas.required' => 'Informe a quantidade de crianças.',
            'criancas.integer' => 'A quantidade de crianças deve ser um número inteiro.',
            'criancas.min' => 'A quantidade de crianças não pode ser negativa.',
            'pne.required' => 'Informe se haverá hóspede PNE.',
            'pet.required' => 'Informe se haverá PET.',
            'observacao.max' => 'A observação deve possuir no máximo 250 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ((int) $usuario->indeterminado !== 1 && !empty($usuario->validade)) {
            try {
                $validade = Carbon::parse($usuario->validade)->startOfDay();
            } catch (\Throwable $e) {
                \Session::flash('message', [
                    'msg' => 'A data de validade do documento está inválida.',
                    'class' => 'danger',
                ]);

                return redirect()->route('home');
            }

            if ($validade->lt($today)) {
                \Session::flash('message', [
                    'msg' => 'Seu documento de identidade está vencido. Atualize o documento para prosseguir.',
                    'class' => 'danger',
                ]);

                return redirect()->route('home');
            }
        }

        try {
            $partesPeriodo = array_map('trim', explode(' - ', trim($request->peridoinicial)));

            if (count($partesPeriodo) !== 2) {
                throw new \InvalidArgumentException('Período inválido.');
            }

            $entradaTexto = $partesPeriodo[0];
            $saidaTexto = $partesPeriodo[1];

            $dataInicio = Carbon::createFromFormat('d-m-Y', $entradaTexto)->startOfDay();
            $dataTermino = Carbon::createFromFormat('d-m-Y', $saidaTexto)->startOfDay();

            if (
                $dataInicio->format('d-m-Y') !== $entradaTexto ||
                $dataTermino->format('d-m-Y') !== $saidaTexto
            ) {
                throw new \InvalidArgumentException('Uma das datas é inválida.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->withErrors([
                'peridoinicial' => 'O período informado possui um formato inválido.',
            ]);
        }

        if ($dataInicio->gte($dataTermino)) {
            return redirect()->back()->withInput()->withErrors([
                'peridoinicial' => 'A data de saída deve ser posterior à data de entrada.',
            ]);
        }

        $diasHospedagem = $dataInicio->diffInDays($dataTermino);
        $diaBloqueado = \App\BloqueioDia::find(1);

        if (!$diaBloqueado) {
            \Session::flash('message', [
                'msg' => 'Configuração do período de inscrições não encontrada.',
                'class' => 'danger',
            ]);

            return redirect()->back()->withInput();
        }

        $temporadaAtual = \App\Temporada::whereDate('data_inicio', '<=', $today->format('Y-m-d'))
            ->whereDate('data_termino', '>=', $today->format('Y-m-d'))
            ->first();

        if (!$temporadaAtual) {
            \Session::flash('message', [
                'msg' => 'Não existe temporada cadastrada para a data atual.',
                'class' => 'danger',
            ]);

            return redirect()->back()->withInput();
        }

        if (!in_array((int) $temporadaAtual->tipo_temporada_id, [1, 2], true)) {
            \Session::flash('message', [
                'msg' => 'O tipo da temporada atual é inválido.',
                'class' => 'danger',
            ]);

            return redirect()->back()->withInput();
        }

        try {
            $janela = $this->calcularJanelaInscricao($today, $diaBloqueado, $temporadaAtual);
        } catch (\Throwable $e) {
            \Session::flash('message', [
                'msg' => 'Não foi possível calcular o período de inscrições.',
                'class' => 'danger',
            ]);

            return redirect()->back()->withInput();
        }

        $minDate = Carbon::parse($janela['minDate'])->startOfDay();
        $maxDate = Carbon::parse($janela['maxDate'])->startOfDay();

        if ($dataInicio->lt($minDate) || $dataTermino->gt($maxDate)) {
            return redirect()->back()->withInput()->withErrors([
                'peridoinicial' => 'O período deve estar entre '
                    . $minDate->format('d/m/Y') . ' e '
                    . $maxDate->format('d/m/Y') . '.',
            ]);
        }

        $diaCorte = (int) $diaBloqueado->dia;

        if ((int) $today->month === 11 && (int) $today->day > $diaCorte) {
            $inicioDezembro = $today->copy()->startOfMonth()->addMonthNoOverflow()->startOfMonth();
            $fimDezembro = $inicioDezembro->copy()->endOfMonth();

            $periodoPassaPorDezembro =
                $dataInicio->lte($fimDezembro) &&
                $dataTermino->gt($inicioDezembro);

            if ($periodoPassaPorDezembro) {
                return redirect()->back()->withInput()->withErrors([
                    'peridoinicial' => 'As inscrições para dezembro encerraram no dia '
                        . $diaCorte . ' de novembro.',
                ]);
            }
        }

        if ($this->periodoPossuiBloqueio($dataInicio, $dataTermino)) {
            return redirect()->back()->withInput()->withErrors([
                'peridoinicial' => 'O período selecionado contém uma data bloqueada pela administração.',
            ]);
        }

        $grupoTarifaIds = \App\GrupoTarifaPostoGraduacao::where(
                'posto_id',
                $usuario->postograd_id
            )
            ->pluck('grupotarifa_id')
            ->unique()
            ->values();

        if ($grupoTarifaIds->isEmpty()) {
            \Session::flash('message', [
                'msg' => 'Grupo de tarifa não cadastrado para seu posto/graduação.',
                'class' => 'danger',
            ]);

            return redirect()->back()->withInput();
        }

        $grupoUnidade = \App\GrupoTarifa::whereIn('id', $grupoTarifaIds)
            ->where('unidade_habitacional_id', $request->tipo)
            ->first();

        if (!$grupoUnidade) {
            return redirect()->back()->withInput()->withErrors([
                'tipo' => 'A unidade selecionada não está disponível para seu posto/graduação.',
            ]);
        }

        $tarifa = \App\Tarifas::where('tipoundhab_id', $request->tipo)
            ->whereIn('grupo_destinacao_id', $grupoTarifaIds)
            ->first();

        if (!$tarifa) {
            \Session::flash('message', [
                'msg' => 'Tarifa não cadastrada para a unidade e grupo do usuário.',
                'class' => 'danger',
            ]);

            return redirect()->back()->withInput();
        }

        $temporadasDoPeriodo = \App\Temporada::whereDate(
                'data_inicio',
                '<',
                $dataTermino->format('Y-m-d')
            )
            ->whereDate('data_termino', '>=', $dataInicio->format('Y-m-d'))
            ->orderBy('data_inicio')
            ->get();

        if ($temporadasDoPeriodo->isEmpty()) {
            \Session::flash('message', [
                'msg' => 'Não existe temporada cadastrada para o período selecionado.',
                'class' => 'danger',
            ]);

            return redirect()->back()->withInput();
        }

        $diasAltaTemporada = 0;
        $diasBaixaTemporada = 0;

        for ($data = $dataInicio->copy(); $data->lt($dataTermino); $data->addDay()) {
            $temporadaDaDiaria = $temporadasDoPeriodo->first(function ($temporada) use ($data) {
                $inicioTemporada = Carbon::parse($temporada->data_inicio)->startOfDay();
                $terminoTemporada = Carbon::parse($temporada->data_termino)->endOfDay();

                return $data->gte($inicioTemporada) && $data->lte($terminoTemporada);
            });

            if (!$temporadaDaDiaria) {
                \Session::flash('message', [
                    'msg' => 'A data ' . $data->format('d/m/Y') . ' não pertence a nenhuma temporada cadastrada.',
                    'class' => 'danger',
                ]);

                return redirect()->back()->withInput();
            }

            if ((int) $temporadaDaDiaria->tipo_temporada_id === 1) {
                $diasAltaTemporada++;
            } elseif ((int) $temporadaDaDiaria->tipo_temporada_id === 2) {
                $diasBaixaTemporada++;
            } else {
                \Session::flash('message', [
                    'msg' => 'Existe uma temporada com tipo inválido.',
                    'class' => 'danger',
                ]);

                return redirect()->back()->withInput();
            }
        }

        if (($diasAltaTemporada + $diasBaixaTemporada) !== $diasHospedagem) {
            \Session::flash('message', [
                'msg' => 'Não foi possível classificar todas as diárias do período selecionado.',
                'class' => 'danger',
            ]);

            return redirect()->back()->withInput();
        }

        $possuiAltaTemporada = $diasAltaTemporada > 0;

        if ($possuiAltaTemporada && $diasHospedagem > 7) {
            return redirect()->back()->withInput()->withErrors([
                'peridoinicial' => 'Na alta temporada, o período máximo é de 7 diárias.',
            ]);
        }

        $quantidadeReservas = \App\QuantidadeReserva::first();

        if (!$quantidadeReservas) {
            \Session::flash('message', [
                'msg' => 'Limite máximo de reservas não configurado.',
                'class' => 'danger',
            ]);

            return redirect()->back()->withInput();
        }

        $consultaPedidosDoMes = \App\Hospede::where('user_id', $usuario->id)
            ->whereYear('data_inicio', $dataInicio->year)
            ->whereMonth('data_inicio', $dataInicio->month);

        $pedidosDoMes = (clone $consultaPedidosDoMes)->count();
        $pedidosCampingMotorhome = (clone $consultaPedidosDoMes)
            ->whereIn('tipo_und_id', [11, 12])
            ->count();

        $tipoCampingMotorhome = in_array((int) $request->tipo, [11, 12], true);

        if ($tipoCampingMotorhome) {
            if ($pedidosCampingMotorhome >= 2) {
                return redirect()->back()->withInput()->withErrors([
                    'tipo' => 'Você já alcançou o limite de 2 pedidos para Camping e/ou Motor-Home neste mês.',
                ]);
            }
        } else {
            $limiteMensal = $possuiAltaTemporada
                ? (int) $quantidadeReservas->reservas
                : (int) $quantidadeReservas->qnt_reservas_baixa_temporada;

            if ($pedidosDoMes >= $limiteMensal) {
                return redirect()->back()->withInput()->withErrors([
                    'peridoinicial' => 'Você já alcançou o limite de '
                        . $limiteMensal . ' pedido(s) para esse mês.',
                ]);
            }
        }

        $valorDiariaAlta = round((float) $tarifa->valor, 2);
        $valorDiariaBaixa = round((float) $tarifa->valor_baixa, 2);

        $calculaDiariaAlta = round($diasAltaTemporada * $valorDiariaAlta, 2);
        $calculaDiariaBaixa = round($diasBaixaTemporada * $valorDiariaBaixa, 2);
        $totalValorBruto = round($calculaDiariaAlta + $calculaDiariaBaixa, 2);

        $isMecenas = (bool) $usuario->mecenas;
        $percentualDesconto = (float) $usuario->percentual_desconto;

        $totalValorLiquido = round((float) $usuario->aplicarDesconto($totalValorBruto), 2);
        $valorDescontoMecenas = round($totalValorBruto - $totalValorLiquido, 2);

        $valorDiariaAltaComDesconto = round((float) $usuario->aplicarDesconto($valorDiariaAlta), 2);
        $valorDiariaBaixaComDesconto = round((float) $usuario->aplicarDesconto($valorDiariaBaixa), 2);
        $calculaDiariaAltaComDesconto = round((float) $usuario->aplicarDesconto($calculaDiariaAlta), 2);
        $calculaDiariaBaixaComDesconto = round((float) $usuario->aplicarDesconto($calculaDiariaBaixa), 2);

        if ($diasAltaTemporada > 0 && $diasBaixaTemporada === 0) {
            $valorTarifa = $valorDiariaAlta;
            $valorTarifaComDesconto = $valorDiariaAltaComDesconto;
        } elseif ($diasBaixaTemporada > 0 && $diasAltaTemporada === 0) {
            $valorTarifa = $valorDiariaBaixa;
            $valorTarifaComDesconto = $valorDiariaBaixaComDesconto;
        } else {
            $valorTarifa = $valorDiariaAlta;
            $valorTarifaComDesconto = $valorDiariaAltaComDesconto;
        }

        $request->merge([
            'data_inicio' => $dataInicio->format('Y-m-d'),
            'data_termino' => $dataTermino->format('Y-m-d'),
            'dias_hospedagem' => $diasHospedagem,
            'dias_alta_temporada' => $diasAltaTemporada,
            'dias_baixa_temporada' => $diasBaixaTemporada,
            'mecenas' => $isMecenas ? 1 : 0,
            'percentual_desconto' => $percentualDesconto,
        ]);

        $consulta = $request;
        $tipos = \App\TipoUndHab::all();
        $totalValorCriptografado = Crypt::encrypt($totalValorLiquido);

        return view('hospedagem.confirmarantigo', [
            'consulta' => $consulta,
            'tipos' => $tipos,
            'diasHospedagem' => $diasHospedagem,
            'diasAltaTemporada' => $diasAltaTemporada,
            'diasBaixaTemporada' => $diasBaixaTemporada,
            'valorDiariaAlta' => $valorDiariaAlta,
            'valorDiariaBaixa' => $valorDiariaBaixa,
            'valorDiariaAltaComDesconto' => $valorDiariaAltaComDesconto,
            'valorDiariaBaixaComDesconto' => $valorDiariaBaixaComDesconto,
            'calculaDiariaAlta' => $calculaDiariaAlta,
            'calculaDiariaBaixa' => $calculaDiariaBaixa,
            'calculaDiariaAltaComDesconto' => $calculaDiariaAltaComDesconto,
            'calculaDiariaBaixaComDesconto' => $calculaDiariaBaixaComDesconto,
            'isMecenas' => $isMecenas,
            'percentualDesconto' => $percentualDesconto,
            'valorDescontoMecenas' => $valorDescontoMecenas,
            'totalValorBruto' => $totalValorBruto,
            'totalValorLiquido' => $totalValorLiquido,
            'totalValor' => $totalValorCriptografado,
            'valorTarifa' => $valorTarifa,
            'valorTarifaComDesconto' => $valorTarifaComDesconto,
        ]);
    }

    private function calcularJanelaInscricao(Carbon $today, $diaBloqueado, $temporadaAtual): array
    {
        $diaAtual = (int) $today->day;
        $diaCorte = (int) $diaBloqueado->dia;
        $diaLimite = (int) $diaBloqueado->limitedia;
        $dataBase = $today->copy()->startOfMonth();

        if ($diaAtual <= $diaCorte) {
            $mesAberto = $dataBase->copy()->addMonthNoOverflow();
        } else {
            $mesAberto = $dataBase->copy()->addMonthsNoOverflow(2);
        }

        $mesDoLimite = $mesAberto->copy()->addMonthNoOverflow()->startOfMonth();
        $diaLimiteValido = min($diaLimite, $mesDoLimite->copy()->endOfMonth()->day);
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

    private function periodoPossuiBloqueio(Carbon $dataInicio, Carbon $dataTermino): bool
    {
        $bloqueioIntervalo = \App\LockDays::where('tipo', 1)
            ->whereDate('data_inicio', '<', $dataTermino->format('Y-m-d'))
            ->whereDate('data_fim', '>=', $dataInicio->format('Y-m-d'))
            ->exists();

        if ($bloqueioIntervalo) {
            return true;
        }

        return \App\LockDays::where('tipo', 2)
            ->whereDate('data_inicio', '>=', $dataInicio->format('Y-m-d'))
            ->whereDate('data_inicio', '<', $dataTermino->format('Y-m-d'))
            ->exists();
    }
}
