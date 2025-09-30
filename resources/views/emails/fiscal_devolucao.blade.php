{{-- filepath: resources/views/emails/fiscal_devolucao.blade.php --}}
<h2>Solicitação de Emissão de Nota Fiscal - DEVOLUÇÃO</h2>

<p><b>NOTA FISCAL SERÁ EMITIDA APENAS APÓS A APROVAÇÃO DO GESTOR RESPONSÁVEL ({{$dados['email_gestor']}})</b></p>

<p><b>Protocolo:</b> {{ $fiscal->id }}</p>
<p><b>Empresa solicitante:</b> {{ $dados['empresa_solicitante'] }}</p>
<p><b>CNPJ:</b> {{ $dados['cnpj'] }}</p>
<p><b>Fornecedor:</b> {{ $dados['fornecedor'] }}</p>
<p><b>CNPJ do Fornecedor:</b> {{ $dados['cnpj_fornecedor'] }}</p>
<p><b>Nota Fiscal de origem:</b> {{ $dados['nota_fiscal'] }}</p>
<p><b>Valor da devolução:</b> {{ $dados['valor_devolucao'] }}</p>
<p><b>Valor total da NF:</b> {{ $dados['valor_nf'] }}</p>
<p><b>Mercadoria a ser devolvida:</b> {{ $dados['mercadoria_devolvida'] }}</p>
<p><b>Motivo da devolução:</b> {{ $dados['motivo_operacao'] }}</p>
<p><b>Informações adicionais:</b> {{ $dados['informacoes_adicionais'] }}</p>
<p><b>Filial:</b> {{ $dados['filial'] }}</p>

@if(isset($produtos) && count($produtos))
    <h4>Itens</h4>
    <ul>
        @foreach($produtos as $produto)
            <li>
                Quantidade: {{ $produto['quantidade'] }},
                Código Rodopar: {{ $produto['codigo_rodopar'] }},
                Valor Unitário: {{ $produto['valor_unitario'] }}
            </li>
        @endforeach
    </ul>
@endif

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
