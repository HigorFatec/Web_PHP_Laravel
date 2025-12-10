<p>Protocolo: {{$financeiro->id}}</p>
<p>Solicitação de Pagamento a Vista Realizado com Sucesso</p>
<p><b>Aguardando Aprovação do Gestor {{$financeiro->unidadeAprovadora->nome_gestor}}</b></p>
<p>Data de Solicitação:</b> {{$financeiro->created_at}}</p>
<p>Solicitante:</b> {{$financeiro->name}} - {{$financeiro->email}}</p>
<p>Filial:</b> {{$financeiro->unidades->unidade_negocio}}</p>


    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>