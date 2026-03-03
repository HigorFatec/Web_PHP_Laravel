<!DOCTYPE html>
<html>
<head>
    <title>Novo Relatório Realizada</title>
</head>
<body>

    <p><center><h4>Solicitação Revisada e Aprovada com Sucesso pelo Gestor <b>{{$relatorio->unidadeAprovadora->nome_gestor}}</b> </h4></center></p><br>

        <p><b><center><h4>Relatório de Despesa</h4></center></b></p><br>
                
        <p>ID do Lançamento <b>(Entrada de Materiais - RODOPAR)</b>: <b>{{ $relatorio->id_rodopar }}</b></p><br><br>



    <div class="card">
        <h4>Resumo da Solicitação</h4>
        <p><b>Título:</b> {{ $relatorio->titulo }}</p>
        <p><b>Funcionário:</b> {{ $relatorio->user_name }}</p>
        <p><b>Valor Total:</b> <span style="color: #2e7d32;">R$ {{ number_format($relatorio->valor, 2, ',', '.') }}</span></p>
        <p><b>Motivo:</b> {{ $relatorio->motivo }}</p>
    </div>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
