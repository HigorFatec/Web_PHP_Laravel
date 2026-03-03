<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KmAtualizadoVeiculo extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'KM_Atualizado_Veiculos';

    protected $primaryKey = null; // view normalmente não tem PK
    public $incrementing = false;
    public $timestamps = false;
}
