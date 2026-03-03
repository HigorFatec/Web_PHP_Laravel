@extends('layout')
@section('title', 'Financeiro')
@section('conteudo')
<div class="row">

@auth    
    @if(auth()->user()->admin >= 0)
    <div class="col s12 m8 offset-m2">
        @else
        <div class="col s12 m6 offset-m3">
            @endif
        @if ($message = Session::get('success'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Saldo alterado com Sucesso!</span>
            <p>O saldo foi alterado com sucesso!
           </p>
          </div>
        </div>
        @endif

        @if ($message = Session::get('success1'))
        <div class="card green darken-1">
          <div class="card-content white-text">
            <span class="card-title">Gestor alterado com Sucesso!</span>
            <p>O gestor da unidade foi alterado com sucesso!
           </p>
          </div>
        </div>
        @endif


        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <div class="card red darken-1">
                    <div class="card-content white-text">
                    <span class="card-title">Erro</span>
                    <p>Corrija os seguintes erros para prosseguir:<br>
                        {{$error}}
                    </p>
                    </div>
                </div>
                @endforeach
            </ul>
        </div>
        @endif


{{-- Saldo Gestores --}}
    @if($saldo->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Ajuste de Saldo - Gestores</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin >= 0)
                    <th>Codigo Unidade</th>
                    <th>Unidade de Negócio</th>
                    <th>Nome do Gestor</th>
                    <th>E-mail Gestor</th>
                    <th>Saldo</th>
                    <th>Ultima Atualização</th>

                    @endif


                </tr>
            </thead>
            <tbody>
                @foreach($saldo as $aprovado)
                    <tr>
                        @if(auth()->user()->admin >= 0)
                        <td><center>{{ $aprovado->cod_unidade }} </center></td>
                        <td>{{ $aprovado->unidade->unidade_negocio }}</td>
                        <td>{{ $aprovado->nome_gestor }}</td>
                        <td>{{ substr($aprovado->email_gestor,0 , 10) }}...</td>
                        @if(auth()->user()->admin == 5 || auth()->user()->admin == 100)
                            <td>R$<input type="number" value="{{$aprovado->saldo}}" class="saldo-input" style="width: 80px; text-align:right;" data-id="{{$aprovado->id}}"></td>
                        @else 
                            <td>R${{ $aprovado->saldo }}</td>
                        @endif
                        <td class="updated_at">
                            {{ \Carbon\Carbon::parse($aprovado->updated_at)->format('d/m/Y H:i:s') }}
                        </td>

                        @endif



                    </tr>
                @endforeach
            </tbody>
        </table>

</div>
</div>

@endif

{{-- FIM --}}


{{-- Filiais --}}
    @if($unidades->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Gestores Aprovadores</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin >= 0)
                    <th>Codigo Unidade</th>
                    <th>Unidade de Negócio</th>
                    <th>Centro de Custo</th>
                    <th>Centro de Gasto</th>
                    <th>Nome do Gestor</th>
                    <th>E-mail Gestor</th>
                    <th>Ultima Atualização</th>

                    @endif


                </tr>
            </thead>
            <tbody>
                @foreach($unidades as $aprovado)
                    <tr>
                        @if(auth()->user()->admin >= 0)
                        <td><center>{{ $aprovado->cod_unidade }} </center></td>
                        <td>{{ $aprovado->unidade_negocio }}</td>
                        <td>{{ $aprovado->descri_custo }}</td>
                        <td>{{ $aprovado->descri_gasto }} </td>
                        @if(auth()->user()->admin == 5 || auth()->user()->admin == 100)

                            <td>
                                <input type="text"
                                    class="gestor-nome-input"
                                    data-id="{{ $aprovado->id }}"
                                    style="width: 150px; padding: 4px; text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase()"
                                    value="{{ $aprovado->nome_gestor }}">
                            </td>

                            <td>
                                <input type="text"
                                    value="{{ $aprovado->email_gestor }}"
                                    class="gestor-email-input"
                                    data-id="{{ $aprovado->id }}"
                                    style="width: 180px; padding: 4px;">
                            </td>
                        @else

                            <td> {{$aprovado->nome_gestor}} </td>
                            <td> {{substr($aprovado->email_gestor,0,13)}}... </td>
                        @endif

                        <td class="updated_at">
                            {{ \Carbon\Carbon::parse($aprovado->updated_at)->format('d/m/Y H:i:s') }}
                        </td>

                        @endif



                    </tr>
                @endforeach
            </tbody>
        </table>

</div>
</div>

@endif

{{-- FIM --}}

{{-- Contas de Pagamento (Lançamento bancário) --}}
    @if($unidades->isEmpty())
        @else
            <div class="card">
                <div class="card-content">
                    <span class="card-title center"><b>Ajuste de Contas - Lançamento</b></span>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    @if(auth()->user()->admin >= 0)
                    <th>Unidade</th>
                    <th>Conta</th>
                    <th>Ultima Atualização</th>

                    @endif


                </tr>
            </thead>
            <tbody>
                @foreach($unidades as $aprovado)
                    <tr>
                        @if(auth()->user()->admin >= 0)
                        <td><center>{{ $aprovado->unidade_negocio }} </center></td>
                        @if(auth()->user()->admin == 5 || auth()->user()->admin == 100)
                            <td><input type="text" value="{{$aprovado->conta}}" class="conta-input" style="width: 80px; text-align:right;" data-id="{{$aprovado->id}}"></td>
                        @else 
                            <td>{{ $aprovado->conta }}</td>
                        @endif
                        <td class="updated_at">
                            {{ \Carbon\Carbon::parse($aprovado->updated_at)->format('d/m/Y H:i:s') }}
                        </td>

                        @endif



                    </tr>
                @endforeach
            </tbody>
        </table>

</div>
</div>

@endif

{{-- FIM --}}




    </div>
</div>

@endauth

<style>
table th,
table td {
    text-align: center !important;
}
</style>

<script>
document.querySelectorAll('.saldo-input').forEach(input => {

    input.addEventListener('change', async (e) => {
        let id = e.target.dataset.id;
        let saldo = e.target.value;

        const response = await fetch("{{ route('saldo.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ id, saldo })
        });

        const result = await response.json();

        if (result.success) {
            // Atualiza a coluna de Última Atualização sem recarregar
            const linha = e.target.closest('tr');
            linha.querySelector('.updated_at').innerText = result.atualizado;
        } else {
            alert("Erro ao atualizar saldo");
        }
    });
});
</script>




<script>
async function atualizarGestor(id, campo, valor, elemento) {

    const response = await fetch("{{ route('gestor.update') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            id: id,
            campo: campo,
            valor: valor
        })
    });

    const result = await response.json();

    console.log("RETORNO:", result); // DEBUG ⚡

    if (result.success) {
        const linha = elemento.closest("tr");
        linha.querySelector(".updated_at").innerText = result.atualizado;
    } else {
        alert("Erro: " + result.message);
    }
}
</script>

<script>
document.querySelectorAll('.gestor-nome-input').forEach(input => {
    input.addEventListener('change', (e) => {
        atualizarGestor(
            e.target.dataset.id,
            "nome",
            e.target.value,
            e.target
        );
    });
});

document.querySelectorAll('.gestor-email-input').forEach(input => {
    input.addEventListener('change', (e) => {
        atualizarGestor(
            e.target.dataset.id,
            "email",
            e.target.value,
            e.target
        );
    });
});
</script>





<script>
document.querySelectorAll('.conta-input').forEach(input => {

    input.addEventListener('change', async (e) => {
        let id = e.target.dataset.id;
        let conta = e.target.value;

        const response = await fetch("{{ route('conta.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ id, conta })
        });

        const result = await response.json();

        if (result.success) {
            // Atualiza a coluna de Última Atualização sem recarregar
            const linha = e.target.closest('tr');
            linha.querySelector('.updated_at').innerText = result.atualizado;
        } else {
            alert("Erro ao atualizar a conta");
        }
    });
});
</script>




@endsection