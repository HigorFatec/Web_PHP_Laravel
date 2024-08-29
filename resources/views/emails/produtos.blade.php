<!DOCTYPE html>
<html>
<head>
    <title>Novo Produto Registrado</title>
</head>
<body>
    <p><b>Nome do Solicitante: {{ $dados['nome_remetente'] }}</b></p>
    <p><b>E-mail do Rementente: {{ $dados['email_remetente']}}</b></p><br>
    <p>Nome do Produto: {{ $dados['nome'] }}</p>
    <p>Ncm do Produto: {{ $dados['ncm'] }}</p>
    <p>CA do Produto: {{ $dados['ca'] }}</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>