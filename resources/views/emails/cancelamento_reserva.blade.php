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
    <p>Nome Completo: {{ $reserva->nome }}</p>
    <p>CPF: {{ $reserva->cpf }}</p>
    <p>RG: {{ $reserva->rg }}</p>
    <p>Data de Nascimento: {{ $reserva->data_nascimento }}</p>
    <p>Origem: {{ $reserva->origem }}</p>
    <p>Destino: {{ $reserva->destino }}</p>
    <p>Data de Ida: {{ $reserva->ida }}</p>
    <p>Data de Volta: {{ $reserva->volta }}</p>
    <p>Motivo: {{ $reserva->motivo }}</p>
    <p>Validacao: {{ $reserva->validacao }}</p>
    <p>Email_Gestor: {{ $reserva->email_gestor }}</p>
    <p>Observações: {{ $reserva->observacoes }}</p>

    <p>Atenciosamente,</p>
    <p><b>Grupo Cargo Polo</b></p>
</body>
</html>
