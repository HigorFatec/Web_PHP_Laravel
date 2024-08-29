<!DOCTYPE html>
<html>
<head>
    <title>Cadastro fornecedor</title>
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


        <style>
        form{
            font-family:Arial, Helvetica, sans-serif;
        }
        .nav-wrapper {
        background-color: #0015ff !important;
        }
        .nav-content {
            background-color: #0051ff !important;
        }
        .preenchimento {
            
            margin-left: 150px;
            margin-right: 150px;
            border:2px solid black;

        }
        .form-label{
            font-size: 20px;
            color:black;
        }
        .btn{
            width: 220px;
            height: 50px;
            background-color: #0015ff;
            font-size: 32px;
        }


        </style>
</head>
<body>
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('fornecedor_fisico.store') }}" method="POST">

                <!-- Compiled and minified JavaScript -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
 

   
                <nav class="nav-extended">
                    <div class="nav-wrapper">
                    <a href="#" class="brand-logo center"><b>Formulário - Grupo Cargo Polo</b></a>
                    <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                    <ul id="nav-mobile" class="right hide-on-med-and-down">
        
                        <li><a href="/sobre"><i class="fas fa-info-circle"></i></a></li>

                    </ul>
                    </div>
                    <div class="nav-content">
                    <ul class="tabs tabs-transparent">
                        <li class="tab"><a class="active" href="/">Home</a></li>
                        <li class="tab"><a class="fornecedor" href="fornecedor_juridico">Fornecedor P.J.</a></li>
                        <li class="tab"><a class="fornecedor_fisico" href="fornecedor_fisico">Fornecedor Pessoa Física</a></li>
                            </ul>
                    </div>
                </nav>

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
        
        <label class="form-label" for="email">E-mail:</label>
        <input type="email" id="email" name="email" >
        <br>

        </p>

        <center><button class="btn" type="submit" name="action">Enviar
            <i class="material-icons right">send</i>
          </button></center><br>

          
    </form>
</body>
</html>
