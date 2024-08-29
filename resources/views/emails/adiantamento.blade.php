<!DOCTYPE html>
<html>
<head>
    <title>Novo Adiantamento Solicitado</title>
</head>
<body>
    <p><b>Nome do Solicitante: {{ $user->name }}</b></p>
    <p><b>E-mail do Solicitante: {{ $user->email }}</b></p>
    <p><b>CPF do Solicitante: {{ $user->cpf }}</b></p>
    <p><b>Filial: {{$user->filial}}</b></p><br>
    <p><b>Dados do Viajante</b></p><br>
    <p>Nome Completo: {{ $dados['nome'] }}</p>
    <p>CPF: {{ $dados['cpf'] }}</p>
    <p>RG: {{ $dados['rg'] }}</p>
    <p>CPF: {{ $dados['cpf'] }}</p>
    <p>Data de Nascimento: {{ $dados['data_nascimento'] }}</p>
    <p>Destino: {{ $dados['destino'] }}</p>
    <p>Data de Ida: {{ $dados['ida'] }}</p>
    <p>Data de Volta: {{ $dados['volta'] }}</p>
    <p>Motivo: {{ $dados['motivo'] }}</p>
    <p>Validacao: {{ $dados['validacao'] }}</p>
    <p>Email_Gestor: {{ $dados['email_gestor'] }}</p>
    <p>Observações: {{ $dados['observacoes'] }}</p><br>
    <p><b>Dados Bancários</b></p> <br>
    <p>Banco: {{ $dados['banco'] }}</p>
    <p>Agência: {{ $dados['agencia'] }}</p>
    <p>Conta: {{ $dados['conta'] }}</p>
    <p>Tipo de Conta: {{ $dados['tipo_conta'] }}</p>
    <p>Titular da Conta: {{ $dados['titular'] }}</p>
    <p>PIX: {{ $dados['pix']}} </p><br><br>


    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
