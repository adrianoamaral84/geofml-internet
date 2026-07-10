<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\UserDocumento;

class MigrarDocumentosUser extends Command
{
    protected $signature = 'geofml:migrar-documentos';
    protected $description = 'Migra documentos da tabela user para user_documentos';

    public function handle()
    {
        $this->info('Iniciando migração dos documentos...');

        $usuarios = DB::table('user')
            ->select('id', 'documento', 'tipo_doc', 'documento_verso', 'tipo_doc_verso')
            ->orderBy('id')
            ->get();

        $migrados = 0;
        $ignorados = 0;

        foreach ($usuarios as $usuario) {
    if (!empty($usuario->documento)) {
        UserDocumento::updateOrCreate(
            [
                'user_id' => $usuario->id,
                'tipo' => 'frente',
            ],
            [
                'arquivo' => $usuario->documento,
                'mime' => $usuario->tipo_doc,
                'tamanho' => strlen($usuario->documento),
                'hash' => hash('sha256', $usuario->documento),
            ]
        );

        $migrados++;
    }

    if (!empty($usuario->documento_verso)) {
        UserDocumento::updateOrCreate(
            [
                'user_id' => $usuario->id,
                'tipo' => 'verso',
            ],
            [
                'arquivo' => $usuario->documento_verso,
                'mime' => $usuario->tipo_doc_verso,
                'tamanho' => strlen($usuario->documento_verso),
                'hash' => hash('sha256', $usuario->documento_verso),
            ]
        );

        $migrados++;
    }

    if (empty($usuario->documento) && empty($usuario->documento_verso)) {
        $ignorados++;
    }
}
           
            

          

        $this->info('Migração finalizada.');
        $this->info('Documentos migrados: ' . $migrados);
        $this->info('Usuários sem documento: ' . $ignorados);

        return 0;
    }
}