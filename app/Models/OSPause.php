<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OSPause extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'os_pauses'; // Nome da tabela, se diferente do padrão

    protected $fillable = [
        'assignment_id',
        'pause_number',
        'reason_code',
        'start_time',
        'end_time',
    ];

    // (Opcional) Se quiser o inverso:
    public function assignment()
    {
        return $this->belongsTo(OSAssignment::class, 'assignment_id');
    }
}
