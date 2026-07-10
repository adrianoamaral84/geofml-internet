<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifasUndTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarifas_und', function (Blueprint $table) {
            $table->id();
            
            $table->float('valor', 8, 2)->nullable();

            $table->unsignedBigInteger('tipoundhab_id')->nullable();
            $table->foreign('tipoundhab_id')->references('id')->on('tipoundhab')->onDelete('set null');
            
            $table->unsignedBigInteger('grupo_destinacao_id')->nullable();
            $table->foreign('grupo_destinacao_id')->references('id')->on('grupo_destinacao')->onDelete('set null');

            $table->unsignedBigInteger('temporada_id')->nullable();
            $table->foreign('temporada_id')->references('id')->on('temporada');
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
        Schema::dropIfExists('tarifas_und');
    }
}
