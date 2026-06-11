<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333333; line-height: 1.6; margin: 0; padding: 20px; background-color: #f4f6f7; }
        .card { width: 100%; max-width: 850px; margin: 0 auto; background: #ffffff; border: 2px solid #2e7d32; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        .header { background-color: #2e7d32; color: #ffffff; padding: 25px 20px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; font-weight: bold; letter-spacing: 0.5px; }
        .content { padding: 30px 25px; }
        .success-banner { background-color: #e8f5e9; border-left: 5px solid #2e7d32; color: #1b5e20; padding: 15px; margin-bottom: 25px; border-radius: 0 4px 4px 0; font-size: 14px; }
        .table-resumo { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .table-resumo td { padding: 10px 15px; font-size: 14px; border-bottom: 1px solid #eaedf1; }
        .label { font-weight: bold; color: #37474f; width: 25%; background: #f9fbf9; }
        .table-itens { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        .table-itens th { background-color: #455a64; color: #ffffff; font-weight: bold; padding: 12px 10px; border: 1px solid #455a64; text-align: left; }
        .table-itens td { padding: 10px; border: 1px solid #e0e6ed; vertical-align: middle; }
        .badge-pos { background: #f4f6f7; color: #37474f; padding: 3px 6px; border-radius: 3px; font-family: monospace; font-weight: bold; border: 1px solid #d1d5db; }
        .history-box { background: #fafbfc; border: 1px solid #e0e6ed; padding: 15px; border-radius: 6px; margin-top: 30px; }
        .check-icon { color: #2e7d32; font-weight: bold; margin-right: 5px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>✅ PROCESSO DE APROVAÇÃO CONCLUÍDO</h2>
            <div style="margin-top: 5px; opacity: 0.9; font-size: 14px;">Lote Liberado: <b>{{ $dados->codigo_lote }}</b></div>
        </div>
        
        <div class="content">
            <div class="success-banner">
                <strong>Atenção Setor de Emissão de Notas Fiscais / Fiscal:</strong><br>
                Este lote de inventário recebeu todas as assinaturas eletrônicas obrigatórias das alçadas competentes e está <b>100% AUTORIZADO</b> para a devida escrituração e movimentação física no ERP.
            </div>

            <h4 style="color: #37474f; margin: 0 0 10px 0; text-transform: uppercase; font-size: 14px; letter-spacing: 0.5px;">Dados Gerais da Solicitação</h4>
            <table class="table-resumo">
                <tr>
                    <td class="label">Unidade / Filial:</td>
                    <td><b>{{ $dados->descricao_localizacao }}</b> (Código: {{ $dados->cod_localizacao }})</td>
                </tr>
                <tr>
                    <td class="label">Tipo de Movimento:</td>
                    <td><b style="color: {{ $dados->movimentacao == 'Entrada' ? '#2e7d32' : '#c62828' }}">{{ $dados->movimentacao }}</b></td>
                </tr>
                <tr>
                    <td class="label">Colaborador Solicitante:</td>
                    <td>{{ $dados->user->name ?? 'Sistema' }}</td>
                </tr>
                <tr>
                    <td class="label">Justificativa do Lote:</td>
                    <td><i>"{{ $dados->observacao }}"</i></td>
                </tr>
            </table>

            <h4 style="color: #37474f; margin: 25px 0 10px 0; text-transform: uppercase; font-size: 14px; letter-spacing: 0.5px;">Grade de Itens Liberados</h4>
            <table class="table-itens">
                <thead>
                    <tr>
                        <th width="80">Código</th>
                        <th>Descrição do Produto</th>
                        <th width="80" style="text-align: center;">Posição</th>
                        <th width="70" style="text-align: center;">Qtd. Solicitada</th>
                        <th width="70" style="text-align: center;">Saldo Atual</th>
                        <th width="100">Vlr. Médio</th>
                        <th width="110">Vlr. Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        // Como o método do controller passa a referência do primeiro item, buscamos todos do mesmo lote para o fiscal ver a lista inteira
                        $todosItensDoLote = \App\Models\ImplantacaoSaldo::where('codigo_lote', $dados->codigo_lote)->get();
                        $totalLoteFiscal = 0;
                    @endphp
                    @foreach($todosItensDoLote as $item)
                        @php $totalLoteFiscal += (float)$item->valor_total; @endphp
                        <tr>
                            <td><b>{{ $item->produto }}</b></td>
                            <td style="color: #212121;">{{ $item->descricao_produto }}</td>
                            <td style="text-align: center;"><span class="badge-pos">{{ $item->posicao ?? 'N/A' }}</span></td>
                            <td style="text-align: center; font-weight: bold;">{{ number_format((float)$item->quantidade, 2, ',', '.') }}</td>
                            <td style="text-align: center; color: #616161;">{{ number_format((float)$item->saldo_fisico, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format((float)$item->valor_medio, 2, ',', '.') }}</td>
                            <td><b>R$ {{ number_format((float)$item->valor_total, 2, ',', '.') }}</b></td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #f1f8e9; font-size: 14px;">
                        <td colspan="6" style="text-align: right; font-weight: bold; padding: 12px 10px; border-top: 2px solid #2e7d32;">VALOR TOTAL DA OPERAÇÃO:</td>
                        <td style="border-top: 2px solid #2e7d32;"><b style="color: #2e7d32; font-size: 15px;">R$ {{ number_format($totalLoteFiscal, 2, ',', '.') }}</b></td>
                    </tr>
                </tbody>
            </table>

            <div class="history-box">
                <strong style="color: #37474f; font-size: 14px; display: block; margin-bottom: 8px;">Histórico de Assinaturas Digitais:</strong>
                <div style="font-size: 13px; color: #2e7d32; font-weight: 500;">
                    <span class="check-icon">✓</span> Alçada Local (Gestor Filial): Confirmado e Assinado Eletronicamente<br>
                    <span class="check-icon">✓</span> Alçada Regional (Gestor Regional): Confirmado e Assinado Eletronicamente<br>
                    <span class="check-icon">✓</span> Alçada Executiva (Diretoria): Confirmado e Assinado Eletronicamente
                </div>
            </div>
        </div>
    </div>
</body>
</html>