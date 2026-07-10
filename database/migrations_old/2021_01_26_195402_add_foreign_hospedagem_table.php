<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignHospedagemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hospedagem', function (Blueprint $table) {
            /*
            $table->foreign('cpf_usuario')->references('cpf')->on('users');

            $table->foreign('und_habitacionais_id')->references('id')->on('unidades_habitacionais');

            $table->foreign('tarifas_und_id')->references('id')->on('tarifas_und');

            $table->foreign('tipo_tarifa_extra_id')->references('id')->on('tipo_tarifa_extra ');

            $table->foreign('situacao_pgto_id')->references('id')->on('situacao_pgto ');
            */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hospedagem', function (Blueprint $table) {
            //
        });
    }
}
