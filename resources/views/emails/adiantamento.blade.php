<!DOCTYPE html>
<html>
<head>
    <title>Nova solicitação realizada</title>

</head>
<body>

    <p><center><h4>Solicitação Aprovada com Sucesso pelo Gestor <b>{{$adiantamento->unidadeAprovadora->nome_gestor}}</b> </h4></center></p><br>


    <p><b><center><h4>Descrição da Solicitação</h4></center></b></p><br>

    <p><b><center><h4>Adiantamento de Viagem</h4></center></b></p><br>
    <p>ID do Lançamento Bancário (ERP RODOPAR): <b>{{ $adiantamento->id_raz }}</b></p><br><br>

    <p>Viajante: {{$adiantamento->nome}} </p>
    <p>CPF: {{$adiantamento->cpf}} </p>
    <p>RG: {{$adiantamento->cpf}} </p>
    <p>Data de Nascimento: {{ \Carbon\Carbon::parse($adiantamento->data_nascimento)->format('d/m/Y') }}</p>

    <p>Motivo da Viagem: {{ $adiantamento->motivo }}</p>
    <p>Observações: {{ $adiantamento->observacoes }}</p>
    <p>Destino: {{ $adiantamento->destino }}</p>
    <p>Data de Ida: {{ \Carbon\Carbon::parse($adiantamento->ida)->format('d/m/Y') }}</p>
    <p>Data de Volta: {{\Carbon\Carbon::parse($adiantamento->volta)->format('d/m/Y')}}</p>

    
    <p>Banco: {{ $adiantamento->banco }}</p>
    <p>Agencia: {{ $adiantamento->agencia }}</p>
    <p>Conta: {{ $adiantamento->conta }}</p>
    <p>Placa: {{ $adiantamento->placa }}</p>
    <p>Tipo Chave Pix: {{ $adiantamento->tipo_pix }}</p>
    <p>Pix: {{ $adiantamento->pix }}</p>
    <p>Favorecido: {{$adiantamento->favorecido}}</p>


    <p>Valor: R${{ $adiantamento->valor }}</p>
    <p>Unidade: {{ $adiantamento->unidades->unidade_negocio }}</p>
    <p>Centro de Custo: {{$adiantamento->centroCusto->descri_custo}} </p>
    <p>Centro de Gasto: {{$adiantamento->centroGasto->descri_gasto}} </p><br>


    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
