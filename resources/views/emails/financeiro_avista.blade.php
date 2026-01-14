<!DOCTYPE html>
<html>
<head>
    <title>Nova solicitação realizada</title>
</head>
<body>

    <p><center><h4>Solicitação Aprovada com Sucesso pelo Gestor <b>{{$financeiro->unidadeAprovadora->nome_gestor}}</b> </h4></center></p><br>

    @if ($financeiro->tipo == 'avista')

        <p><b><center><h4>Pagamento À Vista</h4></center></b></p><br>
        
    @else 

        <p><b><center><h4>Adiantamento à fornecedor</h4></center></b></p>
        
    @endif

    @if ($financeiro->tem_nota_fiscal == 'sim')
        <p><b><center><h4>Solicitação com Nota Fiscal</h4></center></b></p><br>
        <p>ID do Lançamento Bancário (RODOPAR): <b>{{ $financeiro->id_raz }}</b></p><br><br>
    @else
        <p><b><center><h4>Solicitação sem Nota Fiscal</h4></center></b></p><br>
        <p>ID do Lançamento no Contas a Pagar (RODOPAR): Fornecedor: <b>{{$financeiro->fornecedor}}</b> Série: <b>A</b> Documento: <b>{{$financeiro->fornecedor}}-{{$financeiro->id}}</b></p><br><br>
    @endif

    <p>Pedido {{ $financeiro->pedido }}</p>
    <p>Descrição: {{ $financeiro->referencia }}</p>
    <p>CPF/CNPJ: {{ $financeiro->cnpj }}</p>
    <p>Fornecedor: {{$financeiro->name}}</p>
    <p>Banco: {{ $financeiro->banco }}</p>
    <p>Agencia: {{ $financeiro->agencia }}</p>
    <p>Conta: {{ $financeiro->conta }}</p>
    <p>Placa: {{ $financeiro->placa }}</p>
    <p>Tipo Chave Pix: {{ $financeiro->tipo_pix }}</p>
    <p>Pix: {{ $financeiro->pix }}</p>
    <p>Observação {{$financeiro->prazo}} </p>
    <p>Favorecido: {{$financeiro->favorecido}}</p>
    <p>Valor: R${{ $financeiro->valor }}</p>
    <p>Unidade: {{ $financeiro->unidades->unidade_negocio }}</p>
    <p>Centro de Custo: {{$financeiro->centroCusto->descri_custo}} </p>
    <p>Centro de Gasto: {{$financeiro->centroGasto->descri_gasto}} </p><br>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
