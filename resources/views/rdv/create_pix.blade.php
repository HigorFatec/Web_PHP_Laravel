@extends('layout')
@section('title','Comprovante Pix')
@section('conteudo')

<div class="row">
    <div class="col s12 m6 offset-m3">

        @if (@auth()->user()->id != null)

        <div class="card">
            <div class="card-content">
                <span class="card-title center"><b>Anexo de Comprovante Pix</b></span>

<form action="{{ route('despesa.store_pix') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="relatorio_id" value="{{ $relatorio->id }}">
    <input type="hidden" name="valor" value="{{ $valorPix }}">

    <div class="card">
        <div class="card-header">Anexar Comprovante de PIX</div>
        <div class="card-body">
            <p><strong>Relatório:</strong> #{{ $relatorio->id }}</p>
            <p><strong>Valor da Devolução:</strong> R$ {{ number_format($valorPix, 2, ',', '.') }}</p>
            
            <div class="form-group">
                <label>Selecione a Foto/PDF do Comprovante:</label>
                <input type="file" name="foto" class="form-control" required>
            </div>
        </div>
        <div class="card-footer center"><br><br>
            <button type="submit" class="btn btn-success">Enviar Comprovante</button>
        </div>
    </div>
</form>


        </div>
    </div>

    @else
<script>
    alert('Você precisa estar logado para acessar essa página!');
    window.location.href = '/login';
</script>
@endif

        </div>
    </div>
@endsection