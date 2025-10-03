<p>Olá Gestor <b>{{ $fiscal->gestor->nome }}</b>,</p>

<p>Você tem uma nova solicitação para análise:</p>

<ul>
    <li><strong>Empresa:</strong> {{ $fiscal->empresa_solicitante }}</li>
    <li><strong>Filial:</strong> {{ $fiscal->filial }}</li>
    <li><strong>Valor NF:</strong> {{ $fiscal->valor_nf }}</li>
    <li><strong>E-mail do Solicitante:</strong> {{ $fiscal->email }}</li>
</ul>

<p>Deseja aprovar ou reprovar?</p>

<p>
    <a href="{{ route('fiscal.aprovar', ['token' => $fiscal->approval_token]) }}"
       style="background:green;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
       ✅ Aprovar
    </a>

    <a href="{{ route('fiscal.reprovar', ['token' => $fiscal->approval_token]) }}"
       style="background:red;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
       ❌ Reprovar
    </a>
</p>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
