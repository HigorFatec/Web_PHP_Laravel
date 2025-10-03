<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FiscalAprovador;


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
        'nome_gestor',
        'tipo_pix',
        'placa',
        'prazo',
        'tipo_de_venda',
        'finalidade_da_compra',
        'approval_token',
        'status',
        'emails',
        'anexo_path',

    ];

    public function produtos()
    {
        return $this->hasMany(ProdutoFiscal::class);
    }

    // Relacionamento com o gestor
    public function gestor()
    {
        return $this->belongsTo(FiscalAprovador::class, 'email_gestor', 'email'); 
        // 'email_gestor' é a coluna em fiscais
        // 'email' é a coluna correspondente em fiscais_aprovadores
    }
}