<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaldoCombustivel;
use App\Models\ValeCard;


class SaldoController extends Controller
{
        public function index()
    {
        // Pega o último saldo pelo campo data_insercao
        $saldo = SaldoCombustivel::orderBy('data_insercao', 'desc')->first();


        return view('saldo.index', ['saldo' => $saldo]);
    }

    public function valecard()
    {
        $saldo = ValeCard::orderBy('data_insercao', 'desc')->first();
        return view('saldo_valecard.index', ['saldo' => $saldo]);
    }
}
