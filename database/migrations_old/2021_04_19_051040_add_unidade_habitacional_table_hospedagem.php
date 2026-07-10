<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnidadeHabitacionalTableHospedagem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hospedagem', function (Blueprint $table) {
            

            //$table->dropForeign('hospedagem_und_habitacionais_id_foreign'); 

           $table->foreign('und_habitacionais_id')->references('id')->on('unidades_habitacionais')->onDelete('set null');
        

           //$table->unsignedBigInteger('tipo_und_id')->nullable();
           //$table->foreign('tipo_und_id')->references('id')->on('tipoundhab')->onDelete('set null');
     

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
