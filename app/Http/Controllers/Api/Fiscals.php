<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fiscal;
use Illuminate\Http\Request;


class Fiscals extends Controller
{
    public function index()
    {
        $dados = Fiscal::all();

        return response()->json($dados);
    }
    
    public function show($id)
    {
        $pagamento = Pagamento_Pix::find($id);

        if (!$pagamento) {
            return response()->json([
                'message' => 'Pagamento não encontrado'
            ], 404);
        }

        return response()->json($pagamento);
    }
}
