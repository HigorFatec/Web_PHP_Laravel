<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Funcionario extends Authenticatable
{
    protected $connection = 'sqlsrv'; // Conexão SQL Server
    protected $table = 'OSEFUN';      // Nome real da tabela
    protected $primaryKey = 'CODFUN';     // Troque se a PK for diferente
    public $timestamps = false;       // Se não tiver `created_at` e `updated_at`

    protected $fillable = [
        'CODFUN',
        'NOMEFUN',
        'CODFIL',
        'CODUNN',
        // Inclua aqui os campos que quiser usar
    ];
}