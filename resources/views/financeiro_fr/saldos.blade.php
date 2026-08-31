@extends('layout')
@section('title', 'Painel de Controle Financeiro')
@section('conteudo')

<style>
    /* DESIGN EXECUTIVO */
    .border-left-blue { border-left: 5px solid #2196f3; }
    .border-left-green { border-left: 5px solid #4caf50; }
    .border-left-orange { border-left: 5px solid #ef6c00; }

    .gasto-valor {
        font-weight: 700; color: #2c3e50; background: #f1f3f4;
        padding: 5px 12px; border-radius: 4px; display: inline-block; font-size: 0.95rem;
    }

    .clean-input, .clean-input-highlight {
        border: 1px solid transparent !important; border-bottom: 1px dashed #ccc !important;
        background-color: transparent !important; height: 2rem !important;
        width: 100% !important; max-width: 150px; font-weight: 600 !important;
        color: #333 !important; transition: all 0.3s ease; margin: 0 !important;
    }

    .clean-input:focus, .clean-input-highlight:focus {
        border: 1px solid #2196f3 !important; background-color: #fff !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important; border-radius: 4px !important; outline: none;
    }

    .clean-input-highlight { color: #e65100 !important; border-bottom: 1px dashed #ef6c00 !important; }
    table th { font-size: 0.75rem !important; text-transform: uppercase; color: #757575; }
    .updated_at { font-family: monospace; font-size: 0.8rem; color: #9e9e9e; }
</style>

<div class="container" style="margin-top: 20px; width: 95%;">
    
    <div class="row">
        <div class="col s12 m8">
            <h4 style="font-weight: 300;">Controle de Saldos & Gestores</h4>
            <p class="grey-text">Gestão estratégica de limites e aprovações.</p>
        </div>
        
        @if(auth()->user()->temSetor(['diretoria']))
        <div class="col s12 m4 right-align" style="margin-top: 25px;">
            <a href="{{ url('/admin/atualizar-saldos') }}" 
               class="btn-large waves-effect waves-light orange darken-4 z-depth-2"
               onclick="return confirm('Deseja efetivar a virada de saldos agora?')">
                <i class="material-icons left">account_balance_wallet</i> Forçar Virada
            </a>
        </div>
        @endif
    </div>

    {{-- Cards --}}
    <div class="row">
        <div class="col s12 m4">
            <div class="card-panel white border-left-blue">
                <span class="grey-text small">REGIONAIS</span>
                <h5>{{ $saldo->count() }} Ativos</h5>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="card-panel white border-left-green">
                <span class="grey-text small">TOTAL PLANEJADO</span>
                <h5 class="green-text">R$ {{ number_format($saldo->sum('novo_saldo'), 2, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="card-panel white border-left-orange">
                <span class="grey-text small">SISTEMA</span>
                <h5><span class="badge blue white-text left" style="margin-left:0; border-radius:4px;">Ciclo Ativo</span></h5>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <ul class="tabs z-depth-1" style="border-radius: 8px;">
                <li class="tab col s4"><a class="active blue-text" href="#aba-saldos">Saldos</a></li>
                <li class="tab col s4"><a class="blue-text" href="#aba-gestores">Gestores</a></li>
                <li class="tab col s4"><a class="blue-text" href="#aba-contas">Contas</a></li>
            </ul>
        </div>

        {{-- ABA 1: SALDOS --}}
        <div id="aba-saldos" class="col s12">
            <div class="card">
                <div class="card-content">
                    <table class="highlight responsive-table">
                        <thead>
                            <tr>
                                <th>Gestor</th>
                                <th class="center">Gasto</th>
                                <th>Saldo Atual</th>
                                <th>Novo Saldo</th>
                                <th>Última Alteração</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($saldo as $s)
                            <tr>
                                <td><b>{{ $s->nome_gestor }}</b></td>
                                <td class="center"><span class="gasto-valor">R$ {{ number_format($s->total_gasto ?? 0, 2, ',', '.') }}</span></td>
                                @if(auth()->user()->temSetor(['diretoria']))
                                <td><input type="number" step="0.01" value="{{$s->saldo}}" class="saldo-input clean-input" data-id="{{$s->id}}"></td>
                                <td><input type="number" step="0.01" value="{{$s->novo_saldo}}" class="novo-saldo-input clean-input-highlight" data-id="{{$s->id}}"></td> 
                                @else
                                <td>{{$s->saldo}}</td>
                                <td>{{$s->novo_saldo}}</td>
                                @endif
                                <td class="updated_at">
                                    {{ $s->updated_at ? $s->updated_at->format('d/m H:i') : '---' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ABA 2: GESTORES --}}
        <div id="aba-gestores" class="col s12">
            <div class="card">
                <div class="card-content">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Unidade</th>
                                <th>Centro de Custo</th>
                                <th>Gestor Responsável</th>
                                <th>E-mail</th>
                                <th>Sincronizado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unidades as $u)
                            <tr>
                                <td><b>{{ $u->unidade_negocio }}</b></td>
                                <td><b>{{ $u->descri_custo }}</b></td>
                                <td><input type="text" class="gestor-nome-input clean-input" data-id="{{ $u->id }}" value="{{ $u->nome_gestor }}"></td>
                                <td><input type="email" class="gestor-email-input clean-input" data-id="{{ $u->id }}" value="{{ $u->email_gestor }}"></td>
                                <td class="updated_at">
                                    {{ $u->updated_at ? $u->updated_at->format('d/m H:i') : '---' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ABA 3: CONTAS --}}
        <div id="aba-contas" class="col s12">
            <div class="card">
                <div class="card-content">
                    <table class="highlight">
                        <thead>
                            <tr>
                                <th>Unidade</th>
                                <th>Conta de Lançamento</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unidades as $c)
                            <tr>
                                <td>{{ $c->unidade_negocio }}</td>
                                <td><input type="text" value="{{$c->conta}}" class="conta-input clean-input" data-id="{{$c->id}}"></td>
                                <td class="updated_at">
                                    {{ $c->updated_at ? $c->updated_at->format('d/m H:i') : '---' }}
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.Tabs.init(document.querySelectorAll('.tabs'));
    });

    function toastSuccess() {
        M.toast({html: '✅ Salvo!', classes: 'green darken-2 rounded', displayLength: 1000});
    }

    document.querySelectorAll('.saldo-input, .novo-saldo-input, .conta-input, .gestor-nome-input, .gestor-email-input').forEach(input => {
        input.addEventListener('change', async (e) => {
            const id = e.target.dataset.id;
            const valor = e.target.value;
            let rota = "";
            let corpo = { id: id };

            // MAPEAMENTO CONFORME SEU CONTROLLER
            if (e.target.classList.contains('saldo-input')) {
                rota = "{{ route('saldo.update') }}";
                corpo.saldo = valor;
            } 
            else if (e.target.classList.contains('novo-saldo-input')) {
                rota = "{{ route('novo-saldo.update') }}";
                corpo.saldo = valor; // Seu controller espera 'saldo' aqui no update_novo_saldo
            } 
            else if (e.target.classList.contains('conta-input')) {
                rota = "{{ route('conta.update') }}";
                corpo.conta = valor;
            }
            else if (e.target.classList.contains('gestor-nome-input')) {
                rota = "{{ route('gestor.update') }}";
                corpo.campo = "nome"; // ESSENCIAL PARA SEU CONTROLLER
                corpo.valor = valor;
            }
            else if (e.target.classList.contains('gestor-email-input')) {
                rota = "{{ route('gestor.update') }}";
                corpo.campo = "email"; // ESSENCIAL PARA SEU CONTROLLER
                corpo.valor = valor;
            }

            try {
                const response = await fetch(rota, {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: JSON.stringify(corpo)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    toastSuccess();
                    e.target.closest('tr').querySelector('.updated_at').innerText = result.atualizado;
                } else {
                    M.toast({html: '❌ ' + (result.message || 'Erro'), classes: 'red'});
                }
            } catch (err) {
                M.toast({html: 'Erro de conexão', classes: 'red'});
            }
        });
    });
</script>
@endsection