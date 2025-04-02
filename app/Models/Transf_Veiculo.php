<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transf_Veiculo extends Model
{
    use HasFactory;
    protected $fillable = [
        'data',
        'name',
        'email',
        'placa',
        'placa_carreta',
        'placa_carreta_2',
        'placa_carreta_3',
        'filial_origem',
        'filial_destino',
        'centro_custo',
        'centro_gasto',
        'previsao_chegada',
        'email_responsavel',
        'conferencia_pneus',
    ];
    protected $table = 'transf__veiculos';
}
