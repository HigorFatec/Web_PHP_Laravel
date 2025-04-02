<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Florestal_Pix extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'data',
        'cupom',
        'placa',
        'km',
        'cpf',
        'name',
        'cnpj',
        'posto',
        'produto',
        'litragem',
        'valor',
        'pix',
        'valor_3',
        'email_gestor',
        'filial'
    ];
}



