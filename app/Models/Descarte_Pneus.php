<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Descarte_Pneus extends Model
{
    use HasFactory;

    protected $table = 'descarte_pneus';
    protected $fillable = [
        'name',
        'email',
        'filial_origem',
        'data',
        'hora',
        'cod_pneu',
        'n_dot',
        'status_pneu',
        'motivo_descarte',
        'observacoes'
    ];
}
