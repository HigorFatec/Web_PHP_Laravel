<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 10px; text-align: center; border-bottom: 3px solid #007bff; }
        .content { padding: 20px; }
        .footer { font-size: 12px; color: #777; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
        .button { display: inline-block; padding: 10px 20px; margin: 10px 5px; text-decoration: none; border-radius: 5px; color: white; font-weight: bold; }
        .btn-approve { background-color: #28a745; }
        .btn-reject { background-color: #dc3545; }
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 8px; border-bottom: 1px solid #eee; }
        .label { font-weight: bold; width: 150px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Nova Solicitação de Ajuda de Custo</h2>
            <p><strong>Protocolo: #{{ $ajuda_custo->id }}</strong></p>
        </div>
        
        <div class="content">
            <p>Olá, uma nova solicitação de ajuda de custo foi registrada e aguarda sua aprovação.</p>
            
            <table>
                <tr><td class="label">Solicitante:</td><td>{{ $ajuda_custo->user?->name ?? 'N/A' }}</td></tr>
                <tr><td class="label">Colaborador:</td><td>{{ $ajuda_custo->name }}</td></tr>
                <tr><td class="label">Fornecedor ID:</td><td>{{ $ajuda_custo->fornecedor }}</td></tr>
                <tr><td class="label">CNPJ:</td><td>{{ $ajuda_custo->cnpj }}</td></tr>
                <tr><td class="label">Data Admissão:</td><td> {{$ajuda_custo->data_admissao}} </td></tr>
                <tr><td class="label">Filial:</td><td>{{ $ajuda_custo->unidades?->unidade_negocio ?? 'N/A' }}</td></tr>
                <tr><td class="label">Valor Fixo:</td><td>R$ {{ number_format($ajuda_custo->valor_fixo, 2, ',', '.') }}</td></tr>
                <tr><td class="label">Valor Prop.:</td><td>R$ {{ number_format($ajuda_custo->valor_proporcional, 2, ',', '.') }}</td></tr>
                <tr><td class="label">Observação:</td><td>{{ $ajuda_custo->observacao ?? 'Nenhuma' }}</td></tr>
                <tr><td class="label">Unidade:</td> <td>{{$ajuda_custo->unidades?->unidade_negocio ?? 'N/A'}}</td> </tr>
                <tr><td class="label">Centro de Custo:</td> <td>{{$ajuda_custo->centroCusto?->descri_custo ?? 'N/A'}}</td> </tr>
                <tr><td class="label">Centro de Gasto:</td> <td>{{$ajuda_custo->centroGasto?->descri_gasto ?? 'N/A'}}</td> </tr>
            </table>

            <div style="text-align: center; margin-top: 30px;">
                <p><strong>Deseja aprovar esta solicitação?</strong></p>
                <a href="{{ url('/ajuda-custo/aprovar/'.$ajuda_custo->approval_token) }}" class="button btn-approve">Aprovar Solicitação</a>
                <a href="{{ url('/ajuda-custo/reprovar/'.$ajuda_custo->approval_token) }}" class="button btn-reject">Reprovar Solicitação</a>
            </div>
        </div>

        <div class="footer">
            Este é um e-mail automático enviado pelo sistema de Gestão Cargo Polo.
        </div>
    </div>
</body>
</html>