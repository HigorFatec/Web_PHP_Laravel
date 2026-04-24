<!DOCTYPE html>
<html>
<head>
    <title>Relatório Cancelado</title>
</head>
<body>
    <p>Olá,</p>
    <p>Informamos que o relatório solicitada foi cancelada.</p>
    <p><b>Detalhes do relatório Cancelado:</b></p>
    <p>Nome do Solicitante: {{ $user->name }}</p>
    <p>E-mail do Solicitante: {{ $user->email }}</p>
    <p>CPF do Solicitante: {{ $user->cpf }}</p>
    <p>Valor: {{ $relatorio->valor }}</p>
    <p>Inicio: {{ \Carbon\Carbon::parse($relatorio->inicio_viagem)->format('d/m/Y') }}</p>
    <p>Fim: {{ \Carbon\Carbon::parse($relatorio->fim_viagem )->format('d/m/Y')  }}</p>
    <p>Filial: {{$relatorio->unidades->unidade_negocio}}</p>
    <p>Gestor Aprovador: {{ $relatorio->unidades->nome_gestor }}</p>

    <p>Atenciosamente,</p>
    <p><b>Grupo Cargo Polo</b></p>
</body>
</html>
