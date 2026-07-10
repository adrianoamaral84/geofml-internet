<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValorTarifaExtra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('valor_tarifa_extra', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tipo_tarifa_extra_id')->nullable();
            $table->foreign('tipo_tarifa_extra_id')->references('id')->on('tipo_tarifa_extra');

            $table->float('valor', 8, 2)->nullable();

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
        Schema::dropIfExists('valor_tarifa_extra');
    }
}
