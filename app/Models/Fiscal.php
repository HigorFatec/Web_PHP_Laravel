<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fiscal extends Model
{
    use HasFactory;

    protected $table = 'fiscais';
    protected $fillable = [
        'tipo',
        'empresa_solicitante',
        'cnpj',
        'fornecedor',
        'cnpj_fornecedor',
        'nota_fiscal',
        'quantidade_itens',
        'codigo_rodopar_item',
        'valor_devolucao',
        'valor_nf',
        'mercadoria_devolvida',
        'motivo_operacao',
        'informacoes_adicionais',
        'cliente',
        'cnpj_cliente',
        'codigo_fornecedor_rodopar',
        'filial',
        'email',
        'email_gestor',
        'tipo_pix',
        'placa',
        'prazo',
        'tipo_de_venda',
        'finalidade_da_compra',
    ];

    public function produtos()
    {
        return $this->hasMany(ProdutoFiscal::class);
    }
}