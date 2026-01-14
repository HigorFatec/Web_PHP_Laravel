<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class AtualizarMinhaTabela extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tabela:atualizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza registros da tabela todos os dias às 8h';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('gestores_financeiro')
            ->update([
                'saldo' => '100000',
                'updated_at' => now()
            ]);

        $this->info('Tabela atualizada com sucesso!');
    }
}
