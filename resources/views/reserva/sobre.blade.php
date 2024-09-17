@extends('layout')

@section('title', 'Sobre')
@section('conteudo')

@for ($i = 0; $i < 5; $i++)
    <br>
@endfor

<div class="row">
    <div class="col s12 m6 offset-m3">
      <div class="card red darken-1">
        <div class="card-content white-text">
          <span class="card-title"><b>Sobre</b></span>
          <p>Site criado por Higor Machado, utilizado a linguagem de programação PHP, HTML, CSS e JavaScript com o framework Laravel<br>
            Melhorias ou  sugestões, entrar em contato.
        </p>
        </div>
        <div class="card-action">
            <a href="https://wa.me/5513978090383?text=Ol%C3%A1%20Higor,%20gostaria%20de%20conversar%20sobre%20seu%20projeto." style="color:white">Contato</a>
          <a href="https://github.com/HigorFatec" style="color:white">PortFolio</a>
        </div>
      </div>
    </div>
  </div>

@endsection