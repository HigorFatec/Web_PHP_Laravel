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
        $urlIntended = session()->get('url.intended', '/');

        return Socialite::driver('microsoft')
            ->scopes(['openid', 'profile', 'email'])
            ->with(['state' => 'intended_url=' . $urlIntended])
            ->redirect();
    }

    public function callback(Request $request)
    {
        // 3. Recupera a URL original que a Microsoft devolveu no 'state'
        $state = $request->input('state');
        $redirectTo = '/'; // Fallback padrão

        if ($state) {
            parse_str($state, $result);
            if (isset($result['intended_url'])) {
                $redirectTo = $result['intended_url'];
            }
        }

        $microsoftUser = Socialite::driver('microsoft')->stateless()->user();

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
                ],
                // CASO QUEIRA COMPATIBILIDADE COM USUÁRIOS NOVOS TAMBÉM:
                'url.intended_after_register' => $redirectTo
            ]);

            return redirect('/completar-cadastro');
        }

        Auth::login($user);
        // return redirect('/');
        $request->session()->regenerate();

        // 5. Redireciona para a página que ele tentou acessar originalmente!
        return redirect($redirectTo);
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

        // 1. Recupera a URL que salvamos no callback para usuários novos (ou '/' se não houver)
        $redirectTo = session()->get('url.intended_after_register', '/');

        session()->forget(['ms_user', 'url.intended_after_register']);

        Auth::login($user);

        $request->session()->regenerate();


        return redirect($redirectTo);
    }
}
