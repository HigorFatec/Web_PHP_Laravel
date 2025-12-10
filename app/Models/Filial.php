<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UnidadesNegocio;
use App\Models\GestorFinanceiro;


class Filial extends Model
{
    use HasFactory;
    protected $table = 'filiais';

    public function unidades_negocio()
    {
        return $this->hasMany(UnidadesNegocio::class, 'id_filial', 'id');
    }

    public function gestores()
    {
        return $this->hasMany(GestorFinanceiro::class, 'cod_fil', 'id');
    }

}
