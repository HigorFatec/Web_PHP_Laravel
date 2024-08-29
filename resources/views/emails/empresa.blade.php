<!DOCTYPE html>
<html>
<head>
    <title>Nova Empresa Registrada</title>
</head>
<body>
    <p><b>Nome do Solicitante: {{ $dados['nome_remetente'] }}</b></p><br>
    <p><b>E-mail do Rementente: {{ $dados['email_remetente']}}</b></p><br>
    <p>Razao Social: {{ $dados['razao_social'] }}</p>
    <p>Inscricao Estadual: {{ $dados['inscricao_estadual'] }}</p>
    <p>CNPJ: {{ $dados['cnpj'] }}</p>
    <p>Banco: {{ $dados['banco'] }}</p>
    <p>Agencia: {{ $dados['agencia'] }}</p>
    <p>Conta: {{ $dados['conta'] }}</p>
    <p>Tipo de Conta: {{ $dados['tipo_conta'] }}</p>
    <p>Pix: {{ $dados['pix'] }}</p>
    <p>Telefone 1: {{ $dados['telefone_1'] }}</p>
    <p>Telefone 2: {{ $dados['telefone_2'] }}</p>
    <p>Direto com: {{ $dados['direto_com'] }}</p>
    <p>Email: {{ $dados['email'] }}</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>