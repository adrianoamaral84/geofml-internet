<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospedagemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospedagem', function (Blueprint $table) {
            $table->id();

            $table->string('user_cpf')->nullable();
            $table->foreign('user_cpf')->references('cpf')->on('user');
            
            $table->unsignedBigInteger('und_habitacionais_id')->nullable();
            $table->foreign('und_habitacionais_id')->references('id')->on('unidades_habitacionais');
            
            $table->unsignedBigInteger('tarifas_und_id')->nullable();
            $table->foreign('tarifas_und_id')->references('id')->on('tarifas_und');

            $table->unsignedBigInteger('tipo_tarifa_extra_id')->nullable();
            $table->foreign('tipo_tarifa_extra_id')->references('id')->on('tipo_tarifa_extra');

            $table->date('data_inicio');
            $table->date('data_termino');

            $table->unsignedBigInteger('situacao_pgto_id')->nullable();
            $table->foreign('situacao_pgto_id')->references('id')->on('situacao_pgto');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospedagem');
    }
}
