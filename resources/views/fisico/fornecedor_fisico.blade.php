@extends('layout')
@section('title', 'Fornecedor Pessoa Física')
@section('conteudo')


<div class="row">
    <div class="col s12 m6 offset-m3">

        @if ($message = Session::get('success'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Sucesso!</span>
            <p>Parabéns! O Cadastro foi solicitado com sucesso!<br>
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
                <span class="card-title center"><b>Cadastro de Fornecedor Físico</b></span>

    <form action="{{ route('fornecedor_fisico.store') }}" method="POST">
        

                <h3><center><b>Cadastro do Fornecedor Físico</b></center></h3>

                <h4><center>Informações do solicitante da compra</center></h4>
        @csrf
        <p class = "preenchimento">

        <label class="form-label" for="nome_remetente">Nome do Solicitante da compra(obrigatório):</label>
        <input type="text" id="nome_remetente" name="nome_remetente" required>
        <br>

        <label class="form-label" for="email_remetente">Email do Solicitante da compra(obrigatório):</label>
        <input type="email" id="email_remetente" name="email_remetente" required>
        </p>

            <h4><center>Informações do Fornecedor</center></h4>


        <p class="preenchimento">

        <label class="form-label" for="nome">Nome Completo:</label>
        <input type="text" id="nome" name="nome" >
        <br>

        <label class="form-label" for="cpf">CPF:</label>
        <input type="number" id="cpf" name="cpf" >
        <br>

        <label class="form-label" for="rg">RG:</label>
        <input type="number" id="rg" name="rg" >
        <br>

        <label class="form-label" for="data_nascimento">Data de Nascimento:</label>
        <input type="date" id="data_nascimento" name="data_nascimento" >
        <br>

        </p>

        <h4><center>Endereço</center></h4>

        <p class="preenchimento">
        <label class="form-label" for="rua">Rua:</label>
        <input type="text" id="rua" name="rua" >
        <br>

        <label class="form-label" for="numero">Número:</label>
        <input type="number" id="numero" name="numero" >
        <br>

        <label class="form-label" for="bairro">Bairro:</label>
        <input type="text" id="bairro" name="bairro" >
        <br>

        <label class="form-label" for="cidade">Cidade:</label>
        <input type="text" id="cidade" name="cidade" >
        <br>

        <label class="form-label" for="estado">Estado:</label>
        <input type="text" id="estado" name="estado" >
        <br>

        <label class="form-label" for="cep">CEP:</label>
        <input type="number" id="cep" name="cep" >
        <br>

        <label for="complemento" class="form-label">Complemento:</label>
        <input type="text" id="complemento" name="complemento" >
        <br>

        <h4><center>Dados Bancários</center></h4>

        <p class="preenchimento">
        <label class="form-label" for="banco">Banco:</label>
        <input type="text" id="banco" name="banco" >
        <br>

        <label class="form-label" for="agencia">Agencia:</label>
        <input type="number" id="agencia" name="agencia" >
        <br>

        <label class="form-label" for="conta">Conta:</label>
        <input type="number" id="conta" name="conta" >
        <br>

        <label class="form-label" for="tipo_conta">Tipo de Conta:</label>
        <input type="text" id="tipo_conta" name="tipo_conta" >
        <br>

        <label class="form-label" for="pix">Pix:</label>
        <input type="text" id="pix" name="pix" >
        <br>

        </p>

        <h4><center>Contatos</center></h4>
        <p class="preenchimento">

        <label class="form-label" for="telefone_fixo">Telefone Fixo:</label>
        <input type="number" id="telefone_fixo" name="telefone_fixo" >
        <br>

        <label class="form-label" for="celular">Celular:</label>
        <input type="number" id="celular" name="celular" >
        <br>
        
        <label class="form-label" for="email">E-mail para cotação:</label>
        <input type="email" id="email" name="email" >
        <br>

        </p>

        <center><button class="btn" type="submit" name="action">Enviar
            <i class="material-icons right">send</i>
          </button></center><br>

          
    </form>
</body>
</html>

@endsection