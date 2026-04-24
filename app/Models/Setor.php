<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    // OBRIGATÓRIO avisar o nome da tabela, senão o Laravel vai procurar 'setors'
    protected $table = 'setores';

    protected $fillable = ['nome'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}