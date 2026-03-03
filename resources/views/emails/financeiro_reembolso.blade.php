<!DOCTYPE html>
<html>
<head>
    <title>Nova solicitação realizada</title>
</head>
<body>


    <p><b><center><h4>Reembolso/Despesa</h4></center></b></p>
        

    <p>Protocolo {{ $financeiro->id }}</p>
    <p>CPF/CNPJ: {{ preg_replace('/\D/','',$dados['cnpj']) }}</p>
    <p>Recebedor: {{$dados['name']}}</p>
    <p>Banco: {{ $dados['banco'] }}</p>
    <p>Agencia: {{ $dados['agencia'] }}</p>
    <p>Conta: {{ $dados['conta'] }}</p>
    <p>Tipo Chave Pix: {{ $dados['tipo_pix'] }}</p>
    <p>Pix: {{ preg_replace('/\D/','',$dados['pix']) }}</p>
    <p>Observação {{$dados['prazo']}} </p>
    <p>Favorecido: {{$dados['favorecido']}}</p>
    <p>Valor: R${{ $dados['valor'] }}</p>
    <p>Filial: {{ $financeiro->unidades?->unidade_negocio ?? $dados['filial'] }}</p><br>

    <p><strong>Tipo de Reembolso:</strong></p>

    @if(!empty($tipos))
        <ul>
            @foreach($tipos as $tipo)
                <li>{{ $tipo }}</li>
            @endforeach
        </ul>
    @else
        <p>Nenhum tipo informado.</p>
    @endif




    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
