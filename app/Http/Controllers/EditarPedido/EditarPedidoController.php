<?php

namespace App\Http\Controllers\EditarPedido;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Mail\MailController;
use Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;



class EditarPedidoController extends Controller
{


public function index($id){

    
    /*
    |--------------------------------------------------------------------------
    | Descriptografa o ID
    |--------------------------------------------------------------------------
    */
    date_default_timezone_set('America/Sao_Paulo');
    try {
        $id = Crypt::decrypt($id);
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        abort(404);
    }

    $usuarioAutenticado = Auth::user();
    $today = Carbon::today();

    /*
    |--------------------------------------------------------------------------
    | Busca o pedido e verifica se pertence ao usuário
    |--------------------------------------------------------------------------
    */

    $hospedagem = \App\Hospede::where('id', $id)
        ->where('user_id', $usuarioAutenticado->id)
        ->first();

    if (!$hospedagem) {
        abort(
            403,
            'Pedido não encontrado ou você não possui permissão para editá-lo.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica se o pedido ainda pode ser editado
    |--------------------------------------------------------------------------
    |
    | Caso exista uma coluna de status, você pode ativar esta validação.
    | Ajuste os valores conforme os status existentes no seu sistema.
    |
    */

    
    $statusPermitidos = [0];

    if (!in_array($hospedagem->status, $statusPermitidos, true)) {
        \Session::flash('message', [
            'msg' => 'Este pedido não pode mais ser editado.',
            'class' => 'danger',
        ]);

        return redirect()->route('hospede.meuspedidos');
    }
    

    /*
    |--------------------------------------------------------------------------
    | Validade do documento
    |--------------------------------------------------------------------------
    */

    if (
        (int) $usuarioAutenticado->indeterminado !== 1 &&
        !empty($usuarioAutenticado->validade)
    ) {
        try {
            $validade = Carbon::parse(
                $usuarioAutenticado->validade
            )->startOfDay();
        } catch (\Throwable $e) {
            \Session::flash('message', [
                'msg' => 'A data de validade do documento está inválida.',
                'class' => 'danger',
            ]);

            return redirect()->route('home');
        }

        if ($validade->lt($today)) {
            \Session::flash('message', [
                'msg' =>
                    'Seu documento de identidade está com a data de '
                    . 'validade vencida! Favor atualizar o documento '
                    . 'para prosseguir.',
                'class' => 'danger',
            ]);

            return redirect()->route('home');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos de unidade
    |--------------------------------------------------------------------------
    */

    $tipos = \App\TipoUndHab::all();

    /*
    |--------------------------------------------------------------------------
    | Grupos tarifários permitidos para o posto/graduação
    |--------------------------------------------------------------------------
    */

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
            'msg' =>
                'Grupo de Tarifa não cadastrado! '
                . 'Contacte o administrador!',
            'class' => 'danger',
        ]);

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Busca as unidades vinculadas aos grupos
    |--------------------------------------------------------------------------
    */

    $gruposTarifa = \App\GrupoTarifa::with('tipoundhabitacao')
        ->whereIn('id', $grupoTarifaIds)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Coleção de unidades
    |--------------------------------------------------------------------------
    |
    | Mantida para compatibilidade caso a Blade ainda utilize $unidades.
    |
    */

    $unidades = $gruposTarifa
        ->pluck('tipoundhabitacao')
        ->filter()
        ->unique('id')
        ->sortBy(function ($unidade) {
            return mb_strtolower(
                (string) $unidade->descricao
            );
        })
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Array usado no select da view
    |--------------------------------------------------------------------------
    |
    | Formato:
    |
    | [
    |     [
    |         'id' => 1,
    |         'value' => 'Apartamento',
    |     ],
    | ]
    |
    */

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
            'msg' =>
                'Nenhuma unidade habitacional está vinculada '
                . 'ao grupo de tarifa do usuário.',
            'class' => 'danger',
        ]);

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Configuração do dia de corte
    |--------------------------------------------------------------------------
    */

    $diaBloqueado = \App\BloqueioDia::find(1);

    if (!$diaBloqueado) {
        \Session::flash('message', [
            'msg' =>
                'Configuração do período de inscrições não encontrada.',
            'class' => 'danger',
        ]);

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Temporada da data atual
    |--------------------------------------------------------------------------
    */

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
            'msg' =>
                'Não existe temporada cadastrada para a data atual.',
            'class' => 'danger',
        ]);

