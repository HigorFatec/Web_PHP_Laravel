<!DOCTYPE html>
<html>
<head>
    <title>Nova solicitação realizada</title>
</head>
<body>
    <p><center><h4>Solicitação Paga pelo usuário <b>{{$user->name}}</b></h4></center></p><br>

    <p><center><h4>Solicitação Aprovada com Sucesso pelo Gestor <b>{{$financeiro->unidadeAprovadora?->nome_gestor}}</b> </h4></center></p><br>

    <div style="background-color: #f8f9fa; border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center;">
    <h3 style="color: #003366; margin-top: 0;">Comprovante de Pagamento</h3>
    <p>Os documentos foram processados e estão disponíveis para download no link abaixo:</p>
    
    @if(isset($link_comprovante))
        <a href="{{ $link_comprovante }}" 
           style="display: inline-block; padding: 12px 25px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <i class="fa-solid fa-download"></i> BAIXAR COMPROVANTE
        </a>
        <p style="margin-top: 15px; font-size: 11px; color: #888;">
            Link direto: <br>
            <a href="{{ $link_comprovante }}" style="color: #0056b3;">{{ $link_comprovante }}</a>
        </p>
    @else
        <p style="color: #d9534f;">Atenção: Nenhum arquivo foi anexado a este fechamento.</p>
    @endif
    </div>




    @if ($financeiro->tipo == 'reembolso')

        <p><b><center><h4>Reembolso</h4></center></b></p><br>
        
    @else 

        <p><b><center><h4>Adiantamento à fornecedor</h4></center></b></p>
        
    @endif

    @if ($financeiro->tem_nota_fiscal == 'sim')
        <p><b><center><h4>Solicitação com Nota Fiscal</h4></center></b></p><br>
        <p>ID do Lançamento Bancário (RODOPAR): <b>  {{ $financeiro->id_raz }}</b></p><br><br>
    @else
        <p>ID do Lançamento no Contas a Pagar (RODOPAR): Fornecedor: <b> {{$financeiro->fornecedor}}</b> Série: <b>A</b> Documento: <b>{{$financeiro->id}}</b></p><br><br>
    @endif

    <p>Pedido {{ $financeiro->pedido }}</p>
    <p>Descrição: {{ $financeiro->referencia }}</p>
    <p>CPF/CNPJ: {{ $financeiro->cnpj }}</p>
    <p>Fornecedor: {{$financeiro->name}}</p>
    <p>Banco: {{ $financeiro->banco }}</p>
    <p>Agencia: {{ $financeiro->agencia }}</p>
    <p>Conta: {{ $financeiro->conta }}</p>
    <p>Placa: {{ $financeiro->placa }}</p>
    <p>Tipo Chave Pix: {{ $financeiro->tipo_pix }}</p>
    <p>Pix: {{ $financeiro->pix }}</p>
    <p>Observação {{$financeiro->prazo}} </p>
    <p>Favorecido: {{$financeiro->favorecido}}</p>
    <p>Valor: R${{ $financeiro->valor }}</p>
    <p>Unidade: {{ $financeiro->unidades?->unidade_negocio }}</p>
    <p>Centro de Custo: {{$financeiro->centroCusto?->descri_custo}} </p>
    <p>Centro de Gasto: {{$financeiro->centroGasto?->descri_gasto}} </p><br>

    <p>Atenciosamente <b>Grupo Cargo Polo</b></p>
</body>
</html>
