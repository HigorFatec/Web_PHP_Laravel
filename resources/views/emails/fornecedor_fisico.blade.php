<!DOCTYPE html>
<html>
<head>
    <title>Novo Registro Incluido</title>
</head>
<body>
    <p><b>Nome do Solicitante: {{ $dados['nome_remetente'] }}</b></p><br>
    <p><b>E-mail do Rementente: {{ $dados['email_remetente']}}</b></p><br>
    <p>Nome Completo: {{ $dados['nome'] }}</p>
    <p>CPF: {{ $dados['cpf'] }}</p>
    <p>RG: {{ $dados['rg'] }}</p>
    <p>Rua: {{ $dados['rua'] }}</p>
    <p>Numero: {{ $dados['numero'] }}</p>
    <p>Bairro: {{ $dados['bairro'] }}</p>
    <p>Cidade: {{ $dados['cidade'] }}</p>
    <p>Estado: {{ $dados['estado'] }}</p>
    <p>CEP: {{ $dados['cep'] }}</p>
    <p>Complemento: {{ $dados['complemento'] }}</p>
    <p>Banco: {{ $dados['banco'] }}</p>
    <p>Agencia: {{ $dados['agencia'] }}</p>
    <p>Conta: {{ $dados['conta'] }}</p>
    <p>Tipo de Conta: {{ $dados['tipo_conta'] }}</p>
    <p>Pix: {{ $dados['pix'] }}</p>
    <p>Telefone Fixo: {{ $dados['telefone_fixo'] }}</p>
    <p>Celular: {{ $dados['celular'] }}</p>
    <p>Email: {{ $dados['email'] }}</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
