<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'filial',
        'password',
        'email_gestor',
        'microsoft_id',
        'ativo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

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

}
