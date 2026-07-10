<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColummForeignPostoGraduacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posto_graduacao', function (Blueprint $table) {
            
            $table->unsignedBigInteger('situacao_id')->nullable();
            $table->foreign('situacao_id')->references('id')->on('situacao');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posto_graduacao', function (Blueprint $table) {
            //
        });
    }
}
