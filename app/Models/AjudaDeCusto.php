<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UnidadesNegocio;
use App\Models\Financeiro;


class AjudaDeCusto extends Model
{
    use HasFactory;
    protected $fillable = [
        'fornecedor',
        'cnpj',
        'name',
        'data_admissao',
        'valor_fixo',
        'valor_proporcional',
        'observacao',
        'pix',
        'tipo_pix',
        'banco',
        'agencia',
        'conta',
        'favorecido',
        'cod_unidade',
        'cod_gasto',
        'cod_custo',
        'approval_token',
        'status',
        'user_id'
    ];

// Relacionamento com o unidade_negocio
public function unidades()
{
    return $this->belongsTo(UnidadesNegocio::class, 'cod_unidade', 'cod_unidade'); 
    // 'cod_unidade' é a coluna em financeiro
    // 'cod_unidade' é a coluna correspondente em unidades_negocio
}

public function centroGasto()
{
    return $this->belongsTo(UnidadesNegocio::class, 'cod_gasto', 'cod_gasto');
}

public function centroCusto()
{
    return $this->belongsTo(UnidadesNegocio::class, 'cod_custo', 'cod_custo');
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

// Relacionamento com o financeiro
public function financeiro()
{
    return $this->belongsTo(Financeiro::class, 'id', 'relatorio_id'); 
    // 'email_gestor' é a coluna em fiscais
    // 'email' é a coluna correspondente em fiscais_aprovadores
}



    
}
