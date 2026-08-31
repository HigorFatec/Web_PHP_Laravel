<!DOCTYPE html>
<html>
<head>
    <title>Novo Pagamento Pix Aprovado</title>
</head>
<body>

    <center>

    <p> <h4>Codigo o Abastecimento: <b>{{$pagamentoPix->codaba}}</b></h4> </p>
    <p> <h4>Lançamento Bancário: <b>{{$pagamentoPix->id_raz}}</b></h4> </p>

    </center>
    <p>Gestor Aprovador: {{ $dados['email_gestor'] }}</p>


    <p>Data/Hora {{ $dados['data'] }}</p>
    <p>Número do Cupom: {{ $dados['cupom'] }}</p>
    <p>Placa: {{ $placa?->NUMVEI }}</p>
    <p>KM do veiculo: {{$dados['km']}}</p>
    <p>CPF: {{ $dados['cpf'] }}</p>
    <p>Nome completo do motorista: {{ $dados['name'] }}</p>
    <p>CNPJ do posto: {{ $dados['cnpj_2'] }}</p>
    <p>Nome do posto: {{ $dados['posto'] }}</p>
    <p>Produto: {{$pagamentoPix->produtos?->nome}}</p>
    <p>Litragem: {{ $dados['litragem'] }}</p>
    <p>Valor: R${{ $dados['valor'] }}</p>
    <p>Produto ARLA-32: {{ $pagamentoPix->produtos_arla?->nome }}</p>
    <p>Litragem ARLA-32: {{ $dados['litragem_arla'] }}</p>
    <p>Valor ARLA-32: R${{ $dados['valor_arla'] }}</p>
    <p>Banco: {{$dados['banco']}}</p>
    <p>Agência: {{ $dados['agencia'] }}</p>
    <p>Conta Corrente: {{ $dados['conta'] }}</p>
    <p>CNPJ: {{ $dados['cnpj_2'] }}</p>
    <p>Favorecido: {{ $dados['favorecido'] }}</p>
    <p>Chave Pix: {{ $dados['pix'] }}</p>
    <p>Valor: {{ $dados['valor_3'] }}</p>
    <p>Filial: {{ $dados['filial'] }}</p>



    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
