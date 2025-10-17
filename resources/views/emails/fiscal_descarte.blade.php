{{-- filepath: resources/views/emails/fiscal_descarte.blade.php --}}
<h2>Solicitação de Emissão de Nota Fiscal - DESCARTE</h2>

<p><b>NOTA FISCAL APROVADA PELO GESTOR RESPONSÁVEL: ({{$fiscal->email_gestor}})</b></p>


<p><b>Protocolo:</b> {{ $fiscal->id }}</p>
<p><b>Empresa solicitante:</b> {{ $fiscal->empresa_solicitante }}</p>
<p><b>CNPJ:</b> {{ $fiscal->cnpj }}</p>
<p><b>Fornecedor:</b> {{ $fiscal->fornecedor }}</p>
<p><b>Código Fornecedor Rodopar:</b> {{ $fiscal->codigo_fornecedor_rodopar }}</p>
<p><b>CNPJ do Fornecedor:</b> {{ $fiscal->cnpj_fornecedor }}</p>
<p><b>Valor total da NF:</b> {{ $fiscal->valor_nf }}</p>
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
