<!DOCTYPE html>
<html>
<head>
    <title>Hospedagem Cancelada</title>
</head>
<body>
    <p>Olá,</p>
    <p>Informamos que a hospedagem solicitada foi cancelada.</p>
    <p><b>Detalhes da Hospedagem Cancelada:</b></p>
    <p>Nome do Solicitante: {{ $user->name }}</p>
    <p>E-mail do Solicitante: {{ $user->email }}</p>
    <p>CPF do Solicitante: {{ $user->cpf }}</p>
    <p><b>Filial: {{$user->filial}}</b></p><br>
    <p><b>Dados do Viajante</b></p><br>
    <p>Nome Completo: {{ $hospedagem->nome }}</p>
    <p>CPF: {{ $hospedagem->cpf }}</p>
    <p>RG: {{ $hospedagem->rg }}</p>
    <p>Data de Nascimento: {{ $hospedagem->data_nascimento }}</p>
    <p>Destino: {{ $hospedagem->destino }}</p>
    <p>Data de Ida: {{ $hospedagem->ida }}</p>
    <p>Data de Volta: {{ $hospedagem->volta }}</p>
    <p>Motivo: {{ $hospedagem->motivo }}</p>
    <p>Validacao: {{ $hospedagem->validacao }}</p>
    <p>Email_Gestor: {{ $hospedagem->email_gestor }}</p>
    <p>Observações: {{ $hospedagem->observacoes }}</p>

    <p>Atenciosamente,</p>
    <p><b>Grupo Cargo Polo</b></p>
</body>
</html>
