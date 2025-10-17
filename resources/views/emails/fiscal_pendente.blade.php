<p>Olá Gestor <b>{{ $fiscal->gestor->nome }}</b>,</p>

<p>Você tem uma nova solicitação para análise:</p>

@if ($fiscal->tipo == 'descarte')
    <p>Solicitação de Emissão de Nota Fiscal - <b>{{$fiscal->tipo}}</b></p>

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

@elseif ($fiscal->tipo == 'devolucao')
    <p>Solicitação de Emissão de Nota Fiscal - <b>{{$fiscal->tipo}}</b></p>

    <p><b>Finalidade da Compra:</b> {{ $fiscal->finalidade_da_compra }}</p>
    <p><b>Protocolo:</b> {{ $fiscal->id }}</p>
    <p><b>Empresa solicitante:</b> {{ $fiscal->empresa_solicitante }}</p>
    <p><b>CNPJ:</b> {{ $fiscal->cnpj }}</p>
    <p><b>Fornecedor:</b> {{ $fiscal->fornecedor }}</p>
    <p><b>CNPJ do Fornecedor:</b> {{ $fiscal->cnpj_fornecedor }}</p>
    <p><b>Nota Fiscal de origem:</b> {{ $fiscal->nota_fiscal }}</p>
    <p><b>Valor da devolução:</b> {{ $fiscal->valor_devolucao }}</p>
    <p><b>Valor total da NF:</b> {{ $fiscal->valor_nf }}</p>
    <p><b>Mercadoria a ser devolvida:</b> {{ $fiscal->mercadoria_devolvida }}</p>
    <p><b>Motivo da devolução:</b> {{ $fiscal->motivo_operacao }}</p>
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

@elseif ($fiscal->tipo == 'remessa')
    <p>Solicitação de Emissão de Nota Fiscal - <b>{{$fiscal->tipo}}</b></p>
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
@else
<p>Solicitação de Emissão de Nota Fiscal - <b>{{$fiscal->tipo}}</b></p>

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

@endif


<p>Deseja aprovar ou reprovar?</p>

<p>
    <a href="{{ route('fiscal.aprovar', ['token' => $fiscal->approval_token]) }}"
       style="background:green;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
       ✅ Aprovar
    </a>

    <a href="{{ route('fiscal.reprovar', ['token' => $fiscal->approval_token]) }}"
       style="background:red;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
       ❌ Reprovar
    </a>
</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
