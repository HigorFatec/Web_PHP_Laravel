<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCredential extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users_credentials';

    protected $fillable = [
        'matricula',
        'is_admin',
        'admin_level',
        'password',
        'ativo'

    ];

public function setores()
{
    return $this->belongsToMany(Setor::class);
}

// Função auxiliar para facilitar a checagem no código
public function temSetor($nomes)
{
    // 1. Garante que $nomes seja um array
    $nomes = is_array($nomes) ? $nomes : [$nomes];

    // 2. Faz uma consulta direta na tabela de ligação (pivô)
    // Isso ignora qualquer cache e verifica a realidade do banco de dados agora.
    return $this->setores()
                ->whereIn('nome', $nomes)
                ->exists();
}

    public $timestamps = false;
}
