<!DOCTYPE html>
<html>
<head>
    <title>Novo Registro Incluido Diretamente no Rodopar</title>
</head>
<body>
@if ($fornecedor->tipo == 'fisico')
    <p>Foi criado um fornecedor do tipo <b> {{$fornecedor->tipo}} </b></p>
    <p><b>Nome do Solicitante: {{ $fornecedor->nome_remetente }}</b></p><br>
    <p><b>E-mail do Rementente: {{ $fornecedor->email_remetente}}</b></p><br>
    <p>Nome Completo: {{ $fornecedor->razao_social }}</p>
    <p>CPF: {{ $fornecedor->cpf }}</p>
    <p>RG: {{ $fornecedor->rg }}</p>
    <p>Endereço: {{ $fornecedor->endereco }}</p>
    <p>E-mail Fornecedor: {{$fornecedor->email_fornecedor}} </p>
    <p>Bairro: {{ $fornecedor->bairro }}</p>
    <p>Cidade: {{ $fornecedor->cidade }}</p>
    <p>Estado: {{ $fornecedor->estado }}</p>
    <p>Banco: {{$fornecedor->banco}} </p>
    <p>Agencia: {{$fornecedor->agencia}} </p>
    <p>Conta: {{$fornecedor->conta}} </p>
    <p>Favorecido: {{$fornecedor->favorecido}} </p>
    <p>Pix Aleatorio: {{$fornecedor->pix_aleatorio}} </p>


@else
    <p>Foi criado um fornecedor do tipo <b> {{$fornecedor->tipo}} </b></p>
    <p><b>Nome do Solicitante: {{ $fornecedor->nome_remetente }}</b></p><br>
    <p><b>E-mail do Rementente: {{ $fornecedor->email_remetente}}</b></p><br>
    <p>Nome Completo: {{ $fornecedor->razao_social }}</p>
    <p>CNPJ: {{ $fornecedor->cnpj }}</p>
    <p>inscricao_estadual: {{ $fornecedor->ie }}</p>
    <p>Endereço: {{ $fornecedor->endereco }}</p>
    <p>E-mail Fornecedor: {{$fornecedor->email_fornecedor}} </p>
    <p>Bairro: {{ $fornecedor->bairro }}</p>
    <p>Cidade: {{ $fornecedor->cidade }}</p>
    <p>Estado: {{ $fornecedor->estado }}</p>
    <p>Banco: {{$fornecedor->banco}} </p>
    <p>Agencia: {{$fornecedor->agencia}} </p>
    <p>Conta: {{$fornecedor->conta}} </p>
    <p>Favorecido: {{$fornecedor->favorecido}} </p>
    <p>Pix Aleatorio: {{$fornecedor->pix_aleatorio}} </p>


@endif


    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
