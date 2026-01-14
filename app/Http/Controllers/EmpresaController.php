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

        $bancos = FornecedorFinanceiro::bancos();

        return view('empresa.create', compact('cidades','bancos'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tipo' => 'nullable|string',
            'nome_remetente' => 'required|string',
            'email_remetente' => 'required|email',
            'razao_social' => 'required|string|max:80',
            'nome_abreviado' => 'required|string|max:40',
            'cpf' => 'nullable|string',
            'rg' => 'nullable|string',
            'email_fornecedor' => 'nullable|string|max:80',
            'telefone_fornecedor' => 'nullable|string|max:15',


            'cidade' => ['required', 'string', 'not_regex:/^\s*$/'],

            'ie' => 'nullable|string',
            'cnpj' => 'nullable|string',

            'endereco' => 'required|string|max:80',
            'bairro' => 'required|string|max:80',
            'email' => 'nullable|email|max:80',
            'banco' => 'nullable|string',
            'agencia' => 'nullable|string',
            'conta' => 'nullable|string|max:10',
            'favorecido' => 'nullable|string|max:80',
            'pix_preferencial' => 'nullable|string',
            'pix_cnpj' => 'nullable|string|max:50',
            'pix_email' => 'nullable|string|max:50',
            'pix_telefone' => 'nullable|string|max:50',
            'pix_aleatorio' => 'nullable|string|max:50',
            'cep' => 'nullable|string'
        ], [
            'cidade.not_regex' => 'O campo cidade é obrigatório.',
            'bairro.max' => 'O campo bairro deve ter no máximo 80 caracteres.',
            'endereco.max' => 'O campo endereço deve ter no máximo 80 caracteres.',
            'razao_social.max' => 'O campo razão social deve ter no máximo 80 caracteres.',
            'nome_abreviado.max' => 'O campo nome abreviado deve ter no máximo 40 caracteres.',
            'email_fornecedor.max' => 'O campo e-mail fornecedor deve ter no máximo 80 caracteres.',
            'conta.max' => 'O campo conta deve ter no máximo 10 caracteres.',
            'favorecido.max' => 'O campo favorecido deve ter no máximo 80 caracteres.',
            'pix_cnpj.max' => 'O campo pix cnpj deve ter no máximo 50 caracteres.',
            'pix_email.max' => 'O campo pix email deve ter no máximo 50 caracteres.',
            'pix_telefone.max' => 'O campo pix telefone deve ter no máximo 50 caracteres.',
            'pix_aleatorio.max' => 'O campo pix aleatório deve ter no máximo 50 caracteres.',
            'email.email' => 'O campo e-mail deve ser um endereço de e-mail válido.',
            'email.max' => 'O campo e-mail deve ter no máximo 80 caracteres.',

        ]);


        // Adicionar debug para verificar se os dados estão corretos
        Log::info('Dados do formulário:', $request->all());

        // --- CONSULTA CNPJ ---
        if (!empty($request->cnpj)) {

            $dadosCNPJ = $this->consultarCNPJ($request->cnpj);

            if (isset($dadosCNPJ['erro'])) {
                return back()->withErrors(['cnpj' => $dadosCNPJ['erro']])->withInput();
            }

            //$razaoOficial = strtoupper(trim($dadosCNPJ['razao_social']));
            $razaoOficial = strtoupper(trim($dadosCNPJ['nome']));
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
            $message->subject('Novo Fornecedor para Aprovação | Protocolo ' . $fornecedor->id . ' - Tipo: '  . ($fornecedor->tipo ?? ''));
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

            //,$pix_preferencial,$pix_cnpj,$pix_email,$pix_celular)
            FornecedorFinanceiro::cadastro_Fornecedor_fisico($fornecedor->razao_social,$fornecedor->nome_abreviado,$fornecedor->endereco,$fornecedor->bairro,$fornecedor->cidade,$fornecedor->rg,$fornecedor->cpf,$fornecedor->banco,$fornecedor->agencia,$fornecedor->conta,$fornecedor->favorecido,$fornecedor->pix_aleatorio,$fornecedor->email_fornecedor,$fornecedor->pix_preferencial,$fornecedor->pix_cnpj,$fornecedor->pix_email,$fornecedor->pix_telefone,$fornecedor->telefone_fornecedor,$fornecedor->cep);



        } else {
            $cpfCheck = FornecedorFinanceiro::verificar_cnpj($fornecedor->cnpj);

            if ($cpfCheck && $cpfCheck['success'] === false) {
                return redirect()->route('empresa.create')->withErrors($cpfCheck['message'])->withInput();
            }

            FornecedorFinanceiro::cadastro_Fornecedor_juridico($fornecedor->razao_social,$fornecedor->nome_abreviado,$fornecedor->endereco,$fornecedor->bairro,$fornecedor->cidade,$fornecedor->ie,$fornecedor->cnpj,$fornecedor->banco,$fornecedor->agencia,$fornecedor->conta,$fornecedor->favorecido,$fornecedor->pix_aleatorio,$fornecedor->email_fornecedor,$fornecedor->pix_preferencial,$fornecedor->pix_cnpj,$fornecedor->pix_email,$fornecedor->pix_telefone,$fornecedor->telefone_fornecedor,$fornecedor->cep);
        }


        // Envia o email com os dados do fornecedor físico
        Mail::send('emails.fornecedor_financeiro', ['fornecedor' => $fornecedor], function($message) use ($fornecedor, $proximoCodCliFor) {
            //$message->to('higor.05@hotmail.com');
            $message->to($fornecedor->email_remetente);
            $message->cc('tiago.cunha@grupocargopolo.com.br','elizabete.vargas@grupocargopolo.com.br' ,'camila.andrade@grupocargopolo.com.br','laura.machado@grupocargopolo.com.br');
            $message->subject('Novo Fornecedor '  . ($fornecedor->tipo ?? '') .  ' Registrado; Codigo Fornecedor Rodopar: ' . $proximoCodCliFor);
        });

        $proximoCodCliFor = DB::connection('sqlsrv')->table('rodcli')->max('codclifor');

            
        $fornecedor->update(['codclifor' => $proximoCodCliFor]);

        $fornecedor->update(['status' => 'aprovado']);

        return back()->with('success2','Solicitação aprovada com sucesso!');

    }

    public function formReprovar($token)
    {
        $fornecedor = FornecedorFinanceiro::where('approval_token', $token)->firstOrFail();

        return view('empresa.reprovar', compact('fornecedor'));
    }


    public function reprovar(Request $request, $token)
    {

        $fornecedor = FornecedorFinanceiro::where('approval_token', $token)->firstOrFail();

        if ($fornecedor->status !== 'pendente') {
            return back()->withErrors('Solicitação já foi processada.');
        }

        $request->validate([
            'motivo' => 'required|string|min:5'
        ]);

        $fornecedor->motivo_reprovacao = $request->motivo;
        $fornecedor->save();



        Mail::send('emails.fornecedor_reprovado', ['fornecedor' => $fornecedor], function($message) use ($fornecedor){
            $message->to($fornecedor->email_remetente);
            $message->cc(['tiago.cunha@grupocargopolo.com.br','elizabete.vargas@grupocargopolo.com.br' ,'camila.andrade@grupocargopolo.com.br','laura.machado@grupocargopolo.com.br']);
            $message->subject('Solicitação Reprovada - Protocolo: ' . $fornecedor->id);
        });


        $fornecedor->update(['status' => 'reprovado']);

        return redirect()->route('empresa.aprovacao')->with('success2', 'Solicitação de Fornecedor reprovada com sucesso.');
    }

    private function consultarCNPJ($cnpj)
{
    $cnpj = preg_replace('/\D/', '', $cnpj); // remove pontuação

    if (strlen($cnpj) != 14) {
        return ['erro' => 'CNPJ deve ter 14 dígitos.'];
    }

    try {
        $response = Http::timeout(8)->get(
                //"https://brasilapi.com.br/api/cnpj/v1/{$cnpj}"
                "https://receitaws.com.br/v1/cnpj/{$cnpj}"
        );
    } catch (\Exception $e) {
        return ['erro' => 'Erro ao consultar API de CNPJ.'];
    }

    if ($response->failed()) {
        return ['erro' => 'CNPJ inválido ou não encontrado.'];
    }

    return $response->json();
}




    public function aprovacao()
    {
        $pendente = FornecedorFinanceiro::where('status','pendente')->orderBy('created_at', 'desc')->get();

        return view('empresa.aprovacao', compact('pendente'));

    }

}
