<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGerenciarOmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gerenciar_oms', function (Blueprint $table) {
            
            

            $table->unsignedBigInteger('forca_id');            
            $table->foreign('forca_id')->references('id')->on('forca');

            $table->unsignedBigInteger('cidade_id');            
            $table->foreign('cidade_id')->references('id')->on('cidade');

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
        //
    }
}
