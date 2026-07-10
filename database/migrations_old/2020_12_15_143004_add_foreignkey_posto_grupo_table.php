<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignkeyPostoGrupoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grupo_tarifa_posto_graduacao', function (Blueprint $table) {



           // $table->unsignedBigInteger('posto_id');
            $table->foreign('posto_id')->references('id')->on('posto_graduacao')->onDelete('cascade');
         
            //$table->unsignedBigInteger('grupotarifa_id');
            $table->foreign('grupotarifa_id')->references('id')->on('grupo_tarifa')->onDelete('cascade');
         
           
         
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
