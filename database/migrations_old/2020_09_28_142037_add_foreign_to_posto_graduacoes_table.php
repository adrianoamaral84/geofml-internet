<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignToPostoGraduacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('forca')) {
    //

        Schema::table('posto_graduacaos', function (Blueprint $table) {

            $table->unsignedBigInteger('forca_id');
            $table->foreign('forca_id')->references('id')->on('forca');
        });

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posto_graduacaos', function (Blueprint $table) {
            //
        });
    }
}
