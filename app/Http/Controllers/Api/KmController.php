<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KmAtualizadoVeiculo;
use Illuminate\Http\Request;

class KmController extends Controller
{
    public function index()
    {
        $dados = KmAtualizadoVeiculo::all();

        return response()->json($dados);
    }

    public function show($numvei)
    {
        $veiculo = KmAtualizadoVeiculo::where('NUMVEI', $numvei)->first();

        if (!$veiculo) {
            return response()->json([
                'message' => 'Veículo não encontrado'
            ], 404);
        }

        return response()->json($veiculo);
    }
}
