<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyTipoundhabIdTarifasUnd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarifas_und', function (Blueprint $table) {

            $table->foreign('tipoundhab_id')->references('id')->on('unidades_habitacionais')->onDelete('cascade');

          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tarifas_und', function (Blueprint $table) {
            //
        });
    }
}
