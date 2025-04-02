<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = [
        'razao_social',
        'inscricao_estadual',
        'cnpj',
        'banco',
        'agencia',
        'conta',
        'tipo_conta',
        'pix',
        'telefone_1',
        'telefone_2',
        'direto_com',
        'email',
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'complemento',
    ];

}
