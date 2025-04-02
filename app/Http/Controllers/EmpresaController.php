<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmpresaController extends Controller
{
    public function create()
    {
        return view('empresa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_remetente' => 'required|string',
            'email_remetente' => 'required|email',
            'razao_social' => 'nullable|string',
            'inscricao_estadual' => 'nullable|string',
            'cnpj' => 'nullable|string',
            'banco' => 'nullable|string',
            'agencia' => 'nullable|string',
            'conta' => 'nullable|string',
            'tipo_conta' => 'nullable|string',
            'pix' => 'nullable|string',
            'telefone_1' => 'nullable|string',
            'telefone_2' => 'nullable|string',
            'direto_com' => 'nullable|string',
            'email' => 'nullable|email',
            'rua' => 'nullable|string',
            'numero' => 'nullable|string',
            'bairro' => 'nullable|string',
            'cidade' => 'nullable|string',
            'estado' => 'nullable|string',
            'cep' => 'nullable|string',
            'complemento' => 'nullable|string',
        ]);

        // Adiciona o IP e o endereço da máquina aos dados da empresa
        $data = $request->all();

        Log::info('Dados recebidos para envio de email:', $data);

        try {
            // Enviar o e-mail
            Mail::send('emails.empresa', ['dados' => $data], function($message) {
                $message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br']);
                $message->subject('Nova Empresa Registrada');
            });

            Log::info('E-mail enviado com sucesso.');
            return redirect()->route('empresa.success')->with('success', 'E-mail enviado com sucesso.');
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
}
