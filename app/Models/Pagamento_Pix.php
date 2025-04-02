<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento_Pix extends Model
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
        'produto_arla',
        'litragem_arla',
        'valor_arla',
        'banco',
        'agencia',
        'conta',
        'cnpj_2',
        'favorecido',
        'pix',
        'valor_3',
        'email_gestor',
        'filial'
    ];
}
