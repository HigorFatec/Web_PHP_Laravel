<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Filial;
use Illuminate\Support\Str;


class AuthMicrosoftController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('microsoft')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback()
    {
        $microsoftUser = Socialite::driver('microsoft')->user();

        if (!str_ends_with($microsoftUser->getEmail(), '@grupocargopolo.com.br')) {
            abort(403, 'Acesso não permitido');
        }

        $user = User::where('email', $microsoftUser->getEmail())->first();

        if (!$user) {
            session([
                'ms_user' => [
                    'email' => $microsoftUser->getEmail(),
                    'name'  => $microsoftUser->getName(),
                    'ms_id' => $microsoftUser->getId(),
                ]
            ]);

            return redirect('/completar-cadastro');
        }

        Auth::login($user);
        return redirect('/');
    }

    public function completarCadastro()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');

        if (!session()->has('ms_user')) {
            return redirect('/login');
        }

        return view('auth.completar-cadastro', compact('filiais'));
    }

    public function salvarCadastro(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'cpf' => 'required',
            'filial' => 'required',
            'email_gestor' => 'required|email'
            
        ]);

        $ms = session('ms_user');

        $user = User::create([
            'name' => $request->name,
            'email' => $ms['email'],
            'cpf' => $request->cpf,
            'filial' => $request->filial,
            'email_gestor' => $request->email_gestor,
            'password' => bcrypt(Str::random(32)),
            'microsoft_id' => $ms['ms_id'],
        ]);

        session()->forget('ms_user');

        Auth::login($user);
        return redirect('/');
    }
}
