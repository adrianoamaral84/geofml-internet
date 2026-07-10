<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMecenasToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('user', function (Blueprint $table) {
    $table->boolean('mecenas')->default(false)->after('telefone');
});
}

public function down()
{
    Schema::table('user', function (Blueprint $table) {
        $table->dropColumn('mecenas');
    });
}
}
