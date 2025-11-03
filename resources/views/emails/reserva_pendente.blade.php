<p>Olá Gestor ,<b>{{$reserva->email_gestor}}</b></p><br>

<p>Deseja aprovar ou reprovar?</p>

<p>
    <a href="{{ route('reserva.aprovar', ['token' => $reserva->approval_token]) }}"
       style="background:green;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
       ✅ Aprovar
    </a>

    <a href="{{ route('reserva.reprovar', ['token' => $reserva->approval_token]) }}"
       style="background:red;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
       ❌ Reprovar
    </a>
</p><br>

<p>Você tem uma nova solicitação para análise:</p>

<p>Solicitação de Reserva de Passagem <b>{{$reserva->tipo}}</b></p>

<p><b>Nome do Solicitante: {{ $user->name }}</b></p>
<p><b>E-mail do Solicitante: {{ $user->email }}</b></p>
<p><b>CPF do Solicitante: {{ $user->cpf }}</b></p>
<p><b>Filial: {{$user->filial}}</b></p><br>
<p><b>Dados do Viajante</b></p><br>
<p>Nome Completo: {{ $reserva->nome }}</p>
<p>Filial do Viajante: {{$reserva->filial_viajante}}</p>
<p>CPF: {{ $reserva->cpf }}</p>
<p>RG: {{ $reserva->rg }}</p>
<p>Data de Nascimento: {{ \Carbon\Carbon::parse($reserva->data_nascimento)->format('d/m/Y') }}</p>
<p>Origem: {{ $reserva->origem }}</p>
<p>Destino: {{ $reserva->destino }}</p>
<p>Data de Ida: {{ \Carbon\Carbon::parse($reserva->ida)->format('d/m/Y') }}</p>
<p>Data de Volta: {{ \Carbon\Carbon::parse($reserva->volta)->format('d/m/Y')  }}</p>
<p>Pretenção de Horário para Embarque: {{ $reserva->embarque }}</p>
<p>Motivo: {{ $reserva->motivo }}</p>
<p>Validacao: {{ $reserva->validacao }}</p>
<p>Email_Gestor: {{ $reserva->email_gestor }}</p>
<p>Observações: {{ $reserva->observacoes }}</p>




    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
