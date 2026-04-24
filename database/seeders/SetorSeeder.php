<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setor;

class SetorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = ['admin', 'fiscal', 'financeiro', 'suprimentos', 'ti'];

        foreach ($setores as $s) {
            Setor::updateOrCreate(
                ['nome' => $s], // Procura pelo nome
                ['nome' => $s]  // Se não achar, cria; se achar, mantém
            );
        }
    }
}