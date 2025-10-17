<p>Protocolo: {{$fiscal->id}}</p>
<p>Solicitação de Emissão de NF Realizada com Sucesso</p>
<p><b>Aguardando Aprovação do Gestor {{$fiscal->gestor->nome}}</b></p>
<p>Data de Solicitação:</b> {{$fiscal->created_at}}</p>
<p>Solicitante:</b> {{$fiscal->name}} - {{$fiscal->email}}</p>
<p>Filial:</b> {{$fiscal->filial}}</p>


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