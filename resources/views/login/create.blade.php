@extends('layout')
@section('title', 'CargoPolo - Criar Conta')

@section('conteudo')
<div class="login-wrapper">
    <div class="container">
        <div class="row">
            <div class="col s12 m10 offset-m1 l6 offset-l3">
                
                {{-- Erros de validação --}}
                @if($errors->any())
                    <div class="error-box">
                        <strong><i class="fa-solid fa-triangle-exclamation"></i> Ops! Verifique os campos:</strong>
                        @foreach($errors->all() as $error)
                            <p style="margin: 5px 0 0 20px;">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="card card-login">
                    <div class="card-content">
                        <div class="center-align mb-2">
                            <h4 class="login-title">CADASTRE-SE</h4>
                            <p class="login-subtitle">Preencha os dados para solicitar acesso</p>
                        </div>

                        <form action="{{ route('users.store') }}" method="POST" id="registerForm">
                            @csrf

                            <div class="row">
                                <div class="input-field col s12">
                                    <i class="fa-solid fa-envelope prefix icon-blue"></i>
                                    <input type="email" name="email" id="emailField" class="validate" required>
                                    <label for="emailField">Seu E-mail Profissional</label>
                                </div>

                                <div class="input-field col s12 m6">
                                    <i class="fa-solid fa-user prefix icon-blue"></i>
                                    <input type="text" name="name" id="name" required>
                                    <label for="name">Nome Completo</label>
                                </div>

                                <div class="input-field col s12 m6">
                                    <i class="fa-solid fa-address-card prefix icon-blue"></i>
                                    <input type="text" name="cpf" id="cpf" maxlength="11" required 
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);">
                                    <label for="cpf">CPF (somente números)</label>
                                </div>

                                <div class="input-field col s12">
                                    <i class="fa-solid fa-location-dot prefix icon-blue"></i>
                                    <select name="filial" id="filial" required>
                                        <option value="" disabled selected>Selecione sua Filial</option>
                                        @foreach ($filiais as $filial)
                                            <option value="{{$filial}}">{{$filial}}</option>
                                        @endforeach
                                    </select>
                                    <label>Unidade / Filial</label>
                                </div>

                                <div class="input-field col s12">
                                    <i class="fa-solid fa-user-tie prefix icon-blue"></i>
                                    <input type="email" name="email_gestor" id="email_gestor" required>
                                    <label for="email_gestor">E-mail do Gestor Regional</label>
                                </div>

                                <div class="input-field col s12 m6">
                                    <i class="fa-solid fa-key prefix icon-blue"></i>
                                    <input type="password" name="password" id="password" required>
                                    <label for="password">Senha</label>
                                </div>

                                <div class="input-field col s12 m6">
                                    <i class="fa-solid fa-shield-check prefix icon-blue"></i>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                                    <label for="password_confirmation">Confirmar Senha</label>
                                </div>
                            </div>

                            <div class="row valign-wrapper mt-2">
                                <div class="col s6">
                                    <a href="{{ route('login.form') }}" class="btn-flat btn-back">
                                        <i class="fa-solid fa-arrow-left"></i> Voltar
                                    </a>
                                </div>
                                <div class="col s6 right-align">
                                    <button type="submit" class="btn btn-login shadow-btn">
                                        CADASTRAR <i class="fa-solid fa-user-plus ml-1"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('select');
        M.FormSelect.init(elems);
    });
</script>
@endsection