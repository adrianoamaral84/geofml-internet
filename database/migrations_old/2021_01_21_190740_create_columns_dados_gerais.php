<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnsDadosGerais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dados_gerais', function (Blueprint $table) {
            
            $table->binary('cabecalho');
            $table->string('nome_secao');
            $table->string('assinatura');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dados_gerais', function (Blueprint $table) {
            //
        });
    }
}
