<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Hospede extends Model
{
    protected $table = "hospedagem";

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function tipouh(){
        return $this->belongsTo(TipoUndHab::class, 'tipo_und_id');
    }

    public function undHB(){
        return $this->belongsTo(UnidadeHabitacional::class, 'und_habitacionais_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comprovante(){
        return $this->hasOne(Comprovante::class, 'hospedagem_id');
    }

    public function status_hospedagem(){
        return $this->belongsTo(Status_hospedagem::class, 'status');
    }
    
    public function valorTarifaComDesconto()
    {
        /*
         * A tarifa salva em hospedagem.valortarifa ja deve refletir o valor
         * unitario usado no pedido. Reaplicar Mecenas aqui causa desconto
         * duplicado nos recalculos de hospedagem.
         */
        return round((float) $this->valortarifa, 2);
    }

    public function valorPrimeiraDiariaComDesconto()
    {
        $tarifa = $this->tarifaPrimeiraDiariaSemDesconto();

        if ($tarifa !== null) {
            return $this->user
                ? $this->user->aplicarDesconto($tarifa)
                : round((float) $tarifa, 2);
        }

        return $this->valorTarifaSalvaComDesconto();
    }

    private function tarifaPrimeiraDiariaSemDesconto()
    {
        if (!$this->data_inicio || !$this->tipo_und_id || !$this->user) {
            return null;
        }

        $posto = $this->user->posto;

        if (!$posto) {
            return null;
        }

        $gruposTarifa = $posto->grupotarifa()->get();

        if ($gruposTarifa->isEmpty()) {
            return null;
        }

        $tarifa = null;

        foreach ($gruposTarifa as $grupoTarifa) {
            $tarifa = Tarifas::where('tipoundhab_id', $this->tipo_und_id)
                ->where('grupo_destinacao_id', $grupoTarifa->id)
                ->first();

            if ($tarifa) {
                break;
            }
        }

        if (!$tarifa) {
            return null;
        }

        $dataEntrada = Carbon::parse($this->data_inicio)->format('Y-m-d');

        $temporada = Temporada::whereDate('data_inicio', '<=', $dataEntrada)
            ->whereDate('data_termino', '>=', $dataEntrada)
            ->first();

        if (!$temporada) {
            return null;
        }

        if ((int) $temporada->tipo_temporada_id === 2) {
            return round((float) $tarifa->valor_baixa, 2);
        }

        if ((int) $temporada->tipo_temporada_id === 1) {
            return round((float) $tarifa->valor, 2);
        }

        return null;
    }

    private function valorTarifaSalvaComDesconto()
    {
        $diarias = (int) ($this->qntdiarias ?? 0);
        $valorTotal = (float) ($this->valor ?? 0);

        if ($diarias > 0 && $valorTotal > 0) {
            return round($valorTotal / $diarias, 2);
        }

        return round((float) $this->valortarifa, 2);
    }

}
