<!DOCTYPE html>
<html>
<head>
    <title>Nova Transferência de Veiculo Solicitada</title>
</head>
<body>
    <p>Nome: {{ $dados['name'] }}</p>
    <p>Data: {{ \Carbon\Carbon::parse($dados['data'])->format('d/m/Y') }}</p>
    <p>E-mail: {{ $dados['email'] }}</p>
    <p>E-mail do responsável por receber o veículo: {{ $dados['email_responsavel'] }}</p>
    <p>Placa do Veículo: {{$dados['placa']}}</p>
    <p>Placa da Carreta: {{$dados['placa_carreta']}}</p>
    <p>Placa da Carreta (2): {{$dados['placa_carreta_2']}}</p>
    <p>Placa da Carreta (3): {{$dados['placa_carreta_3']}}</p>
    <p>Unidade de Negócio Origem: {{$dados['filial_origem']}}</p>
    <p>Unidade de Negócio Destino: {{$dados['filial_destino']}}</p>
    <p>Centro de Custo: {{$dados['centro_custo']}}</p>
    <p>Centro de Gasto: {{$dados['centro_gasto']}}</p>
    <p>Previsão de Chegada no Destino: {{\Carbon\Carbon::parse($dados['previsao_chegada'])->format('d/m/Y')}}</p>
    <p>Numeração dos Pneus: {{$dados['conferencia_pneus']}}</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
