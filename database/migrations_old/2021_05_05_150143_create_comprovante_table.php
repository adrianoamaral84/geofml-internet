<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprovanteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comprovante', function (Blueprint $table) {
            $table->id();

            $table->string('caminho');

            $table->unsignedBigInteger('hospedagem_id');
            $table->foreign('hospedagem_id')->references('id')->on('hospedagem');
   
            //$table->unsignedBigInteger('user_id');
            //$table->foreign('user_id')->references('id')->on('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comprovante');
    }
}