        return redirect()->back();
    }

    if (
        !in_array(
            (int) $temporadaAtual->tipo_temporada_id,
            [1, 2],
            true
        )
    ) {
        \Session::flash('message', [
            'msg' => 'Tipo de temporada inválido.',
            'class' => 'danger',
        ]);

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Calcula a janela permitida
    |--------------------------------------------------------------------------
    |
    | Utiliza o mesmo método privado usado no index, confirmação e store.
    |
    */

    try {
        $janela = $this->calcularJanelaInscricao(
            $today,
            $diaBloqueado,
            $temporadaAtual
        );
    } catch (\Throwable $e) {
        report($e);

        \Session::flash('message', [
            'msg' =>
                'Não foi possível calcular o período permitido '
                . 'para hospedagem.',
            'class' => 'danger',
        ]);

        return redirect()->back();
    }

    $minDate = $janela['minDate'];
    $maxDate = $janela['maxDate'];

    /*
    |--------------------------------------------------------------------------
    | Variáveis usadas nos bloqueios especiais
    |--------------------------------------------------------------------------
    */

    $diaAtual = (int) $today->day;
    $diaCorte = (int) $diaBloqueado->dia;
    $dataBase = $today->copy()->startOfMonth();

    /*
    |--------------------------------------------------------------------------
    | Bloqueios administrativos
    |--------------------------------------------------------------------------
    */

    $a = [];

    /*
     * Tipo 1: bloqueio por intervalo.
     */

    $bloquearDias = \App\LockDays::where('tipo', 1)->get();

    foreach ($bloquearDias as $bloqueio) {
        if (
            !empty($bloqueio->data_inicio) &&
            !empty($bloqueio->data_fim)
        ) {
            $a[] = [
                $bloqueio->data_inicio,
                $bloqueio->data_fim,
            ];
        }
    }

    /*
     * Tipo 2: bloqueio de um dia individual.
     */

    $bloquearDiasIndividuais = \App\LockDays::where(
            'tipo',
            2
        )
        ->get();

    foreach ($bloquearDiasIndividuais as $bloqueio) {
        if (!empty($bloqueio->data_inicio)) {
            $a[] = $bloqueio->data_inicio;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Bloqueio especial de dezembro
    |--------------------------------------------------------------------------
    |
    | Depois do dia de corte de novembro, dezembro não pode ser solicitado.
    |
    */

    if (
        (int) $today->month === 11 &&
        $diaAtual > $diaCorte
    ) {
        $dezembro = $dataBase
            ->copy()
            ->addMonthNoOverflow()
            ->startOfMonth();

        $a[] = [
            $dezembro
                ->copy()
                ->startOfMonth()
                ->format('Y-m-d'),

            $dezembro
                ->copy()
                ->endOfMonth()
                ->format('Y-m-d'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Converte os bloqueios para JSON
    |--------------------------------------------------------------------------
    */

    $a = json_encode(
        $a,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    /*
    |--------------------------------------------------------------------------
    | Horários de entrada e saída
    |--------------------------------------------------------------------------
    */

    $horario = \App\Horario::first();

    if (!$horario) {
        \Session::flash('message', [
            'msg' =>
                'Horários de entrada e saída não cadastrados.',
            'class' => 'danger',
        ]);

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Período atual do pedido
    |--------------------------------------------------------------------------
    */

    try {
        $peridoinicial =
            Carbon::parse(
                $hospedagem->data_inicio
            )->format('d-m-Y')
            . ' - '
            . Carbon::parse(
                $hospedagem->data_termino
            )->format('d-m-Y');
    } catch (\Throwable $e) {
        \Session::flash('message', [
            'msg' =>
                'O pedido possui uma data de hospedagem inválida.',
            'class' => 'danger',
        ]);

        return redirect()
            ->route('hospede.meuspedidos');
    }

    /*
    |--------------------------------------------------------------------------
    | Anos exibidos no calendário
    |--------------------------------------------------------------------------
    */

    $minYear = (int) Carbon::parse(
        $minDate
    )->format('Y');

    $maxYear = (int) Carbon::parse(
        $maxDate
    )->format('Y');

    /*
    |--------------------------------------------------------------------------
    | Retorno
    |--------------------------------------------------------------------------
    */

    return view(
        'hospedagem.edit_inscricao',
        compact(
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
        )
    );

}




























public function confirmaEdicao(Request $request)
{
    date_default_timezone_set('America/Sao_Paulo');
    $usuario = Auth::user();
    $today = Carbon::today();

    /*
    |--------------------------------------------------------------------------
    | Validação dos dados recebidos
    |--------------------------------------------------------------------------
    */

    $validator = Validator::make(
        $request->all(),
        [
            'id' => [
                'required',
                'integer',
            ],

            'peridoinicial' => [
                'required',
                'string',
            ],

            'tipo' => [
                'required',
                'integer',
            ],

            'adultos' => [
                'required',
                'integer',
                'min:1',
            ],

            'criancas' => [
                'required',
                'integer',
                'min:0',
            ],

            'pne' => [
                'required',
                'in:0,1',
            ],

            'pet' => [
                'required',
                'in:0,1',
            ],

            'observacao' => [
                'nullable',
                'string',
                'max:250',
            ],
        ],
        [
            'id.required' =>
                'O pedido não foi informado.',

            'id.integer' =>
                'O pedido informado é inválido.',

            'peridoinicial.required' =>
                'Informe o período de hospedagem.',

            'tipo.required' =>
                'Selecione o tipo de unidade habitacional.',

            'tipo.integer' =>
                'O tipo de unidade habitacional é inválido.',

            'adultos.required' =>
                'Informe a quantidade de adultos.',

            'adultos.integer' =>
                'A quantidade de adultos deve ser um número inteiro.',

            'adultos.min' =>
                'Deve haver pelo menos um adulto.',

            'criancas.required' =>
                'Informe a quantidade de crianças.',

            'criancas.integer' =>
                'A quantidade de crianças deve ser um número inteiro.',

            'criancas.min' =>
                'A quantidade de crianças não pode ser negativa.',

            'pne.required' =>
                'Informe se haverá hóspede PNE.',

            'pet.required' =>
                'Informe se haverá PET.',

            'observacao.max' =>
                'A observação deve possuir no máximo 250 caracteres.',
        ]
    );

    if ($validator->fails()) {
        return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Pedido que está sendo editado
    |--------------------------------------------------------------------------
    */

    $pedidoAtual = \App\Hospede::where(
            'id',
            $request->id
        )
        ->where(
            'user_id',
            $usuario->id
        )
        ->first();

    if (!$pedidoAtual) {
        \Session::flash('message', [
            'msg' =>
                'Pedido não encontrado ou não pertence ao usuário.',
            'class' =>
                'danger',
        ]);

        return redirect()
            ->route('hospede.meuspedidos');
    }

    /*
    |--------------------------------------------------------------------------
    | Validade do documento
    |--------------------------------------------------------------------------
    */

    if (
        (int) $usuario->indeterminado !== 1 &&
        !empty($usuario->validade)
    ) {
        try {
            $validade = Carbon::parse(
                $usuario->validade
            )->startOfDay();
        } catch (\Throwable $e) {
            \Session::flash('message', [
                'msg' =>
                    'A data de validade do documento está inválida.',
                'class' =>
                    'danger',
            ]);

            return redirect()->route('home');
        }

        if ($validade->lt($today)) {
            \Session::flash('message', [
                'msg' =>
                    'Seu documento de identidade está vencido. '
                    . 'Atualize o documento para prosseguir.',
                'class' =>
                    'danger',
            ]);

            return redirect()->route('home');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Converte o período
    |--------------------------------------------------------------------------
    |
    | Formato esperado:
    |
    | 08-11-2026 - 12-11-2026
    |
    */

    try {
        $partesPeriodo = array_map(
            'trim',
            explode(
                ' - ',
                trim($request->peridoinicial)
            )
        );

        if (count($partesPeriodo) !== 2) {
            throw new \InvalidArgumentException(
                'Período inválido.'
            );
        }

        $entradaTexto = $partesPeriodo[0];
        $saidaTexto = $partesPeriodo[1];

        $dataInicio = Carbon::createFromFormat(
            'd-m-Y',
            $entradaTexto
        )->startOfDay();

        $dataTermino = Carbon::createFromFormat(
            'd-m-Y',
            $saidaTexto
        )->startOfDay();

        /*
         * Evita aceitar datas como 31-02.
         */

        if (
            $dataInicio->format('d-m-Y') !== $entradaTexto ||
            $dataTermino->format('d-m-Y') !== $saidaTexto
        ) {
            throw new \InvalidArgumentException(
                'Data inválida.'
            );
        }
    } catch (\Throwable $e) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'O período informado possui um formato inválido.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Valida entrada e saída
    |--------------------------------------------------------------------------
    */

    if ($dataInicio->gte($dataTermino)) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'A data de saída deve ser posterior à data de entrada.',
            ]);
    }

    $diasHospedagem = $dataInicio->diffInDays(
        $dataTermino
    );

    /*
    |--------------------------------------------------------------------------
    | Configuração do período de inscrições
    |--------------------------------------------------------------------------
    */

    $diaBloqueado = \App\BloqueioDia::find(1);

    if (!$diaBloqueado) {
        \Session::flash('message', [
            'msg' =>
                'Configuração do período de inscrições não encontrada.',
            'class' =>
                'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Temporada da data atual
    |--------------------------------------------------------------------------
    */

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
            'msg' =>
                'Não existe temporada cadastrada para a data atual.',
            'class' =>
                'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    if (
        !in_array(
            (int) $temporadaAtual->tipo_temporada_id,
            [1, 2],
            true
        )
    ) {
        \Session::flash('message', [
            'msg' =>
                'Tipo de temporada inválido.',
            'class' =>
                'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Calcula a janela permitida
    |--------------------------------------------------------------------------
    */

    try {
        $janela = $this->calcularJanelaInscricao(
            $today,
            $diaBloqueado,
            $temporadaAtual
        );
    } catch (\Throwable $e) {
        report($e);

        \Session::flash('message', [
            'msg' =>
                'Não foi possível calcular o período permitido.',
            'class' =>
                'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    $minDate = Carbon::parse(
        $janela['minDate']
    )->startOfDay();

    $maxDate = Carbon::parse(
        $janela['maxDate']
    )->startOfDay();

    /*
    |--------------------------------------------------------------------------
    | Valida a janela de inscrição
    |--------------------------------------------------------------------------
    */

    if (
        $dataInicio->lt($minDate) ||
        $dataTermino->gt($maxDate)
    ) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'O período deve estar entre '
                    . $minDate->format('d/m/Y')
                    . ' e '
                    . $maxDate->format('d/m/Y')
                    . '.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Bloqueio especial de dezembro
    |--------------------------------------------------------------------------
    |
    | Depois do dia de corte de novembro, dezembro não pode ser solicitado.
    |
    */

    $diaCorte = (int) $diaBloqueado->dia;

    if (
        (int) $today->month === 11 &&
        (int) $today->day > $diaCorte
    ) {
        $inicioDezembro = $today
            ->copy()
            ->startOfMonth()
            ->addMonthNoOverflow()
            ->startOfMonth();

        $fimDezembro = $inicioDezembro
            ->copy()
            ->endOfMonth();

        /*
         * A data de saída não é considerada uma diária.
         */

        $periodoPassaPorDezembro =
            $dataInicio->lte($fimDezembro) &&
            $dataTermino->gt($inicioDezembro);

        if ($periodoPassaPorDezembro) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'peridoinicial' =>
                        'As inscrições para dezembro encerraram no dia '
                        . $diaCorte
                        . ' de novembro.',
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Bloqueios administrativos
    |--------------------------------------------------------------------------
    */

    if (
        $this->periodoPossuiBloqueio(
            $dataInicio,
            $dataTermino
        )
    ) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'O período selecionado contém uma data '
                    . 'bloqueada pela administração.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Grupos tarifários permitidos
    |--------------------------------------------------------------------------
    */

    $grupoTarifaIds =
        \App\GrupoTarifaPostoGraduacao::where(
            'posto_id',
            $usuario->postograd_id
        )
        ->pluck('grupotarifa_id')
        ->unique()
        ->values();

    if ($grupoTarifaIds->isEmpty()) {
        \Session::flash('message', [
            'msg' =>
                'Grupo de tarifa não cadastrado para seu '
                . 'posto/graduação.',
            'class' =>
                'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica se a unidade é permitida
    |--------------------------------------------------------------------------
    */

    $grupoUnidade = \App\GrupoTarifa::whereIn(
            'id',
            $grupoTarifaIds
        )
        ->where(
            'unidade_habitacional_id',
            $request->tipo
        )
        ->first();

    if (!$grupoUnidade) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'tipo' =>
                    'A unidade selecionada não está disponível '
                    . 'para seu posto/graduação.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Tarifa correspondente
    |--------------------------------------------------------------------------
    */

    $tarifa = \App\Tarifas::where(
            'tipoundhab_id',
            $request->tipo
        )
        ->whereIn(
            'grupo_destinacao_id',
            $grupoTarifaIds
        )
        ->first();

    if (!$tarifa) {
        \Session::flash('message', [
            'msg' =>
                'Tarifa não cadastrada para a unidade e '
                . 'grupo do usuário.',
            'class' =>
                'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Busca as temporadas que cruzam o período
    |--------------------------------------------------------------------------
    */

    $temporadasDoPeriodo = \App\Temporada::whereDate(
            'data_inicio',
            '<',
            $dataTermino->format('Y-m-d')
        )
        ->whereDate(
            'data_termino',
            '>=',
            $dataInicio->format('Y-m-d')
        )
        ->orderBy('data_inicio')
        ->get();

    if ($temporadasDoPeriodo->isEmpty()) {
        \Session::flash('message', [
            'msg' =>
                'Não existe temporada cadastrada para o período '
                . 'selecionado.',
            'class' =>
                'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Conta as diárias por temporada
    |--------------------------------------------------------------------------
    |
    | A data de saída não conta como uma nova diária.
    |
    */

    $diasAltaTemporada = 0;
    $diasBaixaTemporada = 0;

    for (
        $data = $dataInicio->copy();
        $data->lt($dataTermino);
        $data->addDay()
    ) {
        $temporadaDaDiaria = $temporadasDoPeriodo->first(
            function ($temporada) use ($data) {
                $inicioTemporada = Carbon::parse(
                    $temporada->data_inicio
                )->startOfDay();

                $terminoTemporada = Carbon::parse(
                    $temporada->data_termino
                )->endOfDay();

                return
                    $data->gte($inicioTemporada) &&
                    $data->lte($terminoTemporada);
            }
        );

        if (!$temporadaDaDiaria) {
            \Session::flash('message', [
                'msg' =>
                    'A data '
                    . $data->format('d/m/Y')
                    . ' não pertence a nenhuma temporada cadastrada.',
                'class' =>
                    'danger',
            ]);

            return redirect()
                ->back()
                ->withInput();
        }

        if (
            (int) $temporadaDaDiaria->tipo_temporada_id === 1
        ) {
            $diasAltaTemporada++;
        } elseif (
            (int) $temporadaDaDiaria->tipo_temporada_id === 2
        ) {
            $diasBaixaTemporada++;
        } else {
            \Session::flash('message', [
                'msg' =>
                    'Existe uma temporada com tipo inválido.',
                'class' =>
                    'danger',
            ]);

            return redirect()
                ->back()
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Confere a quantidade de diárias
    |--------------------------------------------------------------------------
    */

    if (
        ($diasAltaTemporada + $diasBaixaTemporada)
        !==
        $diasHospedagem
    ) {
        \Session::flash('message', [
            'msg' =>
                'Não foi possível classificar todas as diárias '
                . 'do período.',
            'class' =>
                'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    $possuiAltaTemporada =
        $diasAltaTemporada > 0;

    /*
    |--------------------------------------------------------------------------
    | Máximo de sete diárias na alta temporada
    |--------------------------------------------------------------------------
    */

    if (
        $possuiAltaTemporada &&
        $diasHospedagem > 7
    ) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'Na alta temporada, o período máximo '
                    . 'é de 7 diárias.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Configuração do limite mensal
    |--------------------------------------------------------------------------
    */

    $quantidadeReservas =
        \App\QuantidadeReserva::first();

    if (!$quantidadeReservas) {
        \Session::flash('message', [
            'msg' =>
                'Limite máximo de reservas não configurado.',
            'class' =>
                'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Pedidos do usuário no mês
    |--------------------------------------------------------------------------
    |
    | O próprio pedido não entra na contagem.
    |
    */

    $consultaPedidosDoMes =
        \App\Hospede::where(
            'user_id',
            $usuario->id
        )
        ->where(
            'id',
            '<>',
            $pedidoAtual->id
        )
        ->whereYear(
            'data_inicio',
            $dataInicio->year
        )
        ->whereMonth(
            'data_inicio',
            $dataInicio->month
        );

    $pedidosDoMes =
        (clone $consultaPedidosDoMes)->count();

    $pedidosCampingMotorhome =
        (clone $consultaPedidosDoMes)
            ->whereIn(
                'tipo_und_id',
                [11, 12]
            )
            ->count();

    $tipoCampingMotorhome = in_array(
        (int) $request->tipo,
        [11, 12],
        true
    );

    /*
    |--------------------------------------------------------------------------
    | Limite de Camping/Motor-Home
    |--------------------------------------------------------------------------
    */

    if ($tipoCampingMotorhome) {
        if ($pedidosCampingMotorhome >= 2) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'tipo' =>
                        'Você já alcançou o limite de 2 pedidos '
                        . 'para Camping e/ou Motor-Home neste mês.',
                ]);
        }
    } else {
        /*
        |--------------------------------------------------------------------------
        | Limite conforme a temporada
        |--------------------------------------------------------------------------
        */

        $limiteMensal = $possuiAltaTemporada
            ? (int) $quantidadeReservas->reservas
            : (int) $quantidadeReservas
                ->qnt_reservas_baixa_temporada;

        if ($pedidosDoMes >= $limiteMensal) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'peridoinicial' =>
                        'Você já alcançou o limite de '
                        . $limiteMensal
                        . ' pedido(s) para esse mês.',
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Valores unitários
    |--------------------------------------------------------------------------
    */

    $valorDiariaAlta = round(
        (float) $tarifa->valor,
        2
    );

    $valorDiariaBaixa = round(
        (float) $tarifa->valor_baixa,
        2
    );

    /*
    |--------------------------------------------------------------------------
    | Subtotais sem desconto
    |--------------------------------------------------------------------------
    */

    $calculaDiariaAlta = round(
        $diasAltaTemporada * $valorDiariaAlta,
        2
    );

    $calculaDiariaBaixa = round(
        $diasBaixaTemporada * $valorDiariaBaixa,
        2
    );

    $totalValorBruto = round(
        $calculaDiariaAlta +
        $calculaDiariaBaixa,
        2
    );

    /*
    |--------------------------------------------------------------------------
    | Desconto Mecenas
    |--------------------------------------------------------------------------
    |
    | O model User já contém:
    |
    | getPercentualDescontoAttribute()
    | aplicarDesconto($valor)
    |
    */

    $isMecenas =
        (bool) $usuario->mecenas;

    $percentualDesconto =
        (float) $usuario->percentual_desconto;

    $totalValorLiquido = round(
        (float) $usuario->aplicarDesconto(
            $totalValorBruto
        ),
        2
    );

    $valorDescontoMecenas = round(
        $totalValorBruto -
        $totalValorLiquido,
        2
    );

    /*
    |--------------------------------------------------------------------------
    | Valores unitários com desconto
    |--------------------------------------------------------------------------
    */

    $valorDiariaAltaComDesconto = round(
        (float) $usuario->aplicarDesconto(
            $valorDiariaAlta
        ),
        2
    );

    $valorDiariaBaixaComDesconto = round(
        (float) $usuario->aplicarDesconto(
            $valorDiariaBaixa
        ),
        2
    );

    /*
    |--------------------------------------------------------------------------
    | Subtotais com desconto
    |--------------------------------------------------------------------------
    */

    $calculaDiariaAltaComDesconto = round(
        (float) $usuario->aplicarDesconto(
            $calculaDiariaAlta
        ),
        2
    );

    $calculaDiariaBaixaComDesconto = round(
        (float) $usuario->aplicarDesconto(
            $calculaDiariaBaixa
        ),
        2
    );

    /*
    |--------------------------------------------------------------------------
    | Tarifa de referência
    |--------------------------------------------------------------------------
    */

    if (
        $diasAltaTemporada > 0 &&
        $diasBaixaTemporada === 0
    ) {
        $valorTarifa =
            $valorDiariaAlta;

        $valorTarifaComDesconto =
            $valorDiariaAltaComDesconto;
    } elseif (
        $diasBaixaTemporada > 0 &&
        $diasAltaTemporada === 0
    ) {
        $valorTarifa =
            $valorDiariaBaixa;

        $valorTarifaComDesconto =
            $valorDiariaBaixaComDesconto;
    } else {
        /*
         * Em período misto, mantém a tarifa alta como referência.
         */

        $valorTarifa =
            $valorDiariaAlta;

        $valorTarifaComDesconto =
            $valorDiariaAltaComDesconto;
    }

    /*
    |--------------------------------------------------------------------------
    | Acrescenta dados calculados ao Request
    |--------------------------------------------------------------------------
    */

    $request->merge([
        'data_inicio' =>
            $dataInicio->format('Y-m-d'),

        'data_termino' =>
            $dataTermino->format('Y-m-d'),

        'dias_hospedagem' =>
            $diasHospedagem,

        'dias_alta_temporada' =>
            $diasAltaTemporada,

        'dias_baixa_temporada' =>
            $diasBaixaTemporada,

        'mecenas' =>
            $isMecenas ? 1 : 0,

        'percentual_desconto' =>
            $percentualDesconto,
    ]);

    $consulta = $request;

    /*
    |--------------------------------------------------------------------------
    | Tipos de unidade
    |--------------------------------------------------------------------------
    */

    $tipos = \App\TipoUndHab::all();

    /*
    |--------------------------------------------------------------------------
    | Criptografa o valor líquido
    |--------------------------------------------------------------------------
    |
    | O store deve recalcular esse valor novamente.
    |
    */

    $totalValorCriptografado =
        Crypt::encrypt($totalValorLiquido);

    /*
    |--------------------------------------------------------------------------
    | Retorno para a tela de confirmação
    |--------------------------------------------------------------------------
    */

    return view(
        'hospedagem.confirmarantigoEdit',
        [
            'consulta' =>
                $consulta,

            'tipos' =>
                $tipos,

            'diasHospedagem' =>
                $diasHospedagem,

            'diasAltaTemporada' =>
                $diasAltaTemporada,

            'diasBaixaTemporada' =>
                $diasBaixaTemporada,

            /*
             * Valores unitários sem desconto.
             */

            'valorDiariaAlta' =>
                $valorDiariaAlta,

            'valorDiariaBaixa' =>
                $valorDiariaBaixa,

            /*
             * Valores unitários com desconto.
             */

            'valorDiariaAltaComDesconto' =>
                $valorDiariaAltaComDesconto,

            'valorDiariaBaixaComDesconto' =>
                $valorDiariaBaixaComDesconto,

            /*
             * Subtotais sem desconto.
             */

            'calculaDiariaAlta' =>
                $calculaDiariaAlta,

            'calculaDiariaBaixa' =>
                $calculaDiariaBaixa,

            /*
             * Subtotais com desconto.
             */

            'calculaDiariaAltaComDesconto' =>
                $calculaDiariaAltaComDesconto,

            'calculaDiariaBaixaComDesconto' =>
                $calculaDiariaBaixaComDesconto,

            /*
             * Desconto Mecenas.
             */

            'isMecenas' =>
                $isMecenas,

            'percentualDesconto' =>
                $percentualDesconto,

            'valorDescontoMecenas' =>
                $valorDescontoMecenas,

            /*
             * Totais.
             */

            'totalValorBruto' =>
                $totalValorBruto,

            'totalValorLiquido' =>
                $totalValorLiquido,

            /*
             * Total líquido criptografado.
             */

            'totalValor' =>
                $totalValorCriptografado,

            /*
             * Tarifa de referência.
             */

            'valorTarifa' =>
                $valorTarifa,

            'valorTarifaComDesconto' =>
                $valorTarifaComDesconto,
        ]
    );

}





























/*

    Grava a edição do pedido após a confirmação do usuário.

*/
public function gravaEdicao(Request $request)
{


    date_default_timezone_set('America/Sao_Paulo');
    $usuario = Auth::user();
    $today = Carbon::today();

    /*
    |--------------------------------------------------------------------------
    | Validação dos campos
    |--------------------------------------------------------------------------
    */

    $validator = Validator::make(
        $request->all(),
        [
            'id' => [
                'required',
                'integer',
            ],

            'peridoinicial' => [
                'required',
                'string',
            ],

            'tipo' => [
                'required',
                'integer',
            ],

            'adultos' => [
                'required',
                'integer',
                'min:1',
            ],

            'criancas' => [
                'required',
                'integer',
                'min:0',
            ],

            'pne' => [
                'required',
                'in:0,1',
            ],

            'pet' => [
                'required',
                'in:0,1',
            ],

            'observacao' => [
                'nullable',
                'string',
                'max:250',
            ],
        ],
        [
            'id.required' =>
                'O pedido não foi informado.',

            'id.integer' =>
                'O pedido informado é inválido.',

            'peridoinicial.required' =>
                'Informe o período de hospedagem.',

            'tipo.required' =>
                'Selecione o tipo de unidade habitacional.',

            'tipo.integer' =>
                'O tipo de unidade habitacional é inválido.',

            'adultos.required' =>
                'Informe a quantidade de adultos.',

            'adultos.integer' =>
                'A quantidade de adultos deve ser um número inteiro.',

            'adultos.min' =>
                'Deve haver pelo menos um adulto.',

            'criancas.required' =>
                'Informe a quantidade de crianças.',

            'criancas.integer' =>
                'A quantidade de crianças deve ser um número inteiro.',

            'criancas.min' =>
                'A quantidade de crianças não pode ser negativa.',

            'pne.required' =>
                'Informe se haverá hóspede PNE.',

            'pet.required' =>
                'Informe se haverá PET.',

            'observacao.max' =>
                'A observação deve possuir no máximo 250 caracteres.',
        ]
    );

    if ($validator->fails()) {
        return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Localiza o pedido
    |--------------------------------------------------------------------------
    |
    | O pedido deve pertencer ao usuário autenticado.
    |
    */

    $hospedagem = \App\Hospede::where(
            'id',
            $request->id
        )
        ->where(
            'user_id',
            $usuario->id
        )
        ->first();

    if (!$hospedagem) {
        \Session::flash('message', [
            'msg' =>
                'Pedido não encontrado ou não pertence ao usuário.',
            'class' => 'danger',
        ]);

        return redirect()
            ->route('hospede.meuspedidos');
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica o status
    |--------------------------------------------------------------------------
    |
    | Somente pedidos com status 0 podem ser editados.
    |
    */

    if ((int) $hospedagem->status !== 0) {
        \Session::flash('message', [
            'msg' =>
                'Este pedido não pode mais ser editado.',
            'class' => 'danger',
        ]);

        return redirect()
            ->route('hospede.meuspedidos');
    }

    /*
    |--------------------------------------------------------------------------
    | Validade do documento
    |--------------------------------------------------------------------------
    */

    if (
        (int) $usuario->indeterminado !== 1 &&
        !empty($usuario->validade)
    ) {
        try {
            $validade = Carbon::parse(
                $usuario->validade
            )->startOfDay();
        } catch (\Throwable $e) {
            \Session::flash('message', [
                'msg' =>
                    'A data de validade do documento está inválida.',
                'class' => 'danger',
            ]);

            return redirect()->route('home');
        }

        if ($validade->lt($today)) {
            \Session::flash('message', [
                'msg' =>
                    'Seu documento de identidade está vencido. '
                    . 'Atualize o documento para prosseguir.',
                'class' => 'danger',
            ]);

            return redirect()->route('home');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Converte o período
    |--------------------------------------------------------------------------
    |
    | Formato esperado:
    | 08-11-2026 - 12-11-2026
    |
    */

    try {
        $partesPeriodo = array_map(
            'trim',
            explode(
                ' - ',
                trim($request->peridoinicial)
            )
        );

        if (count($partesPeriodo) !== 2) {
            throw new \InvalidArgumentException(
                'Período inválido.'
            );
        }

        $entradaTexto = $partesPeriodo[0];
        $saidaTexto = $partesPeriodo[1];

        $dataInicio = Carbon::createFromFormat(
            'd-m-Y',
            $entradaTexto
        )->startOfDay();

        $dataTermino = Carbon::createFromFormat(
            'd-m-Y',
            $saidaTexto
        )->startOfDay();

        /*
         * Impede o Carbon de aceitar datas inválidas
         * realizando correções automáticas.
         */

        if (
            $dataInicio->format('d-m-Y') !== $entradaTexto ||
            $dataTermino->format('d-m-Y') !== $saidaTexto
        ) {
            throw new \InvalidArgumentException(
                'Uma das datas é inválida.'
            );
        }
    } catch (\Throwable $e) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'O período informado possui um formato inválido.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Valida entrada e saída
    |--------------------------------------------------------------------------
    */

    if ($dataInicio->gte($dataTermino)) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'A data de saída deve ser posterior à data de entrada.',
            ]);
    }

    $diasHospedagem = $dataInicio->diffInDays(
        $dataTermino
    );

    /*
    |--------------------------------------------------------------------------
    | Configuração do período de inscrição
    |--------------------------------------------------------------------------
    */

    $diaBloqueado = \App\BloqueioDia::find(1);

    if (!$diaBloqueado) {
        \Session::flash('message', [
            'msg' =>
                'Configuração do período de inscrições não encontrada.',
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Temporada atual
    |--------------------------------------------------------------------------
    */

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
            'msg' =>
                'Não existe temporada cadastrada para a data atual.',
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    if (
        !in_array(
            (int) $temporadaAtual->tipo_temporada_id,
            [1, 2],
            true
        )
    ) {
        \Session::flash('message', [
            'msg' =>
                'O tipo da temporada atual é inválido.',
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Calcula a janela permitida
    |--------------------------------------------------------------------------
    */

    try {
        $janela = $this->calcularJanelaInscricao(
            $today,
            $diaBloqueado,
            $temporadaAtual
        );
    } catch (\Throwable $e) {
        report($e);

        \Session::flash('message', [
            'msg' =>
                'Não foi possível calcular o período permitido.',
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    $minDate = Carbon::parse(
        $janela['minDate']
    )->startOfDay();

    $maxDate = Carbon::parse(
        $janela['maxDate']
    )->startOfDay();

    if (
        $dataInicio->lt($minDate) ||
        $dataTermino->gt($maxDate)
    ) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'O período deve estar entre '
                    . $minDate->format('d/m/Y')
                    . ' e '
                    . $maxDate->format('d/m/Y')
                    . '.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Bloqueio especial de dezembro
    |--------------------------------------------------------------------------
    |
    | Depois do dia de corte de novembro, dezembro fica fechado.
    |
    */

    $diaCorte = (int) $diaBloqueado->dia;

    if (
        (int) $today->month === 11 &&
        (int) $today->day > $diaCorte
    ) {
        $inicioDezembro = $today
            ->copy()
            ->startOfMonth()
            ->addMonthNoOverflow()
            ->startOfMonth();

        $fimDezembro = $inicioDezembro
            ->copy()
            ->endOfMonth();

        $periodoPassaPorDezembro =
            $dataInicio->lte($fimDezembro) &&
            $dataTermino->gt($inicioDezembro);

        if ($periodoPassaPorDezembro) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'peridoinicial' =>
                        'As inscrições para dezembro encerraram no dia '
                        . $diaCorte
                        . ' de novembro.',
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Bloqueios administrativos
    |--------------------------------------------------------------------------
    */

    if (
        $this->periodoPossuiBloqueio(
            $dataInicio,
            $dataTermino
        )
    ) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'O período selecionado contém uma data '
                    . 'bloqueada pela administração.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica duplicidade
    |--------------------------------------------------------------------------
    |
    | O próprio pedido é excluído da consulta.
    |
    */

    $pedidoDuplicado = \App\Hospede::where(
            'user_id',
            $usuario->id
        )
        ->where(
            'id',
            '<>',
            $hospedagem->id
        )
        ->whereDate(
            'data_inicio',
            $dataInicio->format('Y-m-d')
        )
        ->whereDate(
            'data_termino',
            $dataTermino->format('Y-m-d')
        )
        ->exists();

    if ($pedidoDuplicado) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'Você já possui outro pedido para esse mesmo período.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Grupos tarifários do usuário
    |--------------------------------------------------------------------------
    */

    $grupoTarifaIds =
        \App\GrupoTarifaPostoGraduacao::where(
            'posto_id',
            $usuario->postograd_id
        )
        ->pluck('grupotarifa_id')
        ->unique()
        ->values();

    if ($grupoTarifaIds->isEmpty()) {
        \Session::flash('message', [
            'msg' =>
                'Grupo de tarifa não cadastrado para seu '
                . 'posto/graduação.',
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica se a unidade é permitida
    |--------------------------------------------------------------------------
    */

    $grupoUnidade = \App\GrupoTarifa::whereIn(
            'id',
            $grupoTarifaIds
        )
        ->where(
            'unidade_habitacional_id',
            $request->tipo
        )
        ->first();

    if (!$grupoUnidade) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'tipo' =>
                    'A unidade selecionada não está disponível '
                    . 'para seu posto/graduação.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Busca a tarifa
    |--------------------------------------------------------------------------
    */

    $tarifa = \App\Tarifas::where(
            'tipoundhab_id',
            $request->tipo
        )
        ->whereIn(
            'grupo_destinacao_id',
            $grupoTarifaIds
        )
        ->first();

    if (!$tarifa) {
        \Session::flash('message', [
            'msg' =>
                'Tarifa não cadastrada para a unidade e '
                . 'grupo do usuário.',
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Busca as temporadas que cruzam o período
    |--------------------------------------------------------------------------
    */

    $temporadasDoPeriodo = \App\Temporada::whereDate(
            'data_inicio',
            '<',
            $dataTermino->format('Y-m-d')
        )
        ->whereDate(
            'data_termino',
            '>=',
            $dataInicio->format('Y-m-d')
        )
        ->orderBy('data_inicio')
        ->get();

    if ($temporadasDoPeriodo->isEmpty()) {
        \Session::flash('message', [
            'msg' =>
                'Não existe temporada cadastrada para o período '
                . 'selecionado.',
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Conta as diárias por temporada
    |--------------------------------------------------------------------------
    |
    | A data da saída não conta como uma nova diária.
    |
    */

    $diasAltaTemporada = 0;
    $diasBaixaTemporada = 0;

    for (
        $data = $dataInicio->copy();
        $data->lt($dataTermino);
        $data->addDay()
    ) {
        $temporadaDaDiaria = $temporadasDoPeriodo->first(
            function ($temporada) use ($data) {
                $inicioTemporada = Carbon::parse(
                    $temporada->data_inicio
                )->startOfDay();

                $fimTemporada = Carbon::parse(
                    $temporada->data_termino
                )->endOfDay();

                return
                    $data->gte($inicioTemporada) &&
                    $data->lte($fimTemporada);
            }
        );

        if (!$temporadaDaDiaria) {
            \Session::flash('message', [
                'msg' =>
                    'A data '
                    . $data->format('d/m/Y')
                    . ' não pertence a nenhuma temporada cadastrada.',
                'class' => 'danger',
            ]);

            return redirect()
                ->back()
                ->withInput();
        }

        if (
            (int) $temporadaDaDiaria->tipo_temporada_id === 1
        ) {
            $diasAltaTemporada++;
        } elseif (
            (int) $temporadaDaDiaria->tipo_temporada_id === 2
        ) {
            $diasBaixaTemporada++;
        } else {
            \Session::flash('message', [
                'msg' =>
                    'Existe uma temporada com tipo inválido.',
                'class' => 'danger',
            ]);

            return redirect()
                ->back()
                ->withInput();
        }
    }

    if (
        ($diasAltaTemporada + $diasBaixaTemporada)
        !==
        $diasHospedagem
    ) {
        \Session::flash('message', [
            'msg' =>
                'Não foi possível classificar todas as diárias.',
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    $possuiAltaTemporada =
        $diasAltaTemporada > 0;

    /*
    |--------------------------------------------------------------------------
    | Máximo de sete diárias na alta temporada
    |--------------------------------------------------------------------------
    */

    if (
        $possuiAltaTemporada &&
        $diasHospedagem > 7
    ) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'Na alta temporada, o período máximo '
                    . 'é de 7 diárias.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Limite mensal de pedidos
    |--------------------------------------------------------------------------
    */

    $quantidadeReservas =
        \App\QuantidadeReserva::first();

    if (!$quantidadeReservas) {
        \Session::flash('message', [
            'msg' =>
                'Limite máximo de reservas não configurado.',
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    $consultaPedidosDoMes = \App\Hospede::where(
            'user_id',
            $usuario->id
        )
        ->where(
            'id',
            '<>',
            $hospedagem->id
        )
        ->whereYear(
            'data_inicio',
            $dataInicio->year
        )
        ->whereMonth(
            'data_inicio',
            $dataInicio->month
        );

    $pedidosDoMes =
        (clone $consultaPedidosDoMes)->count();

    $pedidosCampingMotorhome =
        (clone $consultaPedidosDoMes)
            ->whereIn(
                'tipo_und_id',
                [11, 12]
            )
            ->count();

    $tipoCampingMotorhome = in_array(
        (int) $request->tipo,
        [11, 12],
        true
    );

    if ($tipoCampingMotorhome) {
        if ($pedidosCampingMotorhome >= 2) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'tipo' =>
                        'Você já alcançou o limite de 2 pedidos '
                        . 'para Camping e/ou Motor-Home neste mês.',
                ]);
        }
    } else {
        $limiteMensal = $possuiAltaTemporada
            ? (int) $quantidadeReservas->reservas
            : (int) $quantidadeReservas
                ->qnt_reservas_baixa_temporada;

        if ($pedidosDoMes >= $limiteMensal) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'peridoinicial' =>
                        'Você já alcançou o limite de '
                        . $limiteMensal
                        . ' pedido(s) para esse mês.',
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Recalcula os valores
    |--------------------------------------------------------------------------
    */

    $valorDiariaAlta = round(
        (float) $tarifa->valor,
        2
    );

    $valorDiariaBaixa = round(
        (float) $tarifa->valor_baixa,
        2
    );

    $subtotalAlta = round(
        $diasAltaTemporada * $valorDiariaAlta,
        2
    );

    $subtotalBaixa = round(
        $diasBaixaTemporada * $valorDiariaBaixa,
        2
    );

    $totalValorBruto = round(
        $subtotalAlta + $subtotalBaixa,
        2
    );

    /*
    |--------------------------------------------------------------------------
    | Aplica o desconto Mecenas
    |--------------------------------------------------------------------------
    */

    $totalValorLiquido = round(
        (float) $usuario->aplicarDesconto(
            $totalValorBruto
        ),
        2
    );

    /*
    |--------------------------------------------------------------------------
    | Tarifa unitária de referência
    |--------------------------------------------------------------------------
    */

    if (
        $diasAltaTemporada > 0 &&
        $diasBaixaTemporada === 0
    ) {
        $valorTarifaReferencia =
            $valorDiariaAlta;
    } elseif (
        $diasBaixaTemporada > 0 &&
        $diasAltaTemporada === 0
    ) {
        $valorTarifaReferencia =
            $valorDiariaBaixa;
    } else {
        /*
         * No período misto, mantém a tarifa alta como referência.
         */

        $valorTarifaReferencia =
            $valorDiariaAlta;
    }

    $valorTarifaComDesconto = round(
        (float) $usuario->aplicarDesconto(
            $valorTarifaReferencia
        ),
        2
    );

    /*
    |--------------------------------------------------------------------------
    | Atualiza o banco
    |--------------------------------------------------------------------------
    */

    try {
        DB::transaction(
            function () use (
                $hospedagem,
                $usuario,
                $request,
                $dataInicio,
                $dataTermino,
                $diasHospedagem,
                $totalValorLiquido,
                $valorTarifaComDesconto
            ) {
                /*
                 * Bloqueia o registro durante a atualização.
                 * Também confirma novamente o usuário e o status.
                 */

                $registro = \App\Hospede::where(
                        'id',
                        $hospedagem->id
                    )
                    ->where(
                        'user_id',
                        $usuario->id
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$registro) {
                    throw new \RuntimeException(
                        'Pedido não encontrado durante a atualização.'
                    );
                }

                if ((int) $registro->status !== 0) {
                    throw new \RuntimeException(
                        'O pedido mudou de status e não pode mais ser editado.'
                    );
                }

                $registro->user_cpf =
                    $usuario->cpf;

                $registro->user_id =
                    $usuario->id;

                $registro->tipo_und_id =
                    (int) $request->tipo;

                $registro->data_inicio =
                    $dataInicio->format('Y-m-d');

                $registro->data_termino =
                    $dataTermino->format('Y-m-d');

                $registro->adulto =
                    (int) $request->adultos;

                $registro->crianca =
                    (int) $request->criancas;

                $registro->pne =
                    (int) $request->pne;

                $registro->pet =
                    (int) $request->pet;

                $registro->observacao =
                    $request->filled('observacao')
                        ? trim($request->observacao)
                        : null;

                /*
                 * Valor total com desconto Mecenas.
                 */

                $registro->valor =
                    $totalValorLiquido;

                /*
                 * Tarifa unitária de referência com desconto.
                 */

                $registro->valortarifa =
                    $valorTarifaComDesconto;

                $registro->qntdiarias =
                    $diasHospedagem;

                /*
                 * O status permanece 0.
                 * Não é necessário redefini-lo.
                 */

                $registro->save();
            }
        );
    } catch (\Throwable $e) {
        report($e);

        \Session::flash('message', [
            'msg' =>
                $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'Não foi possível atualizar a inscrição. Tente novamente.',
            'class' => 'danger',
        ]);

        return redirect()
            ->route('hospede.meuspedidos');
    }

    \Session::flash('message', [
        'msg' =>
            'Inscrição atualizada com sucesso!',
        'class' => 'success',
    ]);

    return redirect()
        ->route('hospede.meuspedidos');
}




































///////////////////////////////////////
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
        $mesAberto = $dataBase
            ->copy()
            ->addMonthNoOverflow();
    } else {
        $mesAberto = $dataBase
            ->copy()
            ->addMonthsNoOverflow(2);
    }

    $mesDoLimite = $mesAberto
        ->copy()
        ->addMonthNoOverflow()
        ->startOfMonth();

    $diaLimiteValido = min(
        $diaLimite,
        $mesDoLimite->copy()->endOfMonth()->day
    );

    $maxDate = $mesDoLimite
        ->copy()
        ->day($diaLimiteValido);

    $isAltaTemporada =
        (int) $temporadaAtual->tipo_temporada_id === 1;

    $isBaixaTemporada =
        (int) $temporadaAtual->tipo_temporada_id === 2;

    if ($isBaixaTemporada) {
        $minDate = $today->copy();
    } elseif ($isAltaTemporada) {
        $minDate = $mesAberto
            ->copy()
            ->startOfMonth();
    } else {
        throw new \RuntimeException(
            'Tipo de temporada inválido.'
        );
    }

    /*
     * Depois do dia 10 de fevereiro, março continua disponível,
     * pois março já pertence à baixa temporada.
     */

    if (
        (int) $today->month === 2 &&
        $diaAtual > $diaCorte
    ) {
        $minDate = $dataBase
            ->copy()
            ->addMonthNoOverflow()
            ->startOfMonth();
    }

    return [
        'minDate' => $minDate->format('Y-m-d'),
        'maxDate' => $maxDate->format('Y-m-d'),
    ];
}

private function periodoPossuiBloqueio(
    Carbon $dataInicio,
    Carbon $dataTermino
): bool {
    /*
     * Tipo 1: intervalo.
     *
     * Verifica se existe interseção entre o período solicitado
     * e algum intervalo bloqueado.
     */

    $bloqueioIntervalo = \App\LockDays::where('tipo', 1)
        ->whereDate('data_inicio', '<', $dataTermino->format('Y-m-d'))
        ->whereDate('data_fim', '>=', $dataInicio->format('Y-m-d'))
        ->exists();

    if ($bloqueioIntervalo) {
        return true;
    }

    /*
     * Tipo 2: dia individual.
     *
     * A saída não é considerada uma diária. Por isso usamos "< dataTermino".
     */

    return \App\LockDays::where('tipo', 2)
        ->whereDate('data_inicio', '>=', $dataInicio->format('Y-m-d'))
        ->whereDate('data_inicio', '<', $dataTermino->format('Y-m-d'))
        ->exists();
}


}