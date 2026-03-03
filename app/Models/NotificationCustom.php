<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationCustom extends Model
{
    use HasFactory;

    // Define o nome da tabela caso o Laravel não identifique automaticamente
    protected $table = 'notifications_custom';

    // CAMPOS QUE PODEM SER PREENCHIDOS (O que faltava)
    protected $fillable = [
        'user_id',
        'titulo',
        'mensagem',
        'url',
        'is_global',
        'read_at'
    ];
}
