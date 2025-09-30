@extends('descarte.layout')
@section('title', 'Descarte de Pneus')
@section('conteudo')

<div class="row">
    <div class="col s12 m6 offset-m3">

        @if ($message = Session::get('success'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Sucesso!</span>
            <p>Parabéns! O descarte foi solicitado com sucesso!<br>
           </p>
          </div>
        </div>
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="card red darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Erro</span>
                        <p>{{$error}} <br>
                    </p>
                    </div>
                    </div>

            @endforeach
        @endif

        <div class="card">
            <div class="card-content">




<form id="form-upload" action="{{route('descarte.store')}}"method="POST" enctype="multipart/form-data">
    @csrf

    <br><br>

    <span class="card-title center"><b>Dados do Solicitante</b></span>

    Nome: <br> <input type="text" name="name" required> <br>
    E-mail: <br> <input type="email" name="email" id="email" required> <br>

    Unidade: <br>
    <select name="filial_origem" id="filial_origem" required>

        <option value=" "></option>
        <option value="CDD - HEINEKEN - CACAPAVA">CDD - HEINEKEN - CACAPAVA</option>
        <option value="CDD - HEINEKEN - RIBEIRÃO PRETO">CDD - HEINEKEN - RIBEIRÃO PRETO</option>
        <option value="CDD - HEINEKEN - SAO VICENTE">CDD - HEINEKEN - SAO VICENTE</option>
        <option value="CDD - HEINEKEN - UBERLANDIA">CDD - HEINEKEN - UBERLANDIA</option>
        <option value="CDD - HEINEKEN - VARGINHA">CDD - HEINEKEN - VARGINHA</option>
        <option value="DED - BRACELL - LENCOIS">DED - BRACELL - LENCOIS</option>
        <option value="DED - COCA COLA - MACEIO">DED - COCA COLA - MACEIO</option>
        <option value="DED - EUCATEX - SALTO">DED - EUCATEX - SALTO</option>
        <option value="DED - HEINEKEN - ALEXANIA">DED - HEINEKEN - ALEXANIA</option>
        <option value="DED - HEINEKEN - ITU">DED - HEINEKEN - ITU</option>
        <option value="DED - HEINEKEN - PACATUBA">DED - HEINEKEN - PACATUBA</option>
        <option value="DED - HEINEKEN - PONTA GROSSA">DED - HEINEKEN - PONTA GROSSA</option>
        <option value="DED - HEINEKEN - RECIFE">DED - HEINEKEN - RECIFE</option>
        <option value="DED - MDIAS - RECIFE">DED - MDIAS - RECIFE</option>
        <option value="DED - NACIONAL GAS - RECIFE">DED - NACIONAL GAS - RECIFE</option>
        <option value="DED - ULTRAGAZ - PACATUBA">DED - ULTRAGAZ - PACATUBA</option>
        <option value="DED - ULTRAGAZ - PONTA GROSSA">DED - ULTRAGAZ - PONTA GROSSA</option>
        <option value="DED - ULTRAGAZ - RECIFE">DED - ULTRAGAZ - RECIFE</option>
        <option value="DIS - BIMBO - VALINHOS/BRASILIA">DIS - BIMBO - VALINHOS/BRASILIA</option>
        <option value="DIS - HEINEKEN -  SANTO ANTONIO DE JESUS">DIS - HEINEKEN -  SANTO ANTONIO DE JESUS</option>
        <option value="DIS - HEINEKEN - REC/CAG/JP">DIS - HEINEKEN - REC/CAG/JP</option>
        <option value="DIS - HEINEKEN - SALVADOR">DIS - HEINEKEN - SALVADOR</option>
        <option value="DIS - HEINEKEN - SOBRAL">DIS - HEINEKEN - SOBRAL</option>
        <option value="DIS - HEINEKEN - EUSEBIO">DIS - HEINEKEN - EUSEBIO</option>
        <option value="SPOT - COCA COLA - SIMOES FILHO">SPOT - COCA COLA - SIMOES FILHO</option>
        <option value="SPOT - RIBEIRAO PRETO">SPOT - RIBEIRAO PRETO</option>

    </select> <br>

    Data: <br> <input type="date" name="data" id="data" required> <br>
    Hora: <br> <input type="time" name="hora" id="hora" required> <br><br>

        <span class="card-title center"><b>Dados do Pneu</b></span>

    Codigo do Pneu / Nº de Fogo <br> <input type="text" name="cod_pneu" id="cod_pneu" required> <br>
    Nº do Dot <br> <input type="text" name="n_dot" id="n_dot" required> <br>
    Status Atual do Pneu: <br>
    <select name="status_pneu" id="status_pneu" required>

        <option value=" "></option>
        <option value="Em uso">Em uso</option>
        <option value="Estoque">Estoque</option>
        <option value="Analise">Analise</option>
    </select> <br>

    Placa (Se estiver em uso):<input type="text" name="placa" id="placa" placeholder="Placa"><br>

    Motivo do Descarte: <br>
    <select name="motivo_descarte" id="motivo_descarte" required>
    
            <option value=" "></option>
            <option value="BOLHA INTERNA NO LINER">BOLHA INTERNA NO LINER</option>
            <option value="BOLHA LATERAL">BOLHA LATERAL</option>
            <option value="CORTE /PERFURAÇÕES COM TAMANHO ACIMA DO LIMITE">CORTE /PERFURAÇÕES COM TAMANHO ACIMA DO LIMITE</option>
            <option value="CORTE LATERAL ACIMA LIMITE CONSERTO">CORTE LATERAL ACIMA LIMITE CONSERTO</option>
            <option value="DANO IRREPARÁVEL NO ASSENTAMENTO TALÃO">DANO IRREPARÁVEL NO ASSENTAMENTO TALÃO</option>
            <option value="DESGASTE EXCESSIVO LOCALIZADO NA RODAGEM (FREIO TRAVADO)">DESGASTE EXCESSIVO LOCALIZADO NA RODAGEM (FREIO TRAVADO)</option>
            <option value="DESLOCAMENTO DO PACOTE DE CINTAS DA RODAGEM">DESLOCAMENTO DO PACOTE DE CINTAS DA RODAGEM</option>
            <option value="DESLOCAMENTO ENTRE O PACOTE DE CINTAS DA RODAGEM">DESLOCAMENTO ENTRE O PACOTE DE CINTAS DA RODAGEM</option>
            <option value="DESLOCAMENTO POR INFILTRAÇÃO NA LATERAL">DESLOCAMENTO POR INFILTRAÇÃO NA LATERAL</option>
            <option value="DESLOCAMENTO POR INFILTRAÇÃO NA RODAGEM">DESLOCAMENTO POR INFILTRAÇÃO NA RODAGEM</option>
            <option value="EXCESSO DE DESGASTE DA BANDA DE RODAGEM">EXCESSO DE DESGASTE DA BANDA DE RODAGEM</option>
            <option value="FALHA NO CONSERTO">FALHA NO CONSERTO</option>
            <option value="FINAL DE VIDA UTIL (DOT ANTIGOU / EXCESSO DE RECAPAGENS)">FINAL DE VIDA UTIL (DOT ANTIGOU / EXCESSO DE RECAPAGENS)</option>
            <option value="FORTE ARRANCAMENTO NO OMBRO POR MANOBRA">FORTE ARRANCAMENTO NO OMBRO POR MANOBRA</option>
            <option value="FORTE ROÇAMENTO LATERAL">FORTE ROÇAMENTO LATERAL</option>
            <option value="INFILTRAÇÃO NA REGIÃO DO TALÃO">INFILTRAÇÃO NA REGIÃO DO TALÃO</option>
            <option value="PNEU AVARIADO FOGO (QUEIMOU)">PNEU AVARIADO FOGO (QUEIMOU)</option>
            <option value="PNEU INATIVADO / VENDIDO JUNTO COM O EQUIPAMENTO">PNEU INATIVADO / VENDIDO JUNTO COM O EQUIPAMENTO</option>
            <option value="QUEBRA CARCAÇA POR IMPACTO (RODAGEM)">QUEBRA CARCAÇA POR IMPACTO (RODAGEM)</option>
            <option value="QUEBRA DA ALMA DO TALÃO (CABO DE AÇO)">QUEBRA DA ALMA DO TALÃO (CABO DE AÇO)</option>
            <option value="RACHADURA CIRCUNFERENCIAL DO TALÃO">RACHADURA CIRCUNFERENCIAL DO TALÃO</option>
            <option value="RACHADURA EM ZIPER NO MEIO DA LATERAL">RACHADURA EM "ZIPER" NO MEIO DA LATERAL</option>
            <option value="RODOU VAZIO COM BAIXA PRESSÃO (COM DANO APARENTE)">RODOU VAZIO / COM BAIXA PRESSÃO (COM DANO APARENTE)</option>
            <option value="RODOU VAZIO COM BAIXA PRESSÃO (SEM DANOS APARENTES)">RODOU VAZIO / COM BAIXA PRESSÃO (SEM DANOS APARENTES)</option>
            <option value="ROUBO OU FURTO">ROUBO OU FURTO</option>
            <option value="SEPARAÇÃO ENTRE AS LONAS DA CARCAÇA (DIAGONAL)">SEPARAÇÃO ENTRE AS LONAS DA CARCAÇA (DIAGONAL)</option>
            <option value="TALÃO SUPERAQUECIDO">TALÃO SUPERAQUECIDO</option>
            <option value="VERGÃO / VEIA LATERAL EXCESSIVA">VERGÃO / VEIA LATERAL EXCESSIVA</option>

    </select> <br>

    
    <span class="card-title center"><b>Anexos</b></span>


    <div class="upload-field">
        <label for="foto_n_fogo">Foto do Nº de Fogo:</label>
        <input type="file" name="foto_n_fogo" id="foto_n_fogo" accept="image/*" capture="environment" required>
    </div>

    <div class="upload-field">
        <label for="foto_dot">Foto do Dot:</label>
        <input type="file" name="foto_dot" id="foto_dot" accept="image/*" capture="environment">
    </div>

    <div class="upload-field">
        <label for="foto_descarte">Foto do Motivo de Descarte:</label>
        <input type="file" name="foto_descarte" id="foto_descarte" accept="image/*" capture="environment" required>
    </div>

    <span class="card-title center"><b>Observações</b></span>
    <label for="observacoes">Observações (máx. 255 caracteres):</label><br>
    <textarea name="observacoes" id="observacoes" rows="6" cols="50" maxlength="255" capture="environment" required></textarea><br><br>


    <button right type="submit" class="btn-cadastrar">Enviar</button>

    
    <br>

</form>

</div>
</div>
</div>
</div>


@endsection