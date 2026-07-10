<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {

            $table->string('endereco');
            $table->string('cep');
            
            $table->unsignedBigInteger('uf_id');            
            $table->foreign('uf_id')->references('id')->on('uf');

            $table->unsignedBigInteger('cidade_id');            
            $table->foreign('cidade_id')->references('id')->on('cidade');

            $table->tinyInteger('pttc')->nullable();

            $table->timestamp('dtUltPromo')->nullable();

            $table->unsignedBigInteger('forca_id')->nullable();
            $table->foreign('forca_id')->references('id')->on('forca');         

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            //
        });
    }
}
