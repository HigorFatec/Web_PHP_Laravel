<!DOCTYPE html>
<html>
<head>
    <title>Novo Relatório Realizada</title>
</head>
<body>

    <p><center><h4>Solicitação Revisada e Aprovada com Sucesso pelo Gestor <b>{{$relatorio->unidadeAprovadora->nome_gestor}}</b> </h4></center></p><br>

        <p><b><center><h4>Relatório de Reembolso</h4></center></b></p><br>
                
        <p>ID do Lançamento <b>(Contas a Pagar - RODOPAR)</b>: <b>{{ $relatorio->id_raz }}</b></p><br><br>

    <p style="font-family: sans-serif; font-size: 16px; color: #333;">
        Os anexos das despesas aprovadas foram compactados e estão prontos para download.
    </p>

    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="border-radius: 4px;" bgcolor="#0056b3">
                            <a href="{{ $zipUrl }}" target="_blank" style="padding: 12px 24px; border: 1px solid #0056b3; border-radius: 4px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-decoration: none; font-weight: bold; display: inline-block;">
                                📎 Baixar Anexos (ZIP)
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="font-family: sans-serif; font-size: 12px; color: #777; margin-top: 15px;">
        Arquivo: <code style="background: #eee; padding: 2px 4px;">{{ $zipName }}</code>
    </p>


    <div class="card">
        <h4>Resumo da Solicitação</h4>
        <p><b>Título:</b> {{ $relatorio->favorecido }}</p>
        <p><b>Funcionário:</b> {{ $relatorio->despesas->first()?->descricao_fornecedor }}</p>
        <p><b>Valor Total:</b> <span style="color: #2e7d32;">R$ {{ number_format($relatorio->valor, 2, ',', '.') }}</span></p>
        <p><b>Motivo:</b> {{ $relatorio->referencia }}</p>
        <p><b>Pix:</b> {{$relatorio->pix}} </p>
    </div>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
