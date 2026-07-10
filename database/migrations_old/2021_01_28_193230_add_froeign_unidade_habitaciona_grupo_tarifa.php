<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFroeignUnidadeHabitacionaGrupoTarifa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grupo_tarifa', function (Blueprint $table) {
            
        $table->unsignedBigInteger('unidade_habitacional_id')->nullable();
        $table->foreign('unidade_habitacional_id')->references('id')->on('unidades_habitacionais')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grupo_tarifa', function (Blueprint $table) {
            //
        });
    }
}
