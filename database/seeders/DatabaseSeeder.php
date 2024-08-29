<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reserva;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            //UsersSeeder::class,
            //CategoriasSeeder::class,
            //ProdutosSeeder::class,
            ReservaSeeder::class,
        ]);
    }
}
