<p>Protocolo: {{$relatorio->id}}</p>
<p>Solicitação de Relatório de Despesa Realizada com Sucesso</p>
<p><b>Aguardando Aprovação do Gestor {{$relatorio->unidadeAprovadora->nome_gestor}}</b></p>
<p>Data de Solicitação:</b> {{$relatorio->created_at}}</p>
<p>Solicitante:</b> {{$relatorio->user_name}} - {{$relatorio->user_email}}</p>
<p>Filial:</b> {{$relatorio->unidades->unidade_negocio}}</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>