<p>Protocolo: {{$reserva->id}}</p>
<p>Solicitação de Reserva Realizada com Sucesso</p>
<p><b>Aguardando Aprovação do Gestor {{$reserva->email_gestor}}</b></p>
<p>Data de Solicitação:</b> {{$reserva->created_at}}</p>
<p>Solicitante:</b> {{$reserva->name}} - {{$reserva->email}}</p>
<p>Filial:</b> {{$reserva->filial}}</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>