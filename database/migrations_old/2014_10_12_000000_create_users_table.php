<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('nomeguerra', 50);

            $table->string('email')->unique();
            $table->string('cpf', 11)->unique();

            $table->string('idtMil', 15);
            $table->string('telefone', 11);
            //$table->string('secao')->nullable();
            
            $table->unsignedBigInteger('om_id')->nullable();
            $table->foreign('om_id')->references('id')->on('gerenciar_oms');
            
            $table->unsignedBigInteger('postograd_id')->nullable();
            //$table->foreign('postograd_id')->references('id')->on('posto_graduacao');
            
            $table->unsignedBigInteger('perfil_id');
            $table->foreign('perfil_id')->references('id')->on('nivel_acessos');
            
            $table->tinyInteger('status')->default('1');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
