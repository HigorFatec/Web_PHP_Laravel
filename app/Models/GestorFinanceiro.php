<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestorFinanceiro extends Model
{
    use HasFactory;

    protected $table = 'gestores_financeiro';

    protected $fillable = [
        'cod_fil',
        'cod_unidade',
        'nome_gestor',
        'email_gestor',
        'saldo',
        'novo_saldo',
    ];

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'cod_fil', 'id');
    }

    public function unidade()
    {
        return $this->belongsTo(UnidadesNegocio::class, 'cod_unidade', 'cod_unidade');
    }
    public function unidadeNegocio()
    {
        // Troque belongsTo por hasMany para retornar uma coleção
        return $this->hasMany(UnidadesNegocio::class, 'email_regional', 'email_gestor');
    }

    public function gastoMensal()
    {
        // Argumentos: (Classe destino, Chave estrangeira no Financeiro, Chave local no Gestor)
        return $this->hasMany(Financeiro::class, 'gestor_aprovador', 'email_gestor');
    }
    
}
