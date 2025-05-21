<!DOCTYPE html>
<html>
<head>
    <title>Nova solicitação realizada</title>
</head>
<body>

    @if ($dados['tipo'] == 'avista')

    <p><b><center><h4>Pagamento À Vista</h4></center></b></p>
        
    @else 


    <p><b><center><h4>Adiantamento à fornecedor</h4></center></b></p>
        
    @endif


    <p>Pedido {{ $dados['pedido'] }}</p>
    <p>Descrição: {{ $dados['referencia'] }}</p>
    <p>CPF/CNPJ: {{ $dados['cnpj'] }}</p>
    <p>Fornecedor: {{$dados['name']}}</p>
    <p>Banco: {{ $dados['banco'] }}</p>
    <p>Agencia: {{ $dados['agencia'] }}</p>
    <p>Conta: {{ $dados['conta'] }}</p>
    <p>Placa: {{ $dados['placa'] }}</p>
    <p>Tipo Chave Pix: {{ $dados['tipo_pix'] }}</p>
    <p>Pix: {{ $dados['pix'] }}</p>
    <p>Observação {{$dados['prazo']}} </p>
    <p>Favorecido: {{$dados['favorecido']}}</p>
    <p>Valor: R${{ $dados['valor'] }}</p>
    <p>Filial: {{ $dados['filial'] }}</p><br>


    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
