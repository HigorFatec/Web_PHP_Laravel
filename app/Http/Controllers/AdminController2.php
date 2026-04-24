<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setor;
use Illuminate\Http\Request;

class AdminController2 extends Controller
{
    // Lista todos os usuários e os setores disponíveis
    public function painel(Request $request)
    {
        $query = User::query()->orderBy('created_at', 'desc')->with('setores'); // Carrega os setores para evitar N+1

        // Filtro de Busca (Nome, Username ou Email)
        $query->when($request->search, function ($q, $search) {
            $q->where(function($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('cpf', 'like', "%{$search}%");
            });
        });

        // Filtro de Status
        $query->when($request->status, function ($q, $status) {
            if ($status === 'ativo') {
                $q->where('ativo', 1);
            } elseif ($status === 'inativo') {
                $q->where('ativo', 0);
            }
        });

        // Filtro de Setor (Relacionamento Many-to-Many)
        $query->when($request->setor, function ($q, $setorId) {
            $q->whereHas('setores', function ($sub) use ($setorId) {
                $sub->where('setores.id', $setorId);
            });
        });

        $usuarios = $query->orderBy('name', 'asc')->get();
        $setores = Setor::all(); // Substitua pelo seu Model de setores

        return view('admin.painel', compact('usuarios', 'setores'));
    }

    public function toggleSetor(Request $request, $id)
{
    $user = User::findOrFail($id);
    $setorId = $request->setor_id;

    // O método 'toggle' do Laravel adiciona se não tiver, e remove se já tiver
    $user->setores()->toggle($setorId);

    $status = $user->setores->contains($setorId) ? 'added' : 'removed';

    return response()->json(['status' => $status]);
}



    // Salva as permissões (Muitos-para-Muitos)
    public function atualizarPermissoes(Request $request, User $user)
    {
        // O método sync remove os antigos e adiciona apenas os marcados no form
        $user->setores()->sync($request->setores);

        return back()->with('success', "Permissões de {$user->name} atualizadas!");
    }
    public function toggleStatus($id) {
    $user = User::findOrFail($id);
    $user->ativo = !$user->ativo; // Inverte 0 para 1 ou vice-versa
    $user->save();

    return response()->json([
        'status' => 'success',
        'ativo' => (bool)$user->ativo // Retorna true ou false
    ]);
}
}