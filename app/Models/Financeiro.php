<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financeiro extends Model
{
    use HasFactory;
    protected $fillable = [
        'tipo',
        'pedido',
        'referencia',
        'cnpj',
        'name',
        'pamcard',
        'banco',
        'agencia',
        'conta',
        'pix',
        'favorecido',
        'valor',
        'prazo',
        'motivo',
        'filial',
        'email',
        'email_gestor',
        'tipo_pix',
        'placa',
        'prazo',

    ];
}
