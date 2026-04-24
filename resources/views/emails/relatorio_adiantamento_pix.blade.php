<h2>Olá, {{ $relatorio->user_name }}</h2>
<p>Seu relatório de despesas <strong>#{{ $relatorio->id }}</strong> foi processado.</p>

<div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p><strong>Valor Recebido (Adiantamento):</strong> R$ {{ number_format($adiantamentoTotal, 2, ',', '.') }}</p>
    <p><strong>Valor Gasto (Relatório):</strong> R$ {{ number_format($relatorio->valor, 2, ',', '.') }}</p>
    <h3 style="color: #d9534f;">Valor a Devolver: R$ {{ number_format($valorPix, 2, ',', '.') }}</h3>
</div>

<p>Para concluir este processo, você deve realizar o PIX para o Celular da empresa </p>

<b>Pix: (16) 99614-9013</b>
<b>Confirme os dados:</b>
<b>CARGO POLO COMERCIO, LOGISTICA E TRANSPORTE</b>
<b>Banco ITAU ag 2129 cc 39020-5</b><br>


<p> e anexar o comprovante no link abaixo: </p>

<a href="{{ url('/despesa/pix/'.$relatorio->id.'?valor='.$valorPix) }}" 
   style="background-color: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block;">
    Anexar Comprovante de PIX
</a>

<p style="margin-top: 20px;"><em>*O relatório só será finalizado após a confirmação do comprovante.</em></p><br><br>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
