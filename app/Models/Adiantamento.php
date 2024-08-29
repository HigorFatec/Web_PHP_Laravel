<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adiantamento extends Model
{
    use HasFactory;
    protected $fillable = [
        'destino',
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
        'banco',
        'agencia',
        'conta',
        'tipo_conta',
        'titular',
        'pix',
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