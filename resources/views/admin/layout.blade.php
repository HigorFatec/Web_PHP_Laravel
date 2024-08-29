<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo')</title>   
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Custom CSS-->
    <link rel="stylesheet" href="{{asset('css/style.css')}}">

    <style>
        .container-filtrar {
            display: flex; /* Usa flexbox para alinhar os botões horizontalmente */
            flex-direction: column;
            align-items: center; /* Centraliza os botões verticalmente */
            justify-content: center; /* Centraliza os botões horizontalmente */
            gap: 10px; /* Espaçamento entre os botões */
        }
        
        .btn-filtrar {
            background-color: #ff0000; /* Cor de fundo do botão */
            color: white; /* Cor do texto */
            padding: 10px 20px; /* Espaçamento interno */
            border: none; /* Remover bordas */
            border-radius: 10px; /* Bordas arredondadas */
            cursor: pointer; /* Cursor de mãozinha ao passar o mouse */
            text-align: center; /* Centralizar texto */
            text-decoration: none; /* Remover sublinhado */
            display: inline-block; /* Mostrar como bloco inline */
            font-size: 16px; /* Tamanho da fonte */
        }

        .btn-filtrar:hover {
            background-color: #0056b3; /* Cor de fundo ao passar o mouse */
        }

        .btn-group .btn.active {
            background-color: #ff0000; /* Cor de destaque para o botão ativo */
            color: white;
        }


        #foraDoPrazoTable {
            display: none; /* Inicialmente oculta a tabela */
            margin-top: 20px;
        }
        #foraDoPrazoTable {
            margin-top: 20px;
        }

        #foraDoPrazoTable h2 {
            text-align: center;
            color: #2c3e50;
            font-family: Arial, sans-serif;
        }

        #foraDoPrazoTable table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            margin-top: 10px;
        }

        #foraDoPrazoTable thead {
            background-color: #3498db;
            color: #fff;
        }

        #foraDoPrazoTable th, #foraDoPrazoTable td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        #foraDoPrazoTable tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #foraDoPrazoTable tr:hover {
            background-color: #ddd;
        }

        #foraDoPrazoTable td[colspan="8"] {
            text-align: center;
        }
    </style>
    
</head>
<body>
     
    
      



    
    @yield('conteudo')

    <!-- Inclua jQuery antes do seu script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.4/js/dataTables.min.js"></script>


    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="{{asset('js/chart.js')}}" ></script>
    <script src="{{asset('js/main.js')}}"></script>
    @stack('graficos')


</body>
</html>