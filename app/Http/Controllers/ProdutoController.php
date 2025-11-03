<?php

namespace App\Http\Controllers;
use App\Models\Produto;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


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
            'email_aprovador' => 'required|email',
            'nome' => 'nullable|string',
            'ncm' => 'nullable|string',
            'ca' => 'nullable|string',
            'tipo' => ['required', 'string', 'not_regex:/^\s*$/'],

        ]);

        // Adiciona o IP e o endereço da máquina aos dados da empresa
        $data = $request->all();

        // Se CA informado, verificar se já existe na tabela estpro
        if (!empty($data['ca'])) {
            $caInformado = strtoupper(trim($data['ca']));
            $caNormalizado = ltrim(preg_replace('/[^0-9]/', '', $caInformado), '0'); // Só números
            $caNormalizado = $caNormalizado === '' ? '0' : $caNormalizado;

            // Puxa todos os valores da coluna 'aplica'
            $casDoBanco = DB::connection('sqlsrv')
                ->table('estpro')
                ->whereNotNull('aplica')
                ->pluck('aplica');

            foreach ($casDoBanco as $caBanco) {
                $caBancoNormalizado = ltrim(preg_replace('/[^0-9]/', '', strtoupper(trim($caBanco))), '0'); // Só números
                $caBancoNormalizado = $caBancoNormalizado === '' ? '0' : $caBancoNormalizado;

                if ($caBancoNormalizado === $caNormalizado) {
                    return back()->withErrors(['msg' => 'Este CA já existe no sistema.'])->withInput();
                }
            }
        }

        $tipo = $data['tipo'];

        $produto = Produto::create(array_merge(
            $data, 
            [
                'approval_token' => Str::uuid(),
                'status' => 'pendente',
                
            // Adicione aqui quaisquer campos adicionais que você queira definir
        ]));




        Log::info('Dados recebidos para envio de email:', $data);

        try {
            if($tipo =='epi'){
                // Enviar o e-mail
                Mail::send('emails.aprovador_produto', ['produto' => $produto], function($message) use($produto) {
                    //$message->to('higor.05@hotmail.com');
                    $message->to('patricia.ronca@grupocargopolo.com.br');
                    $message->subject('Novo Produto Registrado');
                });
            } else {
                // Enviar o e-mail
                Mail::send('emails.produtos', ['produto' => $produto], function($message) use($produto) {
                    //$message->to('higor.05@hotmail.com');
                    $message->to([$produto->email_aprovador,'cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br']);
                    $message->subject('Novo Produto Registrado');
                });

            Log::info('E-mail enviado com sucesso.');
            return redirect()->route('produtos.create')->with('success', 'Produto criado com sucesso!');
        }
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

    public function aprovar($token)
    {
        $produto = Produto::where('approval_token', $token)->firstOrFail();

        if ($produto->status !== 'pendente') {
            return 'Esta solicitação já foi processada.';
        }

        $produto->status = 'aprovado';
        $produto->save();

        // Enviar o e-mail
        Mail::send('emails.produtos', ['produto' => $produto], function($message) use($produto) {
            //$message->to('higor.05@hotmail.com');
            $message->to([$produto->$email_aprovador,'cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br']);
            $message->subject('Novo Produto Registrado');
        });

        return 'Solicitação aprovada com sucesso!';
    }

    public function reprovar($token)
    {
        $produto = Produto::where('approval_token', $token)->firstOrFail();

        if ($produto->status !== 'pendente') {
            return 'Esta solicitação já foi processada.';
        }

        $produto->status = 'reprovado';
        $produto->save();

        return 'Solicitação reprovada com sucesso!';
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
