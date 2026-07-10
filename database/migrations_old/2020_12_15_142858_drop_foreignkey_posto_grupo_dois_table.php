<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignkeyPostoGrupoDoisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grupo_tarifa_posto_graduacao', function (Blueprint $table) {


            $table->dropForeign('grupo_tarifa_posto_graduacao_posto_id_foreign');
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
        Schema::table('grupo_tarifa_posto_graduacao', function (Blueprint $table) {
            //
        });
    }
}
