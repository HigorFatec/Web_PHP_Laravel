<h2>Solicitação de Pagamento a Vista - REPROVADA</h2>

<p>O gestor responsável <b>({{ $financeiro->unidades->nome_gestor }})</b> reprovou a solicitação ({{ $financeiro->id }}).</p>

@if ($financeiro->unidades->nome_gestor == 'MICHEL GUNTHER PLEVKA')
    <p>Sem mais,</p>
@endif

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>