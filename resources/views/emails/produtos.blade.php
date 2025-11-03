<!DOCTYPE html>
<html>
<head>
    <title>Novo Produto Registrado</title>
</head>
<body>
    <p>Tipo de Produto: <b> {{ $produto->tipo }} </b></p><br>
    <p><b>Nome do Solicitante: {{ $produto->nome_remetente }}</b></p>
    <p><b>E-mail do Rementente: {{ $produto->email_remetente }}</b></p><br>
    <p>Nome do Produto: {{ $produto->nome }}</p>
    <p>Ncm do Produto: {{ $produto->ncm }}</p>
    <p>CA do Produto: {{ $produto->ca }}</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>