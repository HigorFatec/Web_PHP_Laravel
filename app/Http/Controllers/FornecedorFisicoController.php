<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FornecedorFisicoController extends Controller
{
    public function create()
    {
        return view('fisico.fornecedor_fisico');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_remetente' => 'required|string',
            'email_remetente' => 'required|email',
            'nome' => 'nullable|string',
            'cpf' => 'nullable|string',
            'rg' => 'nullable|string',
            'rua' => 'nullable|string',
            'numero' => 'nullable|string',
            'bairro' => 'nullable|string',
            'cidade' => 'nullable|string',
            'estado' => 'nullable|string',
            'cep' => 'nullable|string',
            'complemento' => 'nullable|string',
            'banco' => 'nullable|string',
            'agencia' => 'nullable|string',
            'conta' => 'nullable|string',
            'tipo_conta' => 'nullable|string',
            'pix' => 'nullable|string',
            'telefone_fixo' => 'nullable|string',
            'celular' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        // Adicionar debug para verificar se os dados estão corretos
        Log::info('Dados do formulário:', $request->all());

        // Adiciona o IP e o endereço da máquina aos dados da empresa
        $data = $request->all();

        // Envia o email com os dados do fornecedor físico
        Mail::send('emails.fornecedor_fisico', ['dados' => $data], function($message) {
            //$message->to('higor.05@hotmail.com');
            $message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
            $message->subject('Novo Fornecedor Físico Registrado');
        });

        return redirect()->route('fisico.success');
    }
}
