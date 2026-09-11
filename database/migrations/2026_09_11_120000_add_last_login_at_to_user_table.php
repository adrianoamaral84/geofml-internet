<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastLoginAtToUserTable extends Migration
{
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
    }
}
