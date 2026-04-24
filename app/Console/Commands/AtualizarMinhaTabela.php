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
        // O DB::raw('novo_saldo') garante que o valor seja copiado individualmente por linha
        DB::table('gestores_financeiro')->update([
            'saldo'      => DB::raw('saldo + novo_saldo'),
            'updated_at' => now() // Para atualizar a data na sua tabela Blade
        ]);

        $this->info('Saldos incrementados com sucesso!');
    }
}
