<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaixaTemporadaQuantidadeDiasHospedagemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quantidade_dias_hospedagem', function (Blueprint $table) {
            $table->Integer('qnt_reservas_baixa_temporada');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quantidade_dias_hospedagem', function (Blueprint $table) {
            //
        });
    }
}
