<?php
namespace App\Http\Controllers;

use App\Models\UserCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function showCreateAdminForm()
{
    // Verifica se quem abriu é nível 2
    $loggedUser = UserCredential::where('matricula', auth()->user()->CODFUN)->first();

    if (!$loggedUser || $loggedUser->admin_level < 2) {
        abort(403, 'Acesso negado.');
    }

        return view('admin.create');
    }


    public function createAdmin(Request $request)
    {
        // Busca o usuário logado com as credenciais no MySQL
        $loggedUser = UserCredential::where('matricula', Auth::user()->CODFUN)->first();

        if (!$loggedUser || $loggedUser->admin_level < 2) {
            abort(403, 'Acesso negado.');
        }

        // Validação
        $request->validate([
            'matricula' => 'required|string|unique:users_credentials,matricula',
            'password' => 'required|string|min:6',
            'admin_level' => 'required|integer|min:1|max:2',
        ]);

        // Cria o novo administrador
        UserCredential::create([
            'matricula' => $request->matricula,
            'is_admin' => true,
            'admin_level' => $request->admin_level,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Novo administrador criado com sucesso!');
    }

}
