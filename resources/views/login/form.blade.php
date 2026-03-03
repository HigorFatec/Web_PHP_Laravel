@extends('layout')
@section('title', 'Grupo Cargo Polo - Login')
@section('conteudo')

@auth
<script>window.location = "/";</script>
@else

<div class="row" style="margin-top: 40px;">
    <div class="col s12 m6 offset-m3">

        {{-- Mensagem de logout --}}
        @if ($message = Session::get('success'))
            <div class="card-panel green lighten-4 green-text text-darken-4" style="border-radius: 8px;">
                <i class="fa-solid fa-circle-check"></i> <b>Logout:</b> realizado com sucesso!
            </div>
        @endif

        {{-- Mensagem de erro --}}
        @if ($message = Session::get('erro'))
            <div class="card-panel red lighten-4 red-text text-darken-4" style="border-radius: 8px;">
                <i class="fa-solid fa-circle-xmark"></i> <b>Erro:</b> {{ $message }}
            </div>
        @endif

        {{-- Formulário --}}
        <div class="card card-login">
            <div class="card-content">
                <div class="center-align mb-2">
                    <h4 class="login-title">LOGIN</h4>
                    <p class="login-subtitle">Acesse o portal do Grupo Cargo Polo</p>
                </div>

                {{-- Erros de validação --}}
                @if($errors->any())
                    <div class="card-panel red lighten-2 white-text" style="border-radius: 8px; padding: 10px;">
                        @foreach($errors->all() as $error)
                            <p style="margin: 0;"><i class="fa-solid fa-triangle-exclamation"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login.auth') }}" method="POST">
                    @csrf

                    <div class="input-field">
                        <i class="fa-solid fa-envelope prefix icon-blue"></i>
                        <input type="email" name="email" id="email" required class="validate">
                        <label for="email">E-mail</label>
                    </div>

                    <div class="input-field">
                        <i class="fa-solid fa-lock prefix icon-blue"></i>
                        <input type="password" name="password" id="password" required>
                        <label for="password">Senha</label>
                    </div>

                    <div class="row">
                        <div class="col s6">
                            <label>
                                <input type="checkbox" name="remember"/>
                                <span>Lembrar-me</span>
                            </label>
                        </div>
                        <div class="col s6 right-align">
                            <a href="{{ route('password.request') }}" style="font-size: 0.9rem;">Esqueceu sua senha?</a>
                        </div>
                    </div>

                    {{-- Botão login normal --}}
                    <button type="submit" class="btn btn-login shadow-btn full-width" style="margin-top: 20px;">
                        ENTRAR <i class="fa-solid fa-right-to-bracket ml-1"></i>
                    </button>

                    <div class="divider-text">ou</div>

                    {{-- Botão Microsoft --}}
                    <a href="/auth/microsoft" class="btn btn-microsoft full-width">
                        <img src="https://img.icons8.com/color/24/microsoft.png" alt="Microsoft Logo" />
                        Entrar com Microsoft
                    </a>

                    <div class="center-align" style="margin-top: 25px;">
                        <span class="grey-text">Não tem conta? </span>
                        <a href="{{ route('login.create') }}" class="font-weight-600">Cadastre-se</a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

@endauth

@endsection