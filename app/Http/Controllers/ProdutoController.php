<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_remetente' => 'required|string',
            'email_remetente' => 'required|email',
            'nome' => 'nullable|string',
            'ncm' => 'nullable|string',
            'ca' => 'nullable|string',

        ]);

        // Adiciona o IP e o endereço da máquina aos dados da empresa
        $data = $request->all();

        Log::info('Dados recebidos para envio de email:', $data);

        try {
            // Enviar o e-mail
            Mail::send('emails.produtos', ['dados' => $data], function($message) {
                $message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br']);
                $message->subject('Novo Produto Registrado');
            });

            Log::info('E-mail enviado com sucesso.');
            return redirect()->route('produtos.success')->with('success', 'E-mail enviado com sucesso.');
        } catch (\Swift_TransportException $e) {
            // Erro específico relacionado ao transporte de e-mail
            Log::error('Erro de transporte ao enviar email: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Falha ao enviar email devido a problemas de transporte.'])->withInput();
        } catch (\Exception $e) {
            // Outros erros gerais
            Log::error('Erro ao enviar email: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Falha ao enviar email. Por favor, tente novamente mais tarde.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produto $produto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produto $produto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        //
    }
}
