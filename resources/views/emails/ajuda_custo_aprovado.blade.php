<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; }
        .header { background-color: #28a745; color: white; padding: 15px; text-align: center; border-radius: 4px 4px 0 0; }
        .content { padding: 20px; }
        .footer { font-size: 12px; color: #777; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Solicitação Aprovada!</h2>
        </div>
        <div class="content">
            <p>Olá, <strong>{{ $ajuda_custo->user?->name }}</strong>,</p>
            <p>Sua solicitação de ajuda de custo para <strong>{{ $ajuda_custo->name }}</strong> foi **APROVADA** pela gestão e enviada ao setor financeiro para processamento de pagamento.</p>
            
            <p><strong>Detalhes do Protocolo:</strong> #{{ $ajuda_custo->id }}<br>
            <strong>Valor Aprovado:</strong> R$ {{ number_format($ajuda_custo->valor_proporcional, 2, ',', '.') }}</p>
        </div>
        <div class="footer">
            Este é um e-mail automático enviado pelo sistema de Gestão Cargo Polo.
        </div>
    </div>
</body>
</html>