<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Produtos</title>
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

    <form method="POST" action="{{ route('produtos.store') }}">

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
                        <li class="tab"><a class="active" href="/produtos">Home</a></li>
        
                    </ul>
                    </div>
                </nav>

                <h3><center><b>Cadastro de Produtos</b></center></h3>
                
                <h4><center>Informações do solicitante da compra</center></h4>
        @csrf
        <p class = "preenchimento">

        <label class="form-label" for="nome_remetente">Nome do Solicitante (obrigatório):</label>
        <input type="text" id="nome_remetente" name="nome_remetente" required>
        <br>

        <label class="form-label" for="email_remetente">Email do Solicitante (obrigatório):</label>
        <input type="email" id="email_remetente" name="email_remetente" required>
        </p>


        <h4><center>Informações do Produto</center></h4>

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


        </p>

        <br>

       
        <center><button class="btn" type="submit" name="action">Enviar
            <i class="material-icons right">send</i>
          </button></center><br>

          
    </form>
</body>
</html>
