<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Reprovar Solicitação</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 50px 15px; color: #333; }
        .card { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 4px solid #dc3545; }
        h3 { margin-top: 0; color: #dc3545; }
        text-area { width: 100%; height: 100px; padding: 10px; margin-top: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; resize: none; }
        .btn-confirm { background-color: #dc3545; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; width: 100%; font-weight: bold; margin-top: 15px; }
        .btn-confirm:hover { background-color: #c82333; }
        .error { color: #dc3545; font-size: 14px; margin-top: 5px; }
    </style>
</head>
<body>

<div class="card">
    <h3>Reprovar Solicitação #{{ $ajudaCusto->id }}</h3>
    <p>Colaborador: <strong>{{ $ajudaCusto->name }}</strong></p>
    <p>Valor: <strong>R$ {{ number_format($ajudaCusto->valor_proporcional, 2, ',', '.') }}</strong></p>
    
    <form action="{{ route('ajuda_custo.post_reprovar', $ajudaCusto->approval_token) }}" method="POST">
        @csrf
        <label for="motivo">Informe o motivo da reprovação:</label>
        <textarea name="motivo" id="motivo" placeholder="Ex: Valor incorreto ou falta de comprovante..." required>{{ old('motivo') }}</textarea>
        @error('motivo')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn-confirm">Confirmar Reprovação</button>
    </form>
</div>

</body>
</html>