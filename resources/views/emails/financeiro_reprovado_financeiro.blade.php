<h2>Solicitação de Pagamento a Vista - REPROVADA</h2>

<p>O usuário responsável <b>({{ $user->name }})</b> reprovou a solicitação ({{ $financeiro->id }}).</p>

<p>Motivo: {{$financeiro->motivo_reprovacao}}</p>

@if ($user->name == 'MICHEL GUNTHER PLEVKA')
    <p>Sem mais,</p>
@endif

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>