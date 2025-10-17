{{-- filepath: resources/views/emails/fiscal_venda.blade.php --}}
<h2>Solicitação de Emissão de Nota Fiscal - VENDA</h2>

<p><b>NOTA FISCAL APROVADA PELO GESTOR RESPONSÁVEL: ({{$fiscal->email_gestor}})</b></p>

<p><b>Tipo de Venda:</b> {{ $fiscal->tipo_de_venda }}</p>
<p><b>Protocolo:</b> {{ $fiscal->id }}</p>
<p><b>Empresa solicitante:</b> {{ $fiscal->empresa_solicitante }}</p>
<p><b>CNPJ:</b> {{ $fiscal->cnpj }}</p>
<p><b>Cliente:</b> {{ $fiscal->cliente }}</p>
<p><b>Código Cliente Rodopar:</b> {{ $fiscal->codigo_cliente_rodopar ?? '-' }}</p>
<p><b>CNPJ do Cliente:</b> {{ $fiscal->cnpj_cliente }}</p>
<p><b>Motivo da operação:</b> {{ $fiscal->motivo_operacao }}</p>
<p><b>Informações adicionais:</b> {{ $fiscal->informacoes_adicionais }}</p>
<p><b>Filial:</b> {{ $fiscal->filial }}</p>
<p><b>Data de Solicitação:</b> {{$fiscal->created_at}}</p>
<p><b>Data de Aprovação:</b> {{$fiscal->updated_at}}</p>

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
