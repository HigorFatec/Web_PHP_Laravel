<p>Olá Gestor(a) <b>Patricia</b>,</p>

<p>Você tem uma nova solicitação para análise:</p>

<p>Tipo de Produto: <b> {{ $produto->tipo }} </b></p><br>
<p><b>Nome do Solicitante: {{ $produto->nome_remetente }}</b></p>
<p><b>E-mail do Rementente: {{ $produto->email_remetente }}</b></p><br>
<p>Nome do Produto: {{ $produto->nome }}</p>
<p>Ncm do Produto: {{ $produto->ncm }}</p>
<p>CA do Produto: {{ $produto->ca }}</p>
<p>Descrição Curta do Produto: {{ $produto->descricao_curta }}</p>
<p>Filial: {{$produto->filial}} </p>




<p>Deseja aprovar ou reprovar?</p>

<p>
    <a href="{{ route('produto.aprovar', ['token' => $produto->approval_token]) }}"
       style="background:green;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
       ✅ Aprovar
    </a>

    <a href="{{ route('produto.reprovar', ['token' => $produto->approval_token]) }}"
       style="background:red;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
       ❌ Reprovar
    </a>
</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
