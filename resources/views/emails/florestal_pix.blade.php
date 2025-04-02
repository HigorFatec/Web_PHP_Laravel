<!DOCTYPE html>
<html>
<head>
    <title>Novo Pagamento Pix Florestal solicitado</title>
</head>
<body>
    <p>Data/Hora {{ $dados['data'] }}</p>
    <p>Número do Cupom: {{ $dados['cupom'] }}</p>
    <p>Placa: {{ $dados['placa'] }}</p>
    <p>KM do veiculo: {{$dados['km']}}</p>
    <p>CPF: {{ $dados['cpf'] }}</p>
    <p>Nome completo do motorista: {{ $dados['name'] }}</p>
    <p>CNPJ do posto: {{ $dados['cnpj'] }}</p>
    <p>Nome do posto: {{ $dados['posto'] }}</p>
    <p>Produto: {{$dados['produto']}}</p>
    <p>Litragem: {{ $dados['litragem'] }}</p>
    <p>Valor: R${{ $dados['valor'] }}</p>
    <p>Chave Pix: {{ $dados['pix'] }}</p>
    <p>Valor: {{ $dados['valor_3'] }}</p>
    <p>Filial: {{ $dados['filial'] }}</p>



    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
