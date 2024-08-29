<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reserva extends Model
{
    use HasFactory;
    protected $fillable = [
        'origem',
        'destino',
        'tipo',
        'ida',
        'volta',
        'motivo',
        'validacao',
        'email_gestor',
        'observacoes',
        'nome',
        'cpf',
        'rg',
        'data_nascimento',
        'email',
        'cpf',
        'user_name',
        'user_id',
        'user_cpf',
        'user_email'
    ];

    //Relacionamento com a tabela usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
