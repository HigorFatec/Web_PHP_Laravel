<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status da Solicitação</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 100%; }
        h1 { font-size: 24px; margin-bottom: 15px; }
        p { color: #555; font-size: 16px; line-height: 1.5; }
        .sucesso { color: #28a745; }
        .erro { color: #dc3545; }
        .aviso { color: #ffc107; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="card">
        @if($tipo == 'sucesso')
            <h1 class="sucesso">✓ Sucesso</h1>
        @elseif($tipo == 'erro')
            <h1 class="erro">✕ Reprovado</h1>
        @else
            <h1 class="aviso">⚠ Atenção</h1>
        @endif
        
        <p>{{ $mensagem }}</p>
        
        <a href="#" onclick="window.close();" class="btn">Fechar Janela</a>
    </div>
</body>
</html>