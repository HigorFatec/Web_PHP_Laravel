<!DOCTYPE html>
<html>
<head>
    <title>Reserva de Passagem Cancelada</title>
</head>
<body>
    <p>Olá,</p>
    <p>Informamos que a reserva solicitada foi cancelada.</p>
    <p><b>Detalhes da reserva Cancelada:</b></p>
    <p>Nome do Solicitante: {{ $user->name }}</p>
    <p>E-mail do Solicitante: {{ $user->email }}</p>
    <p>CPF do Solicitante: {{ $user->cpf }}</p>
    <p><b>Filial: {{$user->filial}}</b></p><br>
    <p><b>Dados do Viajante</b></p><br>
    <p>Nome Completo: {{ $veiculo->nome }}</p>
    <p>CPF: {{ $veiculo->cpf }}</p>
    <p>RG: {{ $veiculo->rg }}</p>
    <p>Data de Nascimento: {{ $veiculo->data_nascimento }}</p>
    <p>Local de Retirada: {{ $veiculo->origem }}</p>
    <p>Local de Devolução: {{ $veiculo->destino }}</p>
    <p>Data de Ida: {{ $veiculo->ida }}</p>
    <p>Data de Volta: {{ $veiculo->volta }}</p>
    <p>Motivo: {{ $veiculo->motivo }}</p>
    <p>Validacao: {{ $veiculo->validacao }}</p>
    <p>Email_Gestor: {{ $veiculo->email_gestor }}</p>
    <p>Observações: {{ $veiculo->observacoes }}</p>

    <p>Atenciosamente,</p>
    <p><b>Grupo Cargo Polo</b></p>
</body>
</html>
