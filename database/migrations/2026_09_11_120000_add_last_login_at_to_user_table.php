<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLastLoginAtToUserTable extends Migration
{
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable();
        });

        // Evita bloquear imediatamente contas antigas no primeiro deploy.
        // A partir daqui, o prazo de 90 dias passa a contar normalmente.
        DB::table('user')->whereNull('last_login_at')->update([
            'last_login_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
    }
}
