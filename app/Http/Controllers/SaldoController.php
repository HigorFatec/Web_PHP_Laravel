<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaldoCombustivel;
use App\Models\ValeCard;


class SaldoController extends Controller
{
        public function index()
    {
        $user = auth()?->user();

        if (!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
        }

        //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
        // if (!$user->temSetor(['frotas','admin'])){
        //     abort(403, 'Acesso negado. Você não tem permissão para acessar esta página.');
        // }
        // Pega o último saldo pelo campo data_insercao
        $saldo = SaldoCombustivel::orderBy('data_insercao', 'desc')->first();


        return view('saldo.index', ['saldo' => $saldo]);
    }

    public function valecard()
    {
        $user = auth()?->user();

        if (!$user) {
            return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
        }

        //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
        // if (!$user->temSetor(['frotas','admin'])){
        //     abort(403, 'Acesso negado. Você não tem permissão para acessar esta página.');
        // }

        $saldo = ValeCard::orderBy('data_insercao', 'desc')->first();
        return view('saldo_valecard.index', ['saldo' => $saldo]);
    }
}
