<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; }
        .header { background-color: #28a745; color: white; padding: 10px; text-align: center; }
        .content { padding: 20px; }
        .info-box { background-color: #f4f4f4; padding: 15px; border-radius: 5px; border-left: 5px solid #28a745; }
        .footer { font-size: 12px; color: #777; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Solicitação Recebida!</h1>
        </div>
        
        <div class="content">
            <p>Olá, <strong> {{$ajuda_custo->user?->name}}</strong>,</p>
            <p>Sua solicitação de ajuda de custo de <strong>{{ $ajuda_custo->name }}</strong> foi registrada com sucesso em nosso sistema e encaminhada para a gestão.</p>
            
            <div class="info-box">
                <p><strong>Protocolo:</strong> #{{ $ajuda_custo->id }}</p>
                <p><strong>Data:</strong> {{ date('d/m/Y H:i') }}</p>
                <p><strong>Status Atual:</strong> Pendente de Aprovação</p>
            </div>

            <p>Você receberá uma nova notificação assim que houver uma atualização no status do seu pedido.</p>
            
            <p>Dados principais informados:</p>
            <ul>
                <li>Unidade: {{ $ajuda_custo->unidades?->unidade_negocio }}</li>
                <li>Centro de Custo: {{ $ajuda_custo->centroCusto?->descri_custo ?? $ajuda_custo->cod_custo }}</li>
                <li>Centro de Gasto: {{ $ajuda_custo->centroGasto?->descri_gasto ?? $ajuda_custo->cod_gasto }}</li>
                <li>Valor Total Estimado: R$ {{ number_format($ajuda_custo->valor_proporcional, 2, ',', '.') }}</li>
            </ul>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Grupo Cargo Polo - Departamento Administrativo
        </div>
    </div>
</body>
</html>