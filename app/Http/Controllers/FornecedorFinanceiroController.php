<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\FornecedorFinanceiro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class FornecedorFinanceiroController extends Controller
{
    public function create()
    {
        $cidades = FornecedorFinanceiro::cidades();
        return view('fisico.fornecedor_financeiro', compact('cidades'));
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

        // Gera o próximo codclifor
        $proximoCodCliFor = DB::connection('sqlsrv')->table('rodcli')->max('codclifor') + 1;

        // Cria fornecedor
        $fornecedor = FornecedorFinanceiro::create(array_merge(
            $validatedData,
            ['codclifor' => $proximoCodCliFor]
        ));


        if($validatedData['tipo'] === 'fisico'){
            
            // $cpfCheck = FornecedorFinanceiro::verificar_cpf($validatedData['cpf']);

            // if ($cpfCheck && $cpfCheck['success'] === false) {
            //     return back()->withErrors($cpfCheck['message'])->withInput();
            // }

            FornecedorFinanceiro::cadastro_Fornecedor_fisico($validatedData['razao_social'],$validatedData['nome_abreviado'],$validatedData['endereco'],$validatedData['bairro'],$validatedData['cidade'],$validatedData['rg'],$validatedData['cpf'],$validatedData['banco'],$validatedData['agencia'],$validatedData['conta'],$validatedData['favorecido'],$validatedData['pix_aleatorio'],$validatedData['email_fornecedor']);
        } else {
            // $cpfCheck = FornecedorFinanceiro::verificar_cnpj($validatedData['cnpj']);

            // if ($cpfCheck && $cpfCheck['success'] === false) {
            //     return back()->withErrors($cpfCheck['message'])->withInput();
            // }

            FornecedorFinanceiro::cadastro_Fornecedor_juridico($validatedData['razao_social'],$validatedData['nome_abreviado'],$validatedData['endereco'],$validatedData['bairro'],$validatedData['cidade'],$validatedData['inscricao_estadual'],$validatedData['cnpj'],$validatedData['banco'],$validatedData['agencia'],$validatedData['conta'],$validatedData['favorecido'],$validatedData['pix_aleatorio'],$validatedData['email_fornecedor']);
        }


        // Envia o email com os dados do fornecedor físico
        Mail::send('emails.fornecedor_financeiro', ['fornecedor' => $fornecedor], function($message) use ($fornecedor, $proximoCodCliFor) {
            //$message->to('higor.05@hotmail.com');
            $message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
            $message->subject('Novo Fornecedor (Socorro em Rota)'  . ($validatedData['tipo'] ?? '') .  ' Registrado; Codigo Fornecedor: ' . $proximoCodCliFor);
        });

        return redirect()->route('financeiro_fr.index')->with('success', 'Fornecedor criado com sucesso.');
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
