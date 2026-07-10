<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoTarifaPostoGraduacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo_tarifa_posto_graduacao', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('posto_id');
            $table->foreign('posto_id')->references('id')->on('posto_graduacao');
         
            $table->unsignedBigInteger('grupotarifa_id');
            $table->foreign('grupotarifa_id')->references('id')->on('grupo_tarifa');
         
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupo_tarifa_posto_graduacao');
    }
}
