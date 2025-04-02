@extends('pagamento_pix.layout')
@section('title', 'Pagamento Pix')
@section('conteudo'),



<div class="row">
    <div class="col s12 m6 offset-m3">

        @if ($message = Session::get('success'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Sucesso!</span>
            <p>Parabéns! A Transferência Pix foi solicitada com sucesso!<br>
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
                <span class="card-title center"><b>Dados do Abastecimento</b></span>



<form action="{{route('pagamento_pix.store')}}"method="POST" enctype="multipart/form-data">
    @csrf
    <br>
    E-mail: <br> <input type="email" name="email" id="email" required><br>
    E-mail do Gestor: <br> <input type="email" name="email_gestor" id="email_gestor" required>
    Data/Hora: <br> <input type="text" name="data" id="data" required> <br>
    Número do Cupom: <br> <input type="text" name="cupom" id="cupom" required> <br>
    Placa:  <br> <input type="text" name="placa"  maxlength="7" id="placa" required> <br>
    KM do veiculo: <br> <input type="number" name="km" id="km" required> <br>
    CPF: <br> <input type="number" name="cpf" id="cpf" maxlength="11" pattern="\d{11}"  oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" required> <br>
    Nome completo do motorista: <br><input type="text" name="name" id="name" required> <br>
    CNPJ do posto: <br><input type="number" name="cnpj" id="cnpj" maxlength="14" pattern="\d{14}"  oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 14);" required><br>
    Nome do posto: <br><input type="text" name="posto" id="posto" required><br>

    Filial: <br>
    <select name="filial" id="filial" required>

        <option value=" "></option>
        @foreach ($filiais as $filial)
            <option value="{{$filial}}">{{$filial}}</option>
        @endforeach


    </select> <br>

    Produto: <br>
    <select name="produto" id="produto" required>

        <option value=" "></option>
        @foreach ($produtos as $produto)
            <option value="{{$produto}}">{{$produto}}</option>
        @endforeach


    </select> <br>


    Litragem: <br><input type="number" name="litragem" id="litragem" required><br>
    Valor: <br><input type="text" name="valor" id="valor"required> <br>

    Chave Pix: <br><input type="text" name="pix" id="pix" required>
    Valor: <br><input type="text" name="valor_3" id="valor_3" required><br><br><br>

    Anexar Nota Fiscal: <br>
    <input type="file" name="foto" id="foto" accept="image/*" ><br><br>



    <button type="submit" class="btn-cadastrar">Enviar</button>

    <a href="{{route('reserva.sobre')}}">
        <button type="button" class="btn-cadastrar right">Sobre</button></a>
    
    <br>

</form>


@endsection