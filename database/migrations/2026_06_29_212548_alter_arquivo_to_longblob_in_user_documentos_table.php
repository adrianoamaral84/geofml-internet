<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterArquivoToLongblobInUserDocumentosTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE `user_documentos` MODIFY `arquivo` LONGBLOB NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `user_documentos` MODIFY `arquivo` VARCHAR(255) NOT NULL");
    }
}