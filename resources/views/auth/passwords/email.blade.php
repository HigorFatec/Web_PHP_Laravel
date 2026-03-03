@extends('layout')
@section('title', 'Recuperar Acesso - CargoPolo')

@section('conteudo')
<div class="login-wrapper">
    <div class="container">
        <div class="row">
            <div class="col s12 m8 offset-m2 l4 offset-l4">

                {{-- Status do Envio (Laravel default) --}}
                @if (session('status'))
                    <div class="card-panel blue lighten-4 blue-text text-darken-4" style="border-radius: 8px;">
                        <i class="fa-solid fa-paper-plane"></i> {{ session('status') }}
                    </div>
                @endif

                {{-- Mensagens de Erro --}}
                @if($message = Session::get('erro'))
                    <div class="card-panel red lighten-4 red-text text-darken-4" style="border-radius: 8px;">
                        <i class="fa-solid fa-circle-xmark"></i> {{$message}}
                    </div>
                @endif

                <div class="card card-login">
                    <div class="card-content">
                        <div class="center-align mb-2">
                            <i class="fa-solid fa-unlock-keyhole fa-3x icon-blue" style="margin-bottom: 15px;"></i>
                            <h4 class="login-title" style="font-size: 1.5rem;">RECUPERAR</h4>
                            <p class="login-subtitle">Enviaremos um link de redefinição para o seu e-mail.</p>
                        </div>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            
                            <div class="input-field">
                                <i class="fa-solid fa-envelope prefix icon-blue"></i>
                                <input type="email" name="email" id="email" class="validate" required>
                                <label for="email">E-mail Cadastrado</label>
                            </div>

                            <div class="center-align mt-2">
                                <button type="submit" class="btn btn-login shadow-btn full-width">
                                    ENVIAR LINK <i class="fa-solid fa-chevron-right ml-1"></i>
                                </button>
                            </div>

                            <div class="center-align" style="margin-top: 25px;">
                                <a href="{{ route('login.form') }}" class="btn-back">
                                    <i class="fa-solid fa-arrow-left"></i> Voltar ao Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection