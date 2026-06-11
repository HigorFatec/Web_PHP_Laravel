<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333333; line-height: 1.6; margin: 0; padding: 20px; background-color: #f4f6f7; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; background: #ffffff; border: 1px solid #e0e6ed; border-radius: 6px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background-color: #263238; color: #ffffff; padding: 25px 20px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px; }
        .content { padding: 30px 25px; }
        .alert-text { font-size: 15px; color: #455a64; margin-bottom: 25px; }
        .table-info { width: 100%; margin-bottom: 30px; border-collapse: collapse; background: #fafbfc; border-radius: 4px; border: 1px solid #eaedf1; }
        .table-info td { padding: 12px 15px; font-size: 14px; border-bottom: 1px solid #eaedf1; }
        .label { font-weight: bold; color: #37474f; width: 25%; background: #f0f4f8; }
        .table-produtos { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        .table-produtos th { background-color: #37474f; color: #ffffff; font-weight: bold; padding: 12px 10px; border: 1px solid #37474f; text-align: left; }
        .table-produtos td { padding: 10px; border: 1px solid #e0e6ed; vertical-align: middle; }
        .badge-pos { background: #eceff1; color: #37474f; padding: 3px 6px; border-radius: 3px; font-family: monospace; font-weight: bold; }
        .btn-zone { text-align: center; margin-top: 35px; background: #fafbfc; padding: 20px; border-top: 1px solid #e0e6ed; }
        .btn { display: inline-block; padding: 14px 30px; color: #ffffff !important; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn-approve { background-color: #2e7d32; }
        .btn-approve:hover { background-color: #1b5e20; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📦 Solicitação de Ajuste de Estoque</h2>
            <div style="margin-top: 5px; opacity: 0.8; font-size: 14px;">LOTE: <b>{{ $dadosGerais->codigo_lote }}</b></div>
        </div>
        <div class="content">
            <p class="alert-text">Olá, gestor. Uma nova listagem de produtos em lote foi submetida pelo sistema e aguarda a sua conferência e decisão de aprovação.</p>
            
            <table class="table-info">
                <tr>
                    <td class="label">Filial Solicitante:</td>
                    <td><b>{{ $dadosGerais->descricao_localizacao }}</b> (Cód. {{ $dadosGerais->cod_localizacao }})</td>
                </tr>
                <tr>
                    <td class="label">Tipo Movimentação:</td>
                    <td>
                        <span style="font-weight: bold; color: {{ $dadosGerais->movimentacao == 'Entrada' ? '#2e7d32' : '#c62828' }}">
                            {{ $dadosGerais->movimentacao }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Observação Geral:</td>
                    <td><i>"{{ $dadosGerais->observacao }}"</i></td>
                </tr>
            </table>

            <h3 style="color: #263238; border-bottom: 2px solid #cfd8dc; padding-bottom: 8px; margin-bottom: 15px;">Itens Inclusos no Lote</h3>
            <table class="table-produtos">
                <thead>
                    <tr>
                        <th width="80">Código</th>
                        <th>Descrição do Item</th>
                        <th width="80" style="text-align: center;">Posição</th>
                        <th width="60" style="text-align: center;">Qtd</th>
                        <th width="100">Vlr. Médio</th>
                        <th width="110">Vlr. Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalGeralLote = 0; @endphp
                    @foreach($itens as $item)
                        @php $totalGeralLote += (float)$item->valor_total; @endphp
                        <tr>
                            <td><b>{{ $item->produto }}</b></td>
                            <td style="color: #212121;">{{ $item->descricao_produto }}</td>
                            <td style="text-align: center;">
                                <span class="badge-pos">{{ $item->posicao ?? 'N/A' }}</span>
                            </td>
                            <td style="text-align: center; font-weight: bold;">{{ number_format((float)$item->quantidade, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format((float)$item->valor_medio, 2, ',', '.') }}</td>
                            <td><b>R$ {{ number_format((float)$item->valor_total, 2, ',', '.') }}</b></td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #fff9e6; font-size: 14px;">
                        <td colspan="5" style="text-align: right; font-weight: bold; padding: 12px 10px; border-top: 2px solid #b0bec5;">VALOR TOTAL DO INVENTÁRIO:</td>
                        <td style="border-top: 2px solid #b0bec5;"><b style="color: #c62828; font-size: 15px;">R$ {{ number_format($totalGeralLote, 2, ',', '.') }}</b></td>
                    </tr>
                </tbody>
            </table>

            <div class="btn-zone">
                <a href="{{ route('implantacao_saldo.aprovar', $dadosGerais->id) }}" class="btn btn-approve">
                    🔐 REVISAR E APROVAR LOTE COMPLETO
                </a>
            </div>
        </div>
    </div>
</body>
</html>