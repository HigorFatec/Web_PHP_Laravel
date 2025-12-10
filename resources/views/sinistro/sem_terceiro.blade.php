@extends('layout')
@section('title', 'Controle de Sinistros')
@section('conteudo')

<div class="row">
    <div class="col s12 m6 offset-m3">

        @if ($message = Session::get('success'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Sucesso!</span>
            <p>Parabéns! O sinistro foi solicitado com sucesso!<br>
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




<form action="{{route('sinistro.store')}}"method="POST" enctype="multipart/form-data">
    @csrf
    <br>
    <input type="hidden" name="tipo" value="{{ request()->routeIs('sinistro.sem_terceiro') ? 'sem_terceiro' : 'com_terceiro' }}">

    <span class="card-title center"><b>Tipo de Sinistro</b></span>

    <div class="btn-group center" role="group" aria-label="Tipo de Reserva">
       <a href="{{route('sinistro.index')}}"> <button type="button" class="btn" onclick="selecionarTipo('com_terceiro')">
            Com Terceiro
        </button></a>
        <a href="{{route('sinistro.sem_terceiro')}}"><button type="button" class="btn" onclick="selecionarTipo('sem_terceiro')">
            Sem Terceiro
        </button></a>
    </div>

    <input type="hidden" name="tipo_sinistro" id="tipo_sinistro">

    <br><br>


                    <span class="card-title center"><b>Dados do Solicitante</b></span>

    Data da Ocorrência: <br> <input type="date" name="data" id="data" required> <br>
    Hora da Ocorrência: <br> <input type="time" name="hora" id="hora" required> <br>
    Nome Solicitante: <br> <input type="text" name="name" required> <br>
    Telefone Solicitante: <br> <input type="text" name="telefone" id="telefone" required> <br>
    E-mail Solicitante: <br> <input type="email" name="email" id="email" required>
    E-mail Acompanhantes (separado por <b> ; </b>): <br> <input type="text" name="email_gestores" id="email_gestores"><br>
    {{-- <input type="text" name="cpf" maxlength="11" pattern="\d{11}" title="Digite um CPF com 11 números" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);"> <br> --}}
    
    <span class="card-title center"><b>Descreva o ocorrido</b></span>
    <label for="ocorrido">Descreva o ocorrido (máx. 500 caracteres):</label><br>
    <textarea name="ocorrido" id="ocorrido" rows="6" cols="50" maxlength="500" required></textarea><br>

    
    <span class="card-title center"><b>Dados do Veículo</b></span>

    Placa do Veículo: <br> <input type="text" name="placa"  maxlength="7" id="placa" required> <br>

    Unidade: <br>
    <select name="filial_origem" id="filial_origem" required>

        <option value=" "></option>
        <option value="Corporativo">Corporativo</option>
        @foreach ($filiais as $filial)
            <option value="{{$filial}}">{{$filial}}</option>
        @endforeach


    </select> <br>



    

    </select>
    <span class="card-title center"><b>Anexos</b></span>


    Boletim de Ocorrência <b>(Obrigatório)</b>:<br>
    <input type="file" name="boletim" id="boletim" accept=".pdf,image/*" required><br><br>

    Documento do veiculo:<br>
    <input type="file" name="foto_2" id="foto_2" accept="image/*"><br><br>

    CNH:<br>
    <input type="file" name="foto_3" id="foto_3" accept="image/*" ><br><br>

    COMPROVANTE DE ENDEREÇO:<br>
    <input type="file" name="foto_4" id="foto_4" accept="image/*" ><br><br>

    Orçamento:<br>
    <input type="file" name="foto_5" id="foto_5" accept="image/*" ><br><br><br>



    <button right type="submit" class="btn-cadastrar">Enviar</button>

    
    <br>

</form>

</div>
</div>
</div>
</div>

@endsection