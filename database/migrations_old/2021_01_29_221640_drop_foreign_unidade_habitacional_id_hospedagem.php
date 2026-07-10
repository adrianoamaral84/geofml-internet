<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignUnidadeHabitacionalIdHospedagem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hospedagem', function (Blueprint $table) {
           $table->dropForeign('hospedagem_und_habitacionais_id_foreign');
            //
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
