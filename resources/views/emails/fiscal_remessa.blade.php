{{-- filepath: resources/views/emails/fiscal_remessa.blade.php --}}
<h2>Solicitação de Emissão de Nota Fiscal - REMESSA</h2>

<p><b>NOTA FISCAL SERÁ EMITIDA APENAS APÓS A APROVAÇÃO DO GESTOR RESPONSÁVEL ({{$fiscal->email_gestor}})</b></p>

<p><b>Tipo de Venda:</b> {{ $fiscal->tipo_de_venda }}</p>

<p><b>Protocolo:</b> {{ $fiscal->id }}</p>
<p><b>Empresa solicitante:</b> {{ $fiscal->empresa_solicitante }}</p>
<p><b>CNPJ:</b> {{ $fiscal->cnpj }}</p>
<p><b>Filial de Recebimento:</b> {{ $fiscal->fornecedor }}</p>
<p><b>Código Filial de Recebimento Rodopar:</b> {{ $fiscal->codigo_fornecedor_rodopar }}</p>
<p><b>CNPJ do Filial de Recebimento:</b> {{ $fiscal->cnpj_fornecedor }}</p>
<p><b>Valor total da NF:</b> {{ $fiscal->valor_nf }}</p>
<p><b>Motivo da operação:</b> {{ $fiscal->motivo_operacao }}</p>
<p><b>Informações adicionais:</b> {{ $fiscal->informacoes_adicionais }}</p>
<p><b>Filial:</b> {{ $fiscal->filial }}</p>

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
