@extends('layout')
@section('conteudo')
<div class="container" style="margin-top: 30px;">
    <div class="card">
        <div class="card-content">
            <span class="card-title font-weight-bold" style="color: #263238;">Revisar Solicitação de Ajuste em Lote</span>
            <br>
            <p><b>Lote Identificador:</b> <span class="chip blue lighten-5 blue-text text-darken-4" style="font-weight: bold; border-radius: 4px;">{{ $implantacao->codigo_lote }}</span></p>
            <p><b>Filial Solicitante:</b> {{ $implantacao->descricao_localizacao ?? 'Não Informada' }}</p>
            <p><b>Tipo de Movimentação:</b> {{ $implantacao->movimentacao }}</p>
            <p><b>Observação Geral:</b> <i>"{{ $implantacao->observacao }}"</i></p>
            <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 20px 0;">
            
            <h5>Produtos a serem aprovados neste lote:</h5>
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descrição do Produto</th>
                        <th style="text-align: center;">Posição</th>
                        <th style="text-align: center;">Quantidade</th>
                        <th>Vlr. Médio</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalGeralLote = 0; @endphp
                    {{-- Usando a variável $itens injetada pelo controller --}}
                    @foreach($itens as $item)
                        @php $totalGeralLote += (float)$item->valor_total; @endphp
                        <tr>
                            <td><b>{{ $item->produto }}</b></td>
                            <td>{{ $item->descricao_produto }}</td>
                            <td style="text-align: center;"><span class="chip grey lighten-3" style="border-radius: 4px;">{{ $item->posicao ?? 'N/A' }}</span></td>
                            <td style="text-align: center;">{{ number_format((float)$item->quantidade, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format((float)$item->valor_medio, 2, ',', '.') }}</td>
                            <td><b>R$ {{ number_format((float)$item->valor_total, 2, ',', '.') }}</b></td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #fbf7e7;">
                        <td colspan="5" style="text-align: right; font-weight: bold;">VALOR TOTAL DO LOTE:</td>
                        <td><b style="color: #c62828; font-size: 15px;">R$ {{ number_format($totalGeralLote, 2, ',', '.') }}</b></td>
                    </tr>
                </tbody>
            </table>
            <br><br>
            
            <div class="row">
                <div class="col s12 m9">
                    {{-- 🌟 CORREÇÃO: Removido o <form> e transformado em link nativo GET passando o gatilho 'confirmar_acao' --}}
                    <a href="{{ route('implantacao_saldo.aprovar', ['id' => $implantacao->id, 'confirmar_acao' => 1]) }}" 
                       class="btn green darken-2 waves-effect waves-light font-weight-bold" 
                       style="padding: 0 20px; height: 40px; line-height: 40px;">
                        <i class="material-icons left">check_circle</i> Confirmar Aprovação do Lote Completo
                    </a>
                    
                    {{-- Link nativo GET para reprovar com aviso de confirmação --}}
                    <a href="{{ route('implantacao_saldo.reprovar', $implantacao->id) }}" 
                       class="btn red darken-2 waves-effect waves-light" 
                       style="margin-left: 10px; padding: 0 20px; height: 40px; line-height: 40px;"
                       onclick="return confirm('Deseja realmente REPROVAR este lote completo?')">
                        <i class="material-icons left">cancel</i> Reprovar Lote
                    </a>
                </div>
                
                <div class="col s12 m3 right-align">
                    <a href="{{ route('implantacao_saldo.monitoramento') }}" class="btn-flat grey-text text-darken-2 waves-effect" style="height: 40px; line-height: 40px;">
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection