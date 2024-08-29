@extends('layout')
@section('title', 'Reserva - Solicitações')
@section('conteudo')

<br>

<div class="row">
    <div class="col s12 m6 offset-m3">

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
                <span class="card-title center"><b>Cadastrar</b></span>



<form action="{{route('users.store')}}"method="POST">
    @csrf
    E-mail: <br> <input type="email" name="email" id="emailField"> <br>
    Nome: <br> <input type="text" name="name"> <br>
    CPF: <br> 
    <input type="text" name="cpf" maxlength="11" pattern="\d{11}" title="Digite um CPF com 11 números" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);"> <br>
    
    Filial:<br>
    <select name="filial" id="filial">

        <option value=""></option>
        @foreach ($filiais as $filial)
            <option value="{{$filial}}">{{$filial}}</option>
        @endforeach


    </select> <br>
    

    Senha: <br> <input type="password" name="password"> <br>
    Confirme a senha: <br> <input type="password" name="password_confirmation"> <br>

<button type="submit" class="btn-cadastrar right">Cadastrar</button>

<a href="{{route('login.form')}}">
    <button type="button" class="btn-cadastrar left">Voltar</button></a><br>

</form>

</div>
</div>
</div>
</div>

<script>
    document.getElementById('emailField').addEventListener('click', function() {
        alert('Insira um e-mail válido. Certifique-se que você tenha acesso ao e-mail inserido.');
    });
    document.querySelector('form').addEventListener('submit', function(e) {
    var password = document.getElementById('password').value;
    var passwordConfirmation = document.getElementById('password_confirmation').value;

    if (password !== passwordConfirmation) {
        e.preventDefault(); // Impede o envio do formulário
        alert('As senhas não coincidem. Por favor, verifique e tente novamente.');
    }
});

</script>

@endsection