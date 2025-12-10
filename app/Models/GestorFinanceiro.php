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
    ];

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'cod_fil', 'id');
    }

    public function unidade()
    {
        return $this->belongsTo(UnidadesNegocio::class, 'cod_unidade', 'cod_unidade');
    }
}
