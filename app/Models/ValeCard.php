<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValeCard extends Model
{
    protected $table = 'saldo_combustivel_valecard'; // Nome da tabela no banco de dados

    public $timestamps = false; // ou true, dependendo da sua tabela
}
