<!DOCTYPE html>
<html>
<head>
    <title>Novo Sinistro Solicitado</title>
</head>

<body>
    <p>Nome: {{ $dados['name'] }}</p>
    <p>Data: {{ \Carbon\Carbon::parse($dados['data'])->format('d/m/Y') }}</p>
    <p>Hora: {{ \Carbon\Carbon::parse($dados['hora'])->format('H:i') }}</p>
    <p>Telefone: {{ $dados['telefone'] }}</p>
    <p>E-mail: {{ $dados['email'] }}</p>
    <p>Placa do Veículo: {{$dados['placa']}}</p>
    <p>Unidade: {{$dados['filial_origem']}}</p>

    <p><h4>Ocorrido...</h4></p>
    <p><b>De acordo com o solicitante... </b>{{$dados['ocorrido']}}</p>


    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
