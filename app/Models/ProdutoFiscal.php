<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoFiscal extends Model
{
    use HasFactory;

    protected $table = 'produtos_fiscais';
    protected $fillable = [
        'fiscal_id',
        'quantidade',
        'codigo_rodopar',
        'valor_unitario',
    ];

    public function fiscal()
    {
        return $this->belongsTo(Fiscal::class);
    }
}