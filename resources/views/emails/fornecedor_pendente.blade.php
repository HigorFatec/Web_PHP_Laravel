<!DOCTYPE html>
<html>
<head>
    <title>Novo Registro para ser Incluido Diretamente no Rodopar</title>
</head>
<body>


<p>Deseja aprovar ou reprovar?</p>

<p>
    <a href="{{ route('empresa.aprovar', ['token' => $fornecedor->approval_token]) }}"
       style="background:green;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
       ✅ Aprovar
    </a>

    <a href="{{ route('empresa.reprovar.form', ['token' => $fornecedor->approval_token]) }}"
       style="background:red;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
       ❌ Reprovar
    </a>
</p>


@if ($fornecedor->tipo == 'fisico')
    <p>Foi criado um fornecedor do tipo <b> {{$fornecedor->tipo}} </b></p>
    <p>Status: <b> {{$fornecedor->status}} </b></p><br>
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
    <p>Telefone: {{$fornecedor->telefone_fornecedor}}</p>






@else
    <p>Foi criado um fornecedor do tipo <b> {{$fornecedor->tipo}} </b></p>
    <p>Status: <b> {{$fornecedor->status}} </b></p><br>
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
    <p>Telefone: {{$fornecedor->telefone_fornecedor}}</p>
    



@endif
    <p>Cep: {{$fornecedor->cep}} </p> 


    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
