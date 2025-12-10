<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadesNegocio extends Model
{
    use HasFactory;

    protected $table = 'unidades_negocio';

    protected $fillable = [
        'id_filial',
        'unidade_negocio',
        'cod_unidade',
        'cod_gasto',
        'cod_custo',
        'descri_gasto',
        'descri_custo',
        'nome_gestor',
        'email_gestor',
    ];

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'id_filial', 'id');
    }
    public function gestores()
    {
        return $this->hasMany(GestorFinanceiro::class, 'cod_unidade', 'cod_unidade');
    }

    public function gestorRegional()
    {
        return $this->belongsTo(GestorFinanceiro::class, 'email_regional', 'email_gestor');
    }

}
