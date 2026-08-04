<?php

namespace App\Http\Controllers\Pedidos;

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



class PedidosController extends Controller
{




/*

NUMERO 01

CHAMA FORMULARIO "CREATE" PEDIDO DE HOSPEDAGEM

Faz os calculos de meses conforme regras estabelecidas

*/
public function chamaFormularioPedido(){

    

/*
Carbon::setTestNow(
    Carbon::create(2026, 11, 8, 12, 0, 0, 'America/Sao_Paulo')
);
*/
date_default_timezone_set('America/Sao_Paulo');
$usuarioAutenticado = Auth::user();
$today = Carbon::today();



if (
        (int) $usuarioAutenticado->indeterminado !== 1 &&
        !empty($usuarioAutenticado->validade)
    ) {
        $validade = Carbon::parse(
            $usuarioAutenticado->validade
        )->startOfDay();

        if ($validade->lt($today)) {
            \Session::flash('message', [
                'msg' => 'Seu documento de identidade está com a data de validade vencida! Favor atualizar o documento para prosseguir.',
                'class' => 'danger',
            ]);

            return redirect()->route('home');
        }
    }

    /*
|--------------------------------------------------------------------------
| Tipos de unidade permitidos pelo posto/graduação
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Busca os grupos e suas unidades em uma única consulta
|--------------------------------------------------------------------------
*/

$gruposTarifa = \App\GrupoTarifa::with('tipoundhabitacao')
    ->whereIn('id', $grupoTarifaIds)
    ->get();

/*
|--------------------------------------------------------------------------
| Monta o array usado na view
|--------------------------------------------------------------------------
|
| Formato:
|
| [
|     ['id' => 1, 'value' => 'Apartamento'],
|     ['id' => 2, 'value' => 'Chalé'],
| ]
|
*/

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
$diaCorte = (int) $diaBloqueado->dia;          // 10
$diaLimite = (int) $diaBloqueado->limitedia;   // 5

$dataBase = $today->copy()->startOfMonth();

/*
|--------------------------------------------------------------------------
| Verifica a temporada da data atual
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
        'msg' => 'Não existe temporada cadastrada para a data atual.',
        'class' => 'danger',
    ]);

    return redirect()->back();
}

$isAltaTemporada =
    (int) $temporadaAtual->tipo_temporada_id === 1;

$isBaixaTemporada =
    (int) $temporadaAtual->tipo_temporada_id === 2;

/*
|--------------------------------------------------------------------------
| Descobre o mês principal aberto
|--------------------------------------------------------------------------
|
| Até o dia 10:
| abre o próximo mês.
|
| A partir do dia 11:
| abre o mês seguinte ao próximo.
|
*/

if ($diaAtual <= $diaCorte) {
    $mesAberto = $dataBase
        ->copy()
        ->addMonthNoOverflow();
} else {
    $mesAberto = $dataBase
        ->copy()
        ->addMonthsNoOverflow(2);
}

/*
|--------------------------------------------------------------------------
| Data máxima
|--------------------------------------------------------------------------
|
| O calendário termina no dia 5 do mês seguinte ao mês aberto.
|
| Exemplo:
| mês aberto = abril
| maxDate = 5 de maio
|
*/

$mesDoLimite = $mesAberto
    ->copy()
    ->addMonthNoOverflow()
    ->startOfMonth();

$ultimoDiaMesLimite = $mesDoLimite
    ->copy()
    ->endOfMonth()
    ->day;

$diaLimiteValido = min(
    $diaLimite,
    $ultimoDiaMesLimite
);

$maxDate = $mesDoLimite
    ->copy()
    ->day($diaLimiteValido)
    ->format('Y-m-d');

/*
|--------------------------------------------------------------------------
| Data mínima
|--------------------------------------------------------------------------
*/

if ($isBaixaTemporada) {

    /*
     * Março até novembro:
     * pode solicitar para hoje, amanhã ou datas futuras.
     */

    $minDate = $today->format('Y-m-d');

} elseif ($isAltaTemporada) {

    /*
     * Dezembro, janeiro e fevereiro:
     * somente o mês que está aberto pelo ciclo de inscrição.
     */

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


/*
|--------------------------------------------------------------------------
| Bloqueios administrativos
|--------------------------------------------------------------------------
*/

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



/*
|--------------------------------------------------------------------------
| Bloqueio especial de dezembro
|--------------------------------------------------------------------------
|
| Depois de 10 de novembro, dezembro já está fechado.
|
*/

if (
    $today->month === 11 &&
    $diaAtual > $diaCorte
) {
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


/*
|--------------------------------------------------------------------------
| Outros dados da tela
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Retorno para a view
|--------------------------------------------------------------------------
*/
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































/*

numero 02

Rcebe os dados do formulario pedido para usuario verificar se esta tudo certo e clicar em cdastrar

*/

public function confimrarPedido(Request $request){

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
    | Interpreta o período
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
         * Evita aceitar datas inválidas corrigidas
         * automaticamente pelo Carbon.
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
    | Janela permitida
    |--------------------------------------------------------------------------
    */

    try {
        $janela = $this->calcularJanelaInscricao(
            $today,
            $diaBloqueado,
            $temporadaAtual
        );
    } catch (\Throwable $e) {
        \Session::flash('message', [
            'msg' =>
                'Não foi possível calcular o período de inscrições.',
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
    | Depois do dia 10 de novembro, dezembro está fechado.
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
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Verifica se a UH é permitida para o usuário
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
    | Tarifa da unidade
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
    | Temporadas que cruzam o período
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
    | Conta as diárias de alta e baixa temporada
    |--------------------------------------------------------------------------
    |
    | Entrada: 10/03
    | Saída:   12/03
    |
    | Diárias cobradas:
    | 10/03 e 11/03
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
                'Não foi possível classificar todas as diárias '
                . 'do período selecionado.',
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
    | Limite de 7 diárias na alta temporada
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
    | Configuração dos limites mensais
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

    /*
    |--------------------------------------------------------------------------
    | Pedidos do usuário no mês da entrada
    |--------------------------------------------------------------------------
    */

    $consultaPedidosDoMes =
        \App\Hospede::where(
            'user_id',
            $usuario->id
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
    | Limite Camping/Motor-Home
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
    | Valores das tarifas
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
    | Subtotais brutos
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
    | O model User já define:
    |
    | percentual_desconto = 30 ou 0
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
    | Tarifas unitárias com desconto
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
         * Período misto:
         * mantém a tarifa alta como referência.
         */

        $valorTarifa =
            $valorDiariaAlta;

        $valorTarifaComDesconto =
            $valorDiariaAltaComDesconto;
    }

    /*
    |--------------------------------------------------------------------------
    | Dados normalizados para a confirmação
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

    $tipos = \App\TipoUndHab::all();

    /*
    |--------------------------------------------------------------------------
    | Total líquido criptografado
    |--------------------------------------------------------------------------
    |
    | Serve para manter compatibilidade com a etapa seguinte.
    | O total deve ser recalculado novamente na gravação final.
    |
    */

    $totalValorCriptografado =
        Crypt::encrypt($totalValorLiquido);

    /*
    |--------------------------------------------------------------------------
    | Retorno para a confirmação
    |--------------------------------------------------------------------------
    */

    return view(
        'hospedagem.confirmarantigo',
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
             * Valores unitários.
             */

            'valorDiariaAlta' =>
                $valorDiariaAlta,

            'valorDiariaBaixa' =>
                $valorDiariaBaixa,

            'valorDiariaAltaComDesconto' =>
                $valorDiariaAltaComDesconto,

            'valorDiariaBaixaComDesconto' =>
                $valorDiariaBaixaComDesconto,

            /*
             * Subtotais.
             */

            'calculaDiariaAlta' =>
                $calculaDiariaAlta,

            'calculaDiariaBaixa' =>
                $calculaDiariaBaixa,

            'calculaDiariaAltaComDesconto' =>
                $calculaDiariaAltaComDesconto,

            'calculaDiariaBaixaComDesconto' =>
                $calculaDiariaBaixaComDesconto,

            /*
             * Mecenas.
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

























public function store(Request $request)
{
    
    date_default_timezone_set('America/Sao_Paulo');
    $usuario = Auth::user();
    $today = Carbon::today();

    /*
    |--------------------------------------------------------------------------
    | Validação básica
    |--------------------------------------------------------------------------
    */

    $validator = Validator::make(
        $request->all(),
        [
            'id' => [
                'nullable',
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
         * Impede datas inválidas ajustadas automaticamente,
         * como 31-02.
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
    | Localiza o pedido na edição
    |--------------------------------------------------------------------------
    */

    $isEdicao = !empty($request->id);
    $pedidoAtual = null;

    if ($isEdicao) {
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
                'class' => 'danger',
            ]);

            return redirect()
                ->route('hospede.meuspedidos');
        }

        /*
         * Caso exista controle de status, valide aqui.
         *
         * Exemplo:
         *
         * if (!in_array($pedidoAtual->status, ['PENDENTE', 'AGUARDANDO'])) {
         *     return redirect()
         *         ->route('hospede.meuspedidos')
         *         ->with(...);
         * }
         */
    }

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
    | Janela permitida
    |--------------------------------------------------------------------------
    */

    try {
        $janela = $this->calcularJanelaInscricao(
            $today,
            $diaBloqueado,
            $temporadaAtual
        );
    } catch (\Throwable $e) {
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
    | Depois do dia 10 de novembro, dezembro já está fechado.
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
    | Na edição, exclui o próprio registro.
    |
    */

    $consultaDuplicidade = \App\Hospede::where(
            'data_inicio',
            $dataInicio->format('Y-m-d')
        )
        ->where(
            'data_termino',
            $dataTermino->format('Y-m-d')
        )
        ->where(
            'user_id',
            $usuario->id
        );

    if ($isEdicao) {
        $consultaDuplicidade->where(
            'id',
            '<>',
            $pedidoAtual->id
        );
    }

    if ($consultaDuplicidade->exists()) {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'peridoinicial' =>
                    'Você já possui uma inscrição para esse mesmo período.',
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
    | Verifica se a UH é permitida
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
    | Tarifa
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
    | Temporadas que cruzam o período
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
    | A data da saída não é cobrada como nova diária.
    |
    */

    $diasAltaTemporada = 0;
    $diasBaixaTemporada = 0;

    for (
        $data = $dataInicio->copy();
        $data->lt($dataTermino);
        $data->addDay()
    ) {
        $temporadaDaDiaria =
            $temporadasDoPeriodo->first(
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
    | Limite de sete diárias
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

    $consultaPedidosDoMes =
        \App\Hospede::where(
            'user_id',
            $usuario->id
        )
        ->whereYear(
            'data_inicio',
            $dataInicio->year
        )
        ->whereMonth(
            'data_inicio',
            $dataInicio->month
        );

    if ($isEdicao) {
        $consultaPedidosDoMes->where(
            'id',
            '<>',
            $pedidoAtual->id
        );
    }

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
    | Aplica o desconto Mecenas pelo model User
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
    | Tarifa de referência salva no banco
    |--------------------------------------------------------------------------
    |
    | Em período misto, valor representa o total correto, mas valortarifa
    | guarda apenas uma tarifa de referência.
    |
    */

    if (
        $diasAltaTemporada > 0 &&
        $diasBaixaTemporada === 0
    ) {
        $valorTarifa = $usuario->aplicarDesconto(
            $valorDiariaAlta
        );
    } elseif (
        $diasBaixaTemporada > 0 &&
        $diasAltaTemporada === 0
    ) {
        $valorTarifa = $usuario->aplicarDesconto(
            $valorDiariaBaixa
        );
    } else {
        /*
         * Período misto: mantém a tarifa alta como referência.
         */

        $valorTarifa = $usuario->aplicarDesconto(
            $valorDiariaAlta
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Gravação
    |--------------------------------------------------------------------------
    */

    try {
        DB::beginTransaction();

        $consulta = $isEdicao
            ? $pedidoAtual
            : new \App\Hospede();

        $consulta->user_cpf =
            $usuario->cpf;

        $consulta->user_id =
            $usuario->id;

        $consulta->tipo_und_id =
            (int) $request->tipo;

        $consulta->data_inicio =
            $dataInicio->format('Y-m-d');

        $consulta->data_termino =
            $dataTermino->format('Y-m-d');

        $consulta->adulto =
            (int) $request->adultos;

        $consulta->crianca =
            (int) $request->criancas;

        $consulta->pne =
            (int) $request->pne;

        $consulta->pet =
            (int) $request->pet;

        $consulta->observacao =
            $request->filled('observacao')
                ? trim($request->observacao)
                : null;

        /*
         * Valor final já com desconto Mecenas.
         */

        $consulta->valor =
            $totalValorLiquido;

        /*
         * Tarifa unitária de referência, também com desconto.
         */

        $consulta->valortarifa =
            round((float) $valorTarifa, 2);

        $consulta->qntdiarias =
            $diasHospedagem;

        $consulta->save();

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();

        report($e);

        \Session::flash('message', [
            'msg' =>
                'Não foi possível salvar a inscrição. '
                . 'Tente novamente.',
            'class' => 'danger',
        ]);

        return redirect()
            ->back()
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Resultado
    |--------------------------------------------------------------------------
    */

    if ($isEdicao) {
        \Session::flash('message', [
            'msg' =>
                'Inscrição atualizada com sucesso!',
            'class' => 'success',
        ]);

        return redirect()
            ->route('hospede.meuspedidos');
    }

    /*
    |--------------------------------------------------------------------------
    | E-mail da nova solicitação
    |--------------------------------------------------------------------------
    |
    | Mail::queue não retorna um indicador confiável de envio.
    | O envio ocorre na fila.
    |
    */

    try {
        Mail::queue(
            new \App\Mail\ConfirmaHospedagem($consulta)
        );

        \Session::flash('message', [
            'msg' =>
                'Inscrição realizada! Você receberá os dados '
                . 'da solicitação por e-mail.',
            'class' => 'success',
        ]);
    } catch (\Throwable $e) {
        /*
         * O pedido já foi salvo.
         * Falha no e-mail não deve apagar a inscrição.
         */

        report($e);

        \Session::flash('message', [
            'msg' =>
                'Inscrição realizada com sucesso, mas não foi '
                . 'possível colocar o e-mail na fila.',
            'class' => 'warning',
        ]);
    }

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