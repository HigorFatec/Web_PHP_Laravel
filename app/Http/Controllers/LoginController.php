<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Filial;

class LoginController extends Controller
{
    

    public function auth(Request $request) {
        $credenciais = $request->validate([
            'email' => [
                'required', 
                'email', 
                'regex:/^.+@grupocargopolo\.com\.br$/i' // Valida o domínio
            ],
            'password' => ['required'],
        ], [
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'O campo email deve ser um email válido',
            'email.regex' => 'Apenas e-mails do @grupocargopolo.com.br são permitidos',
            'password.required' => 'O campo senha é obrigatório',
        ]);

        if (Auth::attempt($credenciais, $request->remember)) {
            $request->session()->regenerate();
            
            // Se houver uma página interceptada na sessão, vai para ela.
            // Se o usuário entrou direto pelo login, vai para a Home '/'.
            return redirect()->intended('/');
        } else {
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
