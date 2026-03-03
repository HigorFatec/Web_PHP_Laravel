<!DOCTYPE html>
<html>
<head>
    <title>Nova Hospedagem Solicitada</title>
</head>
<body>
    <p><b>Nome do Solicitante: {{ $user->name }}</b></p>
    <p><b>E-mail do Solicitante: {{ $user->email }}</b></p>
    <p><b>CPF do Solicitante: {{ $user->cpf }}</b></p>
    <p><b>Filial: {{$user->filial}}</b></p><br>
    <p><b>Dados do Viajante</b></p><br>
    <p>Nome Completo: {{ $hospedagem->nome }}</p>
    <p>CPF: {{ $hospedagem->cpf }}</p>
    <p>RG: {{ $hospedagem->rg }}</p>
    <p>Filial do Viajante: {{$hospedagem->filial_viajante}}</p>
    <p>Data de Nascimento: {{ \Carbon\Carbon::parse($hospedagem->data_nascimento)->format('d/m/Y') }}</p>
    <p>Destino: {{ $hospedagem->destino }}</p>
    <p>Data de Check-In: {{ \Carbon\Carbon::parse($hospedagem->ida)->format('d/m/Y') }}</p>
    <p>Data de Check-Out: {{ \Carbon\Carbon::parse($hospedagem->volta)->format('d/m/Y')  }}</p>
    <p>Motivo: {{ $hospedagem->motivo }}</p>
    <p>Validacao: {{ $hospedagem->validacao }}</p>
    <p>Email_Gestor: {{ $hospedagem->email_gestor }}</p>
    <p>Observações: {{ $hospedagem->observacoes }}</p>


    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
