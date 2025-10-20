<?php

namespace App\Models;
use App\Models\UsersGestores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reserva extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',
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
        'user_email',
        'filial_viajante',
        'approval_token',
        'anexo_path',
    ];

    //Relacionamento com a tabela usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

        // Relacionamento com o gestor
    public function gestor()
    {
        return $this->belongsTo(UsersGestores::class, 'email_gestor', 'email'); 
        // 'email_gestor' é a coluna em fiscais
        // 'email' é a coluna correspondente em fiscais_aprovadores
    }

}
