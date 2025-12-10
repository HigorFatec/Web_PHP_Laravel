<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\FornecedorFinanceiro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;




class EmpresaController extends Controller
{
    public function create()
    {

        $cidades = FornecedorFinanceiro::cidades();

        return view('empresa.create', compact('cidades'));
    }

    public function store(Request $request)
 {
        $validatedData = $request->validate([
            'tipo' => 'nullable|string',
            'nome_remetente' => 'required|string',
            'email_remetente' => 'required|email',
            'razao_social' => 'required|string',
            'nome_abreviado' => 'required|string',
            'cpf' => 'nullable|string',
            'rg' => 'nullable|string',
            'email_fornecedor' => 'nullable|string',

            'cidade' => ['required', 'string', 'not_regex:/^\s*$/'],

            'ie' => 'nullable|string',
            'cnpj' => 'nullable|string',

            'endereco' => 'required|string',
            'bairro' => 'required|string',
            'email' => 'nullable|email',
            'banco' => 'nullable|string',
            'agencia' => 'nullable|string',
            'conta' => 'nullable|string',
            'favorecido' => 'nullable|string',
            'pix_aleatorio' => 'nullable|string',
        ]);


        // Adicionar debug para verificar se os dados estão corretos
        Log::info('Dados do formulário:', $request->all());

        // --- CONSULTA CNPJ ---
        if (!empty($request->cnpj)) {

            $dadosCNPJ = $this->consultarCNPJ($request->cnpj);

            if (isset($dadosCNPJ['erro'])) {
                return back()->withErrors(['cnpj' => $dadosCNPJ['erro']])->withInput();
            }

            $razaoOficial = strtoupper(trim($dadosCNPJ['razao_social']));
            $razaoInformada = strtoupper(trim($request->razao_social));

            // --- COMPARAÇÃO ---
            if ($razaoInformada !== $razaoOficial) {
                return back()
                    ->withErrors([
                        'razao_social' =>
                            "A razão social informada não confere com o cadastro oficial da Receita Federal.
                            <br><b>Digitado:</b> {$request->razao_social}
                            <br><b>Correto:</b> {$dadosCNPJ['razao_social']}"
                    ])
                    ->withInput();
            }
        }

        
        $fornecedor = FornecedorFinanceiro::create(array_merge(
            $validatedData,
            [
                'approval_token' => Str::uuid(),
                'status' => 'pendente'
            ]
        ));



        // Envia o email com os dados do fornecedor físico
        Mail::send('emails.fornecedor_pendente', ['fornecedor' => $fornecedor], function($message) use ($fornecedor) {
            // $message->to('higor.05@hotmail.com');
            $message->to('elizabete.vargas@grupocargopolo.com.br' ,'camila.andrade@grupocargopolo.com.br','laura.machado@grupocargopolo.com.br');
            $message->cc('tiago.cunha@grupocargopolo.com.br');
            $message->subject('Novo Fornecedor para Aprovação '  . ($fornecedor->tipo ?? ''));
        });

        return redirect()->route('empresa.create')->with('success', 'Solicitação de Fornecedor criado com sucesso.');
    }






    public function aprovar($token){

        $fornecedor = FornecedorFinanceiro::where('approval_token', $token)->firstOrFail();

        if ($fornecedor->status !== 'pendente') {
            return back()->withErrors('Solicitação já foi processada.');
        }

        // Gera o próximo codclifor
        $proximoCodCliFor = DB::connection('sqlsrv')->table('rodcli')->max('codclifor') + 1;

        //dd($proximoCodCliFor);
        
        if($fornecedor->tipo === 'fisico'){
            
            $cpfCheck = FornecedorFinanceiro::verificar_cpf($fornecedor->cpf);  

            if ($cpfCheck && $cpfCheck['success'] === false) {
                return redirect()->route('empresa.create')->withErrors($cpfCheck['message'])->withInput();
            }

            FornecedorFinanceiro::cadastro_Fornecedor_fisico($fornecedor->razao_social,$fornecedor->nome_abreviado,$fornecedor->endereco,$fornecedor->bairro,$fornecedor->cidade,$fornecedor->rg,$fornecedor->cpf,$fornecedor->banco,$fornecedor->agencia,$fornecedor->conta,$fornecedor->favorecido,$fornecedor->pix_aleatorio,$fornecedor->email_fornecedor);



        } else {
            $cpfCheck = FornecedorFinanceiro::verificar_cnpj($fornecedor->cnpj);

            if ($cpfCheck && $cpfCheck['success'] === false) {
                return redirect()->route('empresa.create')->withErrors($cpfCheck['message'])->withInput();
            }

            FornecedorFinanceiro::cadastro_Fornecedor_juridico($fornecedor->razao_social,$fornecedor->nome_abreviado,$fornecedor->endereco,$fornecedor->bairro,$fornecedor->cidade,$fornecedor->inscricao_estadual,$fornecedor->cnpj,$fornecedor->banco,$fornecedor->agencia,$fornecedor->conta,$fornecedor->favorecido,$fornecedor->pix_aleatorio,$fornecedor->email_fornecedor);
        }


        // Envia o email com os dados do fornecedor físico
        Mail::send('emails.fornecedor_financeiro', ['fornecedor' => $fornecedor], function($message) use ($fornecedor, $proximoCodCliFor) {
            //$message->to('higor.05@hotmail.com');
            $message->to($fornecedor->email_remetente);
            $message->cc('tiago.cunha@grupocargopolo.com.br','elizabete.vargas@grupocargopolo.com.br' ,'camila.andrade@grupocargopolo.com.br','laura.machado@grupocargopolo.com.br');
            $message->subject('Novo Fornecedor '  . ($fornecedor->tipo ?? '') .  ' Registrado; Codigo Fornecedor Rodopar: ' . $proximoCodCliFor);
        });

        $proximoCodCliFor = DB::connection('sqlsrv')->table('rodcli')->max('codclifor');

            
        $fornecedor->update(['codclifor' => $proximoCodCliFor])

        $fornecedor->update(['status' => 'aprovado']);

        return back()->with('success2','Solicitação aprovada com sucesso!');

    }

    public function reprovar($token)
    {
        $fornecedor = FornecedorFinanceiro::where('approval_token', $token)->firstOrFail();

        if ($fornecedor->status !== 'pendente') {
            return back()->withErrors('Solicitação já foi processada.');
        }

        Mail::send('emails.fornecedor_reprovado', ['fornecedor' => $fornecedor], function($message) use ($fornecedor){
            $message->to($fornecedor->email_remetente);
            $message->cc('tiago.cunha@grupocargopolo.com.br','elizabete.vargas@grupocargopolo.com.br' ,'camila.andrade@grupocargopolo.com.br','laura.machado@grupocargopolo.com.br');
            $message->subject('Solicitação Reprovada - Protocolo: ' . $fornecedor->id);
        });


        $fornecedor->update(['status' => 'reprovado']);

        return back()->withErrors('Solicitação reprovada com sucesso!');
    }

    private function consultarCNPJ($cnpj)
{
    $cnpj = preg_replace('/\D/', '', $cnpj); // remove pontuação

    if (strlen($cnpj) != 14) {
        return ['erro' => 'CNPJ deve ter 14 dígitos.'];
    }

    try {
        $response = Http::timeout(8)->get(
            "https://brasilapi.com.br/api/cnpj/v1/{$cnpj}"
        );
    } catch (\Exception $e) {
        return ['erro' => 'Erro ao consultar API de CNPJ.'];
    }

    if ($response->failed()) {
        return ['erro' => 'CNPJ inválido ou não encontrado.'];
    }

    return $response->json();
}


}
