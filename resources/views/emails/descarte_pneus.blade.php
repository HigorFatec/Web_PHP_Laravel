<!DOCTYPE html>
<html>
<head>
    <title>Novo Descarte de Pneus Solicitado</title>
</head>
<body>
    <p>Nome: {{ $dados['name'] }}</p>
    <p>E-mail: {{ $dados['email'] }}</p>
    <p>Unidade: {{ $dados['filial_origem'] }}</p>
    <p>Data: {{ $dados['data'] }}</p>
    <p>Hora: {{ $dados['hora'] }}</p>
    <p>Código do Pneu / Nº de Fogo: {{ $dados['cod_pneu'] }}</p>
    <p>Nº do Dot: {{ $dados['n_dot'] }}</p>
    <p>Status Atual do Pneu: {{ $dados['status_pneu'] }}</p>
    <p>Placa: {{ $dados['placa'] }}</p>
    <p>Motivo do Descarte: {{ $dados['motivo_descarte'] }}</p>
    <p>Observações: {{ $dados['observacoes'] }}</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
