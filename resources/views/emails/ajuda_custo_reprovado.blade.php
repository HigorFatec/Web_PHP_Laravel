<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; }
        .header { background-color: #dc3545; color: white; padding: 15px; text-align: center; border-radius: 4px 4px 0 0; }
        .content { padding: 20px; }
        .motivo-box { background-color: #fff3f3; border-left: 5px solid #dc3545; padding: 15px; margin: 15px 0; font-style: italic; }
        .footer { font-size: 12px; color: #777; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Solicitação Reprovada</h2>
        </div>
        <div class="content">
            <p>Olá, <strong>{{ $ajuda_custo->user?->name }}</strong>,</p>
            <p>Informamos que a sua solicitação de ajuda de custo para <strong>{{ $ajuda_custo->name }}</strong> (Protocolo **#{{ $ajuda_custo->id }}**) foi **REPROVADA**.</p>
            
            <p><strong>Motivo apresentado pela gestão:</strong></p>
            <div class="motivo-box">
                "{{ $motivo }}"
            </div>

            <p>Caso necessário, realize os ajustes apontados e efetue uma nova solicitação através do sistema.</p>
        </div>
        <div class="footer">
            Este é um e-mail automático enviado pelo sistema de Gestão Cargo Polo.
        </div>
    </div>
</body>
</html>