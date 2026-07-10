<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadesHabitacionaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidades_habitacionais', function (Blueprint $table) {
            $table->id();
            $table->string('sigla', 10);
            $table->string('descricao', 100);
            $table->Integer('qtd_quartos');
            $table->string('capacidade_ocupacao', 3);
            $table->tinyInteger('sala');
            $table->tinyInteger('coznha');
            $table->tinyInteger('pet');
            $table->tinyInteger('disponivel');
            $table->string('observacao');
            
            $table->unsignedBigInteger('tipo_und_hab_id')->nullable();
            $table->foreign('tipo_und_hab_id')->references('id')->on('tipoundhab')->onDelete('set null');
            
            $table->unsignedBigInteger('classe_habitacional_id')->nullable();
            $table->foreign('classe_habitacional_id')->references('id')->on('classe_habitacional')->onDelete('set null');
            
            $table->unsignedBigInteger('grupo_destinacao_id')->nullable();
            $table->foreign('grupo_destinacao_id')->references('id')->on('grupo_destinacao')->onDelete('set null');
            
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
        Schema::dropIfExists('unidades_habitacionais');
    }
}
