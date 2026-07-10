<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemporadaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporada', function (Blueprint $table) {
     
            $table->id();

            $table->unsignedBigInteger('tipo_temporada_id')->nullable();
            $table->foreign('tipo_temporada_id')->references('id')->on('tipo_temporada')->onDelete('set null');

            $table->date('data_inicio');
            $table->date('data_termino');
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
        Schema::dropIfExists('temporada');
    }
}
