<p>Protocolo: {{$adiantamento->id}}</p>
<p>Solicitação de Pagamento a Vista Realizado com Sucesso</p>
<p><b>Aguardando Aprovação do Gestor {{$adiantamento->unidadeAprovadora->nome_gestor}}</b></p>
<p>Data de Solicitação:</b> {{$adiantamento->created_at}}</p>
<p>Solicitante:</b> {{$adiantamento->name}} - {{$adiantamento->email}}</p>
<p>Filial:</b> {{$adiantamento->unidades->unidade_negocio}}</p>


    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>