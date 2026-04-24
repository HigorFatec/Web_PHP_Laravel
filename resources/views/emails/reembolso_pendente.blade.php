<!DOCTYPE html>
<html>
<head>
    <style>
        .button {
            background-color: #26a69a; /* Cor padrão Materialize */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 8px;
            font-weight: bold;
        }
        .container { font-family: sans-serif; color: #333; line-height: 1.6; }
        .card { border: 1px solid #ddd; padding: 20px; border-radius: 10px; background: #f9f9f9; }
    </style>
</head>
<body class="container">
    <h2>Olá, {{ $relatorio->unidadeAprovadora?->nome_gestor }}</h2>
    <p>Uma nova solicitação de reembolso de despesa foi criada e aguarda sua análise detalhada.</p>

    <div class="card">
        <h4>Resumo da Solicitação</h4>
        <p><b>Título:</b> {{ $relatorio->favorecido }}</p>
        <p><b>Funcionário:</b> {{ $relatorio->despesas->first()?->descricao_fornecedor }}</p>
        <p><b>Valor Total:</b> <span style="color: #2e7d32;">R$ {{ number_format($relatorio->valor, 2, ',', '.') }}</span></p>
        <p><b>Motivo:</b> {{ $relatorio->referencia }}</p>
    </div>

    <p style="margin-top: 25px;">
        Para garantir a conformidade, você deve revisar as fotos dos comprovantes antes de aprovar:
    </p>

    <center>
        <a href="{{ route('reembolso.tela_aprovacao', ['token' => $relatorio->approval_token]) }}" class="button">
            🔍 ANALISAR COMPROVANTES E APROVAR
        </a>
    </center>

    <p style="font-size: 12px; color: #777; margin-top: 30px;">
        Este é um e-mail automático enviado pelo Sistema de RDV - <b>Grupo Cargo Polo</b>.
    </p>
</body>
</html>