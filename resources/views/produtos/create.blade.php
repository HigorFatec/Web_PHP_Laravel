@extends('layout')
@section('title', 'Cadastro de Produtos')
@section('conteudo')

<div class="row">
    <div class="col s12 m6 offset-m3">

        @if ($message = Session::get('success'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Sucesso!</span>
            <p>Parabéns! O produto foi inserido com sucesso!<br>
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
                <span class="card-title center"><b>Cadastro de Produtos</b></span>



    <form method="POST" action="{{ route('produtos.store') }}">
 

                
                <div class="card-title center"> Informações do solicitante da compra </div><br>
        @csrf
        <p class = "preenchimento">

        <label class="form-label" for="nome_remetente">Nome do Solicitante (obrigatório):</label>
        <input type="text" id="nome_remetente" name="nome_remetente" required>
        <br>

        <label class="form-label" for="email_remetente">Email do Solicitante (obrigatório):</label>
        <input type="email" id="email_remetente" name="email_remetente" required>

        <label class="form-label" for="email_aprovador">Insira o e-mail do aprovador (Área técnica para validação do cadastro)</label>
        <input type="email" id="email_aprovador" name="email_aprovador" required>
        </p>


        <div class="card-title center"> Informações do Produto </div>

        <p class="preenchimento">
        <label class="form-label" for="nome">Nome do Produto (obrigatório):</label>
        <input type="text" id="nome" name="nome" required>
        <br>

        <label class="form-label" for="ncm">Ncm do Produto (obrigatório):</label>
        <input type="text" id="ncm" name="ncm" required>
        <br>
        

        <label class="form-label" for="ca">CA do Produto (obrigatório para EPI):</label>
        <input type="text" id="ca" name="ca" >
        <br>

        Selecione o tipo de Produto: <br>
        <select name="tipo" id="tipo" required>

        <option value=" "></option>
        <option value="epi">EPI</option>
        <option value="outro">Outro</option>

        </select> <br>

        </p>

        <br>

       
        <center><button class="btn blue darken-3" type="submit" name="action">Enviar
            <i class="material-icons right">send</i>
          </button></center><br>

          
    </form>


@endsection