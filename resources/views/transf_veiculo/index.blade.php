@extends('layout')
@section('title', 'Transferência de Veículo')
@section('conteudo')

<div class="row">
    <div class="col s12 m6 offset-m3">

        @if ($message = Session::get('success'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Sucesso!</span>
            <p>Parabéns! A transferência do veículo foi solicitada com sucesso!<br>
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
                <span class="card-title center"><b>Transferência de Veículo</b></span> <br>
                    <span class="card-title center"><b>Dados do Solicitante</b></span>




<form action="{{route('transf_veiculo.store')}}"method="POST" enctype="multipart/form-data">
    @csrf
    <br>
    Data Da Solicitação: <br> <input type="date" name="data" id="data" required> <br>
    Nome: <br> <input type="text" name="name" required> <br>
    E-mail: <br> <input type="email" name="email" id="email" required>
    {{-- <input type="text" name="cpf" maxlength="11" pattern="\d{11}" title="Digite um CPF com 11 números" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);"> <br> --}}
    <span class="card-title center"><b>Dados do Veículo</b></span>

    Placa do Veículo: <br> <input type="text" name="placa"  maxlength="7" id="placa" required> <br>
    Placa da Carreta: <br> <input type="text" name="placa_carreta"  maxlength="7"  id="placa_carreta" > <br>
    Placa da Carreta (2): <br> <input type="text" name="placa_carreta_2"  maxlength="7"  id="placa_carreta_2" > <br>
    Placa da Carreta (3): <br> <input type="text" name="placa_carreta_3"  maxlength="7"  id="placa_carreta_3" > <br><br>

    <span class="card-title center"><b>Origem</b></span>
    Unidade de Negócio: <br>
    <select name="filial_origem" id="filial_origem" required>

        <option value=" "></option>
        <option value="Corporativo">Corporativo</option>
        @foreach ($filiais as $filial)
            <option value="{{$filial}}">{{$filial}}</option>
        @endforeach


    </select> <br>
    <span class="card-title center"><b>Destino</b></span>

    Unidade de Negócio: <br>
    <select name="filial_destino" id="filial_destino" required>

        <option value=" " ></option>
        <option value="Corporativo">Corporativo</option>
        @foreach ($filiais as $filial)
            <option value="{{$filial}}">{{$filial}}</option>
        @endforeach

    </select> <br>

    Centro De Gasto: <br>
    <select name="centro_custo" id="centro_custo" required>

        <option value=" "></option>
        @foreach ($veiculos as $veiculo)
            <option value="{{$veiculo}}">{{$veiculo}}</option>
        @endforeach

    </select> <br>

    Centro De Custo: <br>
    <select name="centro_gasto" id="centro_gasto" required>

        <option value=" "></option>
        <option value="Diretoria">Diretoria</option>
        @foreach ($gastos as $gasto)
            <option value="{{$gasto}}">{{$gasto}}</option>
        @endforeach

    </select> <br>

    Previsão de Chegada no Destino: <br> <input type="date" name="previsao_chegada" id="previsao_chegada" required> <br>

    E-mail do responsável por receber o veículo: <br> 
    <input type="email" name="email_responsavel" id="email_responsavel" required> <br>




    

    </select>
    <span class="card-title center"><b>Anexos</b></span>

    <b><center> Obs: Cada arquivo deve ter no máximo 2MB e ser do tipo imagem. </center></b><br><br>


    Anexar CheckList da Manutenção:<br>
    <input type="file" name="foto" id="foto" accept="image/*" required><br><br>

    Anexar Numeração dos Pneus:<br>
    <input type="file" name="foto_2" id="foto_2" accept="image/*" required><br><br>

    Anexar Certificado do Tacógrafo<br>
    <input type="file" name="foto_3" id="foto_3" accept="image/*" required><br><br>

    Anexar Plano Revisional (relatorio 459 do rodopar) <br>
    <input type="file" name="foto_4" id="foto_4" accept="image/*" required><br><br>

    <p>As numerações dos pneus que constam no Prolog foram conferidas com as do veículo?</p>
    <div class="btn-group" role="group" aria-label="Conferencia Pneus">
        <input type="hidden" name="conferencia_pneus" id="conferencia_pneus" required>
        <button type="button" class="btn" data-value="Sim" onclick="setConferenciaPneus(this)">Sim</button>
        <button type="button" class="btn" data-value="Não" onclick="setConferenciaPneus(this)">Não</button>
    </div>
    <br>

    <button type="submit" class="btn-cadastrar">Enviar</button>

    <a href="{{route('reserva.sobre')}}">
        <button type="button" class="btn-cadastrar right">Sobre</button></a>
    
    <br>

</form>

</div>
</div>
</div>
</div>

@endsection