<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospedagem extends Model
{
    use HasFactory;
    protected $fillable = [
        'destino',
        'ida',
        'volta',
        'motivo',
        'referencia',
        'validacao',
        'email_gestor',
        'observacoes',
        'nome',
        'cpf',
        'rg',
        'data_nascimento',
        'email',
        'cpf',
        'filial_viajante',
        'user_name',
        'user_id',
        'user_cpf',
        'user_email',
        'approval_token',
        'anexo_path'
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