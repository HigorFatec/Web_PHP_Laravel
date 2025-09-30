{{-- filepath: resources/views/emails/fiscal_venda.blade.php --}}
<h2>Solicitação de Emissão de Nota Fiscal - VENDA</h2>

<p><b>NOTA FISCAL SERÁ EMITIDA APENAS APÓS A APROVAÇÃO DO GESTOR RESPONSÁVEL ({{$dados['email_gestor']}})</b></p>

<p><b>Tipo de Venda:</b> {{ $dados['tipo_de_venda'] }}</p>
<p><b>Protocolo:</b> {{ $fiscal->id }}</p>
<p><b>Empresa solicitante:</b> {{ $dados['empresa_solicitante'] }}</p>
<p><b>CNPJ:</b> {{ $dados['cnpj'] }}</p>
<p><b>Cliente:</b> {{ $dados['cliente'] }}</p>
<p><b>Código Cliente Rodopar:</b> {{ $dados['codigo_cliente_rodopar'] ?? '-' }}</p>
<p><b>CNPJ do Cliente:</b> {{ $dados['cnpj_cliente'] }}</p>
<p><b>Motivo da operação:</b> {{ $dados['motivo_operacao'] }}</p>
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
