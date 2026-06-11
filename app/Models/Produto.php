<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';
    protected $fillable = [
        'nome_remetente',
        'email_remetente',
        'email_aprovador',
        'nome',
        'ncm',
        'ca',
        'tipo',
        'approval_token',
        'status',
        'user_id',
        'descricao_curta',
        'filial',
        'motivo_cancelamento'
         // Adicione outros campos conforme necessário
    ];



}
