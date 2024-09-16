<!DOCTYPE html>
<html>
<head>
    <title>Reserva de Adiantamento Cancelada</title>
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
    <p>Nome Completo: {{ $adiantamento->nome }}</p>
    <p>CPF: {{ $adiantamento->cpf }}</p>
    <p>RG: {{ $adiantamento->rg }}</p>
    <p>Filial: {{ $adiantamento->filial }}</p>
    <p>Data de Nascimento: {{ \Carbon\Carbon::parse($adiantamento->data_nascimento)->format('d/m/Y') }}</p>
    <p>Origem: {{ $adiantamento->origem }}</p>
    <p>Destino: {{ $adiantamento->destino }}</p>

    <p>Data de Ida: {{ \Carbon\Carbon::parse($adiantamento->ida)->format('d/m/Y') }}</p>
    <p>Data de Volta: {{ \Carbon\Carbon::parse($adiantamento->volta )->format('d/m/Y')  }}</p>

    <p>Motivo: {{ $adiantamento->motivo }}</p>
    <p>Validacao: {{ $adiantamento->validacao }}</p>
    <p>Email_Gestor: {{ $adiantamento->email_gestor }}</p>
    <p>Observações: {{ $adiantamento->observacoes }}</p>

    <p>Atenciosamente,</p>
    <p><b>Grupo Cargo Polo</b></p>
</body>
</html>
