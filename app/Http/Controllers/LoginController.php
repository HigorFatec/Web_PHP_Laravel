<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Filial;

class LoginController extends Controller
{
    

    public function auth(Request $request){
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ],[
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'O campo email deve ser um email válido',
            'password.required' => 'O campo senha é obrigatório',
            'password.min' => 'O campo senha deve ter no mínimo 8 caracteres',
            'password.confirmed' => 'As senhas não coincidem',

        ]);

        if(Auth::attempt($credenciais, $request->remember)){
            $request->session()->regenerate();
            return redirect()->intended('/reserva');
        } else{
            return redirect()->back()->with('erro', 'Usuário ou senha incorretos');
        }

    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('login.form'))->with('success', 'Deslogado com sucesso');
    }

    public function create(){

        $filiais = Filial::orderBy('filial')->pluck('filial');

        return view('login.create', compact('filiais'));
    }

}
