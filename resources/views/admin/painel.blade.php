@extends('layout')
@section('title', 'Gerenciar Usuários - CargoPolo')
@section('conteudo')

<div class="login-wrapper" style="padding-top: 40px; background-color: #f4f7fa;"> {{-- Fundo leve para o branco destacar --}}
    <div class="container">
        
        <div class="row valign-wrapper" style="margin-bottom: 20px;">
            <div class="col s12 m8">
                <div style="display: flex; align-items: center;">
                    <i class="fa-solid fa-users-gear" style="font-size: 2.2rem; color: #003366; margin-right: 15px;"></i>
                    <div>
                        <h4 style="color: #003366; font-weight: 800; margin: 0; font-size: 1.8rem; text-transform: uppercase;">Gerenciar Usuários</h4>
                        <p style="color: #333; margin: 2px 0 0; font-weight: 500;">Administração total de acessos e permissões da plataforma</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m4 right-align">
                {{-- Botão Principal Moderno --}}
                <a href="#!" class="btn btn-login shadow-btn full-width-on-small" style="background-color: #003366 !important; height: 45px; line-height: 45px;">
                    <i class="fa-solid fa-user-plus left"></i> NOVO USUÁRIO
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="card card-login" style="background-color: #ffffff !important; border: 1px solid #e0e0e0;">
                    <div class="card-content" style="padding: 20px 30px;">
                        <span class="card-title" style="color: #184693; font-weight: 700; font-size: 1.1rem; display: flex; align-items: center;">
                            <i class="fa-solid fa-filter" style="margin-right: 10px;"></i> Filtros de Busca
                        </span>
                        
                        {{-- Formulário de Filtro --}}
                        <form action="{{ Request::url() }}" method="GET">
                            <div class="row valign-wrapper" style="margin-top: 20px;">
                                <div class="input-field col s12 m5">
                                    <i class="fa-solid fa-search prefix icon-blue"></i>
                                    <input type="text" name="search" id="search_field" placeholder="Nome, usuário ou email">
                                    <label for="search_field">Buscar</label>
                                </div>
                                <div class="input-field col s12 m3">
                                    <select name="status">
                                        <option value="" disabled selected>Todos</option>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                    <label>Status</label>
                                </div>
                                <div class="input-field col s12 m4">
                                    <select name="setor">
                                        <option value="" disabled selected>Todos os Setores</option>
                                        @foreach($setores as $setor)
                                            <option value="{{ $setor->id }}">{{ ucfirst($setor->nome) }}</option>
                                        @endforeach
                                    </select>
                                    <label>Setores</label>
                                </div>
                            </div>
                        <div class="row center-align" style="margin: 0; gap: 10px; display: flex; justify-content: center;">
                                <button type="submit" class="btn waves-effect waves-light shadow-btn" style="background-color: #184693 !important;">
                                    APLICAR FILTROS <i class="fa-solid fa-filter-list right"></i>
                                </button>
                                <a href="{{ Request::url() }}" class="btn-flat waves-effect" style="color: #666; font-weight: 600;">
                                    LIMPAR
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="card card-login" style="background-color: #ffffff !important; border: 1px solid #e0e0e0;">
                    <div class="card-content" style="padding: 30px;">
                        <span class="card-title" style="color: #184693; font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; margin-bottom: 25px;">
                            <i class="fa-solid fa-list-check" style="margin-right: 10px;"></i> Lista de Usuários ({{ $usuarios->count() }})
                        </span>

                        <table class="highlight responsive-table">
                            <thead>
                                <tr style="border-bottom: 2px solid #184693; border-top: 1px solid #eee;">
                                    <th style="color: #184693; font-weight: 800; font-size: 13px;">USUÁRIO / NOME</th>
                                    <th style="color: #184693; font-weight: 800; font-size: 13px;">E-MAIL</th>
                                    <th style="color: #184693; font-weight: 800; font-size: 13px;" class="center-align">STATUS</th>
                                    <th style="color: #184693; font-weight: 800; font-size: 13px;">SETORES (Permissões)</th>
                                    <th style="color: #184693; font-weight: 800; font-size: 13px;" class="center-align">AÇÕES</th>
                                </tr>
                            </thead>

                            <tbody style="color: #000000 !important; font-weight: 500;"> {{-- Forçando preto sólido --}}
                                @foreach($usuarios as $user)
                                <tr id="user-row-{{ $user->id }}" class="{{ $user->ativo ? '' : 'row-inactive' }}">
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <div class="user-icon-circle-filled">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <div style="margin-left: 12px;">
                                                <span style="font-weight: 700; color: #1a1a1a; display: block; font-size: 14px;">{{ $user->name }}</span>
                                                <span style="color: #555; font-size: 12px; font-weight: 600;">{{ $user->username ?? 'username' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td style="color: #444;">{{ $user->email }}</td>

                                    <td class="center-align">
                                        {{-- Badge de Status Moderno --}}
                                        @if($user->ativo)
                                            <span class="badge-status-clean ativo"><i class="fa-solid fa-circle-check"></i> ATIVO</span>
                                        @else
                                            <span class="badge-status-clean inativo"><i class="fa-solid fa-circle-xmark"></i> INATIVO</span>
                                        @endif
                                    </td>

                                    <td style="width: 250px;">
                                        {{-- Seção de Setores (Clique para toggle) --}}
                                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                            @foreach($setores as $setor)
                                                <div class="role-badge-clickable {{ $user->setores->contains($setor->id) ? 'active' : '' }}" 
                                                     onclick="toggleSetor({{ $user->id }}, {{ $setor->id }}, this)">
                                                    {{ ucfirst($setor->nome) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <td class="center-align">
                                        {{-- Ação de Inativar (Substituindo o Switch por algo mais "Administrativo") --}}
                                        <button class="btn-flat waves-effect {{ $user->ativo ? 'red-text' : 'green-text' }}" 
                                                onclick="toggleStatus({{ $user->id }}, this)" 
                                                style="padding: 0 10px; font-weight: 600; text-transform: none;">
                                            {{ $user->ativo ? 'Desativar' : 'Ativar' }}
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* Ajustes Gerais de Contraste */
    table tbody tr td { color: #000000 !important; font-weight: 500; font-size: 13.5px; padding: 15px 5px !important; }
    
    /* Ícone de Usuário Preenchido (Estilo Moderno) */
    .user-icon-circle-filled {
        width: 38px; height: 38px; background: #e0f2f1; color: #00897b; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 14px;
        box-shadow: inset 0 0 0 1.5px rgba(0,0,0,0.05);
    }

    /* Badges de Status Modernos (Sem Fundo Chapado) */
    .badge-status-clean {
        display: inline-flex; align-items: center; gap: 5px; font-weight: 700; font-size: 12px;
        padding: 4px 10px; border-radius: 20px; text-transform: uppercase;
    }
    .badge-status-clean i { font-size: 14px; }
    .badge-status-clean.ativo { color: #2e7d32; border: 1.5px solid #a5d6a7; background: #e8f5e9; }
    .badge-status-clean.inativo { color: #c62828; border: 1.5px solid #ef9a9a; background: #ffebee; }

    /* Badges de Setor Clicáveis (Estilo Tag Profissional Clean) */
    .role-badge-clickable {
        padding: 4px 10px; border-radius: 6px; background: #ffffff;
        font-size: 11px; font-weight: 700; color: #444; cursor: pointer;
        transition: all 0.2s ease; border: 1.5px solid #d0d0d0;
        display: inline-flex; align-items: center;
    }
    .role-badge-clickable.active {
        background: #e3f2fd !important; color: #184693 !important; border-color: #184693 !important;
    }
    .role-badge-clickable:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    /* Estilo para linha Inativa */
    .row-inactive td { opacity: 0.6; color: #777 !important; }
    .row-inactive .badge-status-clean.inativo { opacity: 1; }

    /* Override dos inputs do Materialize para estilo Clean */
    .input-field input:focus { border-bottom: 2px solid #005bb6 !important; box-shadow: 0 1px 0 0 #005bb6 !important; }
    .input-field input:focus + label { color: #005bb6 !important; }
    .select-wrapper input.select-dropdown { color: #000; font-weight: 500; border-bottom: 1px solid #9e9e9e; }
    .select-wrapper input.select-dropdown:focus { border-bottom: 1px solid #005bb6 !important; }

    /* Responsividade */
    @media only screen and (max-width: 600px) { .full-width-on-small { width: 100% !important; margin-top: 10px; } }
</style>

<script>
// Inicializar selects e dropdowns do Materialize
document.addEventListener('DOMContentLoaded', function() {
    var elemsSelect = document.querySelectorAll('select');
    M.FormSelect.init(elemsSelect);
    
    var elemsDropdown = document.querySelectorAll('.dropdown-trigger');
    M.Dropdown.init(elemsDropdown, { coverTrigger: false, constrainWidth: false });
});

// Suas funções AJAX originais (apenas atualizando a ação de Ativar/Desativar)
function toggleSetor(userId, setorId, element) {
    fetch(`/admin/usuarios/${userId}/toggle-setor`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ setor_id: setorId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'added') { element.classList.add('active'); }
        else { element.classList.remove('active'); }
    });
}

function toggleStatus(userId, element) {
    // 1. Localiza a linha e o container do badge de status
    const row = document.getElementById(`user-row-${userId}`);
    const statusContainer = document.getElementById(`status-container-${userId}`);
    
    // 2. Faz a chamada ao servidor
    fetch(`/admin/usuarios/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Garante que o Laravel aceite a requisição
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Falha na requisição');
        return response.json();
    })
    .then(data => {
        // 3. Atualiza a interface baseada no retorno do banco de dados
        if (data.ativo) {
            // Se ficou ATIVO
            row.classList.remove('row-inactive');
            element.textContent = 'Desativar';
            element.className = 'btn-flat waves-effect red-text';
            element.style.fontWeight = '600';
            
            if(statusContainer) {
                statusContainer.innerHTML = '<span class="badge-status-clean ativo"><i class="fa-solid fa-circle-check"></i> ATIVO</span>';
            }
            M.toast({html: 'Usuário Ativado!', classes: 'green rounded'});
        } else {
            // Se ficou INATIVO
            row.classList.add('row-inactive');
            element.textContent = 'Ativar';
            element.className = 'btn-flat waves-effect green-text';
            element.style.fontWeight = '600';
            
            if(statusContainer) {
                statusContainer.innerHTML = '<span class="badge-status-clean inativo"><i class="fa-solid fa-circle-xmark"></i> INATIVO</span>';
            }
            M.toast({html: 'Usuário Desativado!', classes: 'orange rounded'});
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        M.toast({html: 'Erro ao comunicar com o servidor', classes: 'red'});
    });
}
</script>
@endsection