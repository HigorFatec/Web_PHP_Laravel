<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pagamento_Pix;
use App\Models\Florestal_Pix;
use Illuminate\Http\Request;


class PagamentoPix extends Controller
{
    public function index()
    {
        $dados = Pagamento_Pix::all();

        return response()->json($dados);
    }
    
    public function florestal()
    {
        $dados = Florestal_Pix::all();

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
