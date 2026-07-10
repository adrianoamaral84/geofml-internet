<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignTipoUnidadeHabitacionaGrupoTarifa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grupo_tarifa', function (Blueprint $table) {
            
        $table->foreign('unidade_habitacional_id')->references('id')->on('tipoundhab')->onDelete('cascade');
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
