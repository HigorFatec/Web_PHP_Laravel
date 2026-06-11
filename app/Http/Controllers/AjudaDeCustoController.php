<?php

namespace App\Http\Controllers;

use App\Models\AjudaDeCusto;
use Illuminate\Http\Request;
use App\Models\UnidadesNegocio;
use App\Models\Financeiro;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;


class AjudaDeCustoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        //
        $unidade = Financeiro::unidade_de_negocio();
        $custo = Financeiro::centro_de_custo();
        $gasto = Financeiro::centro_de_gasto();

        $fornecedores = []; // opcional, apenas para evitar erro no Blade

        // dd($unidade, $custo, $gasto);

        return view('ajuda_custo.index', compact('unidade', 'custo', 'gasto', 'fornecedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        

        $validatedData = $request->validate([
            'fornecedor' => 'required|integer',
            'cnpj' => 'required|string',
            'name' => 'required|string',
            'data_admissao' => 'required|date',
            'valor_fixo' => 'required|numeric',
            'valor_proporcional' => 'required|numeric',
            'observacao' => 'nullable|string',
            'pix' => 'nullable|string',
            'tipo_pix' => 'nullable|string',
            'banco' => 'nullable|string',
            'agencia' => 'nullable|string',
            'conta' => 'nullable|string',
            'favorecido' => 'nullable|string',
            'cod_unidade' => 'required|integer',
            'cod_gasto' => 'required|integer',
            'cod_custo' => 'required|string',
        ]);


        $user = auth()->user();

        $ajuda_custo = AjudaDeCusto::create(array_merge(
            $validatedData,
            [
                'approval_token' => Str::uuid(),
                'status' => 'pendente',
                'user_id' => auth()->id()
            ]
        ));



        $ajuda_custo->load('unidades', 'centroGasto', 'centroCusto','user');



        // Email só para o gestor aprovar/reprovar
        Mail::send('emails.ajuda_custo_pendente', ['ajuda_custo' => $ajuda_custo], function ($message) use ($validatedData, $ajuda_custo) {
            $message->to('leticia.carvalho@grupocargopolo.com.br');
            //$message->to('higor.machado@grupocargopolo.com.br');
            $message->subject('Solicitação de Ajuda de Custo Pendente - Filial ' . $ajuda_custo->unidades?->unidade_negocio . ' - Protocolo: ' . $ajuda_custo->id);
            

        });

        Mail::send('emails.ajuda_custo_solicitacao', ['ajuda_custo' => $ajuda_custo], function ($message) use ($validatedData, $user, $ajuda_custo) {
            $message->to($user->email);
            $message->subject('Confirmação de Solicitação Ajuda de Custo - Protocolo: ' . $ajuda_custo->id);

        });


        return redirect()->route('ajuda_de_custo.index')->with('success', 'Ajuda de custo criada com sucesso!');
    }

    public function aprovar($token)
    {
        // Busca o registro pelo token ou retorna erro 404 se não existir
        $ajudaCusto = AjudaDeCusto::where('approval_token', $token)->firstOrFail();

        // Valida se a solicitação já não foi processada antes
        if ($ajudaCusto->status !== 'pendente') {
            return view('emails.respostas.status', [
                'mensagem' => 'Esta solicitação já foi processada anteriormente.',
                'tipo' => 'aviso'
            ]);
        }


        $ajudaCusto->load('unidades', 'centroGasto', 'centroCusto', 'user');



        // 2. Insere na tabela Financeiro para o setor de pagamentos ver
        Financeiro::create([
            'tipo'             => 'ajuda_de_custo', // Identificador para a tela de resumo
            'referencia'       => 'Ajuda de Custo #' . $ajudaCusto->id,
            'cnpj'             => $ajudaCusto->cnpj,
            'favorecido'       => $ajudaCusto->favorecido ?? $ajudaCusto->name,
            'banco'            => $ajudaCusto->banco,
            'agencia'          => $ajudaCusto->agencia,
            'conta'            => $ajudaCusto->conta,
            'pix'              => $ajudaCusto->pix,
            'tipo_pix'         => $ajudaCusto->tipo_pix,
            'valor'            => $ajudaCusto->valor_proporcional,
            'solicitante'      => $ajudaCusto->name,
            'cod_unidade'      => $ajudaCusto->cod_unidade,
            'cod_custo'        => $ajudaCusto->cod_custo,
            'cod_gasto'        => $ajudaCusto->cod_gasto,
            'status'           => 'aprovado_gestor', // Status que sua tela de resumo busca
            'gestor_aprovador' => 'leticia.carvalho@grupocargopolo.com.br',
            // 'gestor_aprovador' => 'higor.machado@grupocargopolo.com.br',

            'user_id'          => $ajudaCusto->user_id,
            'fornecedor'       => $ajudaCusto->fornecedor,
            'tem_nota_fiscal'  => 'nao',
            'email'            => $ajudaCusto->user?->email
        ]);



        // Opcional: Você pode disparar um e-mail aqui notificando o usuário que foi aprovado.
        // Envia e-mail de notificação de APROVAÇÃO para o solicitante
        if ($ajudaCusto->user?->email) {
            Mail::send('emails.ajuda_custo_aprovado', ['ajuda_custo' => $ajudaCusto], function ($message) use ($ajudaCusto) {
                $message->to($ajudaCusto->user->email);
                $message->cc('higor.machado@grupocargopolo.com.br'); // Cópia para o gestor
                $message->subject('Solicitação de Ajuda de Custo APROVADA - Protocolo: ' . $ajudaCusto->id);
            });
        }


        // Atualiza o status
        $ajudaCusto->update([
            'status' => 'aprovado'
        ]);


        return view('emails.respostas.status', [
            'mensagem' => 'Solicitação de ajuda de custo APROVADA com sucesso!',
            'tipo' => 'sucesso'
        ]);
    }


    public function exibirFormReprovar($token)
    {
        $ajudaCusto = AjudaDeCusto::where('approval_token', $token)->firstOrFail();

        if ($ajudaCusto->status !== 'pendente') {
            return view('emails.respostas.status', [
                'mensagem' => 'Esta solicitação já foi processada anteriormente.',
                'tipo' => 'aviso'
            ]);
        }

        return view('ajuda_custo.reprovar_motivo', compact('ajudaCusto'));
    }




    public function reprovar(Request $request, $token)
    {
        $request->validate([
            'motivo' => 'required|string|max:500'
        ], [
            'motivo.required' => 'O motivo da reprovação é obrigatório.'
        ]);

        $ajudaCusto = AjudaDeCusto::where('approval_token', $token)->firstOrFail();

        if ($ajudaCusto->status !== 'pendente') {
            return view('emails.respostas.status', [
                'mensagem' => 'Esta solicitação já foi processada anteriormente.',
                'tipo' => 'aviso'
            ]);
        }

        $ajudaCusto->load('unidades', 'centroGasto', 'centroCusto', 'user');

        // Atualiza o status e salva o motivo (opcional se tiver a coluna no banco)
        $ajudaCusto->update([
            'status' => 'reprovado',
            'motivo_reprovacao' => $request->motivo // Opcional
        ]);

        // Envia e-mail de notificação de REPROVAÇÃO para o solicitante com o motivo
        if ($ajudaCusto->user?->email) {
            Mail::send('emails.ajuda_custo_reprovado', ['ajuda_custo' => $ajudaCusto, 'motivo' => $request->motivo], function ($message) use ($ajudaCusto) {
                $message->to($ajudaCusto->user->email);
                $message->subject('Solicitação de Ajuda de Custo REPROVADA - Protocolo: ' . $ajudaCusto->id);
            });
        }

        return view('emails.respostas.status', [
            'mensagem' => 'Solicitação de ajuda de custo REPROVADA com sucesso.',
            'tipo' => 'erro'
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(AjudaDeCusto $ajudaDeCusto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AjudaDeCusto $ajudaDeCusto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AjudaDeCusto $ajudaDeCusto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AjudaDeCusto $ajudaDeCusto)
    {
        //
    }
}
