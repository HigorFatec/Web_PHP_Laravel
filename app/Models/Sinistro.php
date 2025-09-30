<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sinistro extends Model
{
    use HasFactory;
        protected $fillable = [
        'data',
        'hora',
        'name',
        'telefone',
        'email',
        'placa',
        'filial_origem',
        'email_terceiro',
        'telefone_terceiro',
        'nome_terceiro',
        'placa_terceiro',
        'ocorrido'
    ];
    protected $table = 'sinistro';
}
