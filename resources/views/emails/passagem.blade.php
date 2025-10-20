<!DOCTYPE html>
<html>
<head>
    <title>Nova Passagem {{$reserva->tipo}} Solicitada</title>
</head>
<body>
    <p><b>Nome do Solicitante: {{ $user->name }}</b></p>
    <p><b>E-mail do Solicitante: {{ $user->email }}</b></p>
    <p><b>CPF do Solicitante: {{ $user->cpf }}</b></p>
    <p><b>Filial: {{$user->filial}}</b></p><br>
    <p><b>Dados do Viajante</b></p><br>
    <p>Nome Completo: {{ $reserva->nome }}</p>
    <p>Filial do Viajante: {{$reserva->filial_viajante}}</p>
    <p>CPF: {{ $reserva->cpf }}</p>
    <p>RG: {{ $reserva->rg }}</p>
    <p>Data de Nascimento: {{ \Carbon\Carbon::parse($reserva->data_nascimento)->format('d/m/Y') }}</p>
    <p>Origem: {{ $reserva->origem }}</p>
    <p>Destino: {{ $reserva->destino }}</p>
    <p>Data de Ida: {{ \Carbon\Carbon::parse($reserva->ida)->format('d/m/Y') }}</p>
    <p>Data de Volta: {{ \Carbon\Carbon::parse($reserva->volta)->format('d/m/Y')  }}</p>
    <p>Pretenção de Horário para Embarque: {{ $reserva->embarque }}</p>
    <p>Motivo: {{ $reserva->motivo }}</p>
    <p>Validacao: {{ $reserva->validacao }}</p>
    <p>Email_Gestor: {{ $reserva->email_gestor }}</p>
    <p>Observações: {{ $reserva->observacoes }}</p>


    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
