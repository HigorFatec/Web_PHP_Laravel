<?php

namespace App\Http\Controllers;

use App\Models\Pagamento_Pix;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Filial;
use App\Models\Produto_Arla;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use DB;



class PagamentoPixController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $filiais = Filial::orderBy('filial')->pluck('filial');
        $produtos = Produto_Arla::get();

        $veiculos = Pagamento_Pix::veiculos();

        $postos = Pagamento_Pix::postos();

        return view('pagamento_pix.index', compact('filiais','produtos','veiculos','postos'));
        //
    }

    public function buscarFornecedores(Request $request)
    {
        $search = $request->search;

        $fornecedores = Pagamento_Pix::motoristasQuery($search);

        return response()->json($fornecedores);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function aprovacao()
    {
        $aprovar = Pagamento_Pix::where('status','pendente')->orderBy('created_at', 'desc')->paginate(3);


        return view('pagamento_pix.aprovacao', compact('aprovar'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'email' => 'required|email',
            'fornecedor' => 'required|string',
            'data' => 'required|string',
            'cupom' => 'required|string',
            'placa' => 'required|string',
            'km' => 'required|numeric',
            'cpf' => 'required|string',
            'name' => 'required|string',
            'cnpj' => 'required|string',
            'posto' => 'required|string',
            'produto' => ['required', 'string', 'not_regex:/^\s*$/'],
            'litragem' => 'required|numeric',
            'valor' => 'required|numeric',
            'produto_arla' => 'nullable|string',
            'litragem_arla' => 'nullable|numeric',
            'valor_arla' => 'nullable|numeric',
            'banco' => 'required|string',
            'agencia' => 'required|string',
            'conta' => 'required|string',
            'cnpj_2' => 'required|string',
            'favorecido' => 'required|string',
            'pix' => 'required|string',
            'valor_3' => 'required|numeric',
            'email_gestor' => 'required|email',
            'filial' => ['required', 'string', 'not_regex:/^\s*$/'],

        ]);

        $pagamentoPix = Pagamento_Pix::create(array_merge(
            $validatedData,
                [
                    'approval_token' => Str::uuid(),
                    'status' => 'pendente',
                    'solicitante' => $user->name
                ]
            ));

        $foto = $request->file('foto');

        $pagamentoPix->load('produtos');

        $placa = DB::connection('sqlsrv')
        ->table('RODVEI')
        ->select('NUMVEI')
        ->where('CODVEI', '=', $pagamentoPix->placa)
        ->first();




        // Envia o email com os dados do formulário
        Mail::send('emails.pagamento_pix', ['dados' => $validatedData, 'pagamentoPix' => $pagamentoPix, 'placa' => $placa], function($message) use ($validatedData, $foto, $pagamentoPix, $placa){
            //$message->to(['combustivel@grupocargopolo.com.br','contasapagar@grupocargopolo.com.br', 'ludmylla.gomes@grupocargopolo.com.br', 'michel.plevka@grupocargopolo.com.br', 'vanderlei.nascimento@grupocargopolo.com.br','jaine.paula@grupocargopolo.com.br']);
            $message->to(['arthur.abreu@grupocargopolo.com.br','combustivel@grupocargopolo.com.br']);

            // $message->to('higor.machado@grupocargopolo.com.br');

            if ($validatedData['produto'] === 24){
                $message->cc($validatedData['email_gestor']);
            }

            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
            //$message->cc([$validatedData['email'],$validatedData['email_gestor']]);
            $message->subject( 'SOLICITAÇÃO DE TRANSFERÊNCIA DE PIX ; POSTO: '. $validatedData['cnpj'] . ' PLACA: ' . $placa?->NUMVEI );

            //Verificar se existe imagem anexada
            if ($foto)  {
                $pathToFile = $foto->getPathname();
                $filename = $foto->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }

            
            if ($foto) {
                \Log::info('Foto anexada: ' . $foto->getClientOriginalName());
            } else {
                \Log::info('Foto não anexada.');
            }
            

        });

        

        // Redirecionar ou retornar uma resposta de sucesso
        return redirect()->route('pagamento_pix.index')->with('success', 'Transferencia realizada com sucesso!');
    }



    public function cancelarPagamento($id)
    {
        $reserva = Pagamento_Pix::findOrFail($id);
        
        if($reserva->status !== 'pendente'){
            return 'Solicitação já foi processada.';    
        }

        if (auth()->user()->admin == 0) {
                return redirect()->route('pagamento_pix.index')->with('error', 'Você não tem permissão para cancelar este veiculo.');
        }

        //$reserva->delete();
        // Altera o status da coluna "Ok" para "Cancelada"
        $reserva->status = 'cancelada';
        $reserva->save();

        // Obtém o usuário autenticado
        $user = Auth::user();

    // Envia o e-mail de cancelamento
    Mail::send('emails.cancelamento_pagamento_pix', ['dados' => $reserva, 'user' => $user], function($message) use ($user, $reserva) {
        $message->to([$reserva->email ,'combustivel@grupocargopolo.com.br', $user->email]);
        $message->subject('Pagamento Recusado!');
    });


    return redirect()->route('pagamento_pix.aprovacao')->with('success', 'Veiculo cancelada com sucesso.');
    }

    public function finalizarPagamento($id)
    {
        return 'Faça a aprovação do pagamento pelo link enviado no e-mail para finalizar o abastecimento.';
        $pagamentoPix = Pagamento_Pix::findOrFail($id);

        if($pagamentoPix->status !== 'pendente'){
            return 'Solicitação já foi processada.';    
        }

        if (auth()->user()->admin == 0) {
                return redirect()->route('pagamento_pix.index')->with('error', 'Você não tem permissão para finalizar esta reserva.');
        }

        //$reserva->delete();
        // Altera o status da coluna "Ok" para "Cancelada"
        $pagamentoPix->status = 'finalizada';
        $pagamentoPix->save();

        // Obtém o usuário autenticado
        $user = Auth::user();

        $pagamentoPix->load('produtos');


        // Envia o e-mail de finalização
        Mail::send('emails.pagamento_pix', ['dados' => $pagamentoPix, 'pagamentoPix' => $pagamentoPix], function($message) use ($pagamentoPix, $user){
            $message->to(['combustivel@grupocargopolo.com.br','contasapagar@grupocargopolo.com.br', 'michel.plevka@grupocargopolo.com.br', 'vanderlei.nascimento@grupocargopolo.com.br','jaine.paula@grupocargopolo.com.br']);
            //$message->to('higor.05@hotmail.com');
            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
            $message->cc([$pagamentoPix->email,$user->email]);
            $message->subject( ' TRANSFERÊNCIA DE PIX; POSTO: '. $pagamentoPix->cnpj . ' PLACA: ' . $pagamentoPix->placa );
        });

        return redirect()->route('pagamento_pix.aprovacao')->with('success2', 'Veiculo finalizado com sucesso.');

    }





// APROVAR - gestor clicou no link -> dispara para os setores
public function aprovar($token)
{
    $pagamentoPix = Pagamento_Pix::where('approval_token', $token)->firstOrFail();

    if ($pagamentoPix->status !== 'pendente') {
        return 'Solicitação já foi processada.';
    }

    // Obtém o usuário autenticado
    $user = Auth::user();

    if ($user === null) {
        // Alerta: Certifique-se de que a rota 'login.form' não redireciona de volta para cá, causando loop.
        return redirect()->route('login.form')->with('error', 'Você precisa estar logado para aprovar uma solicitação.');
    }

    // 1. Gera o próximo CODABA de forma limpa e segura
    $lancamento = DB::connection('sqlsrv')
        ->table('RODABA')
        ->selectRaw('ISNULL(MAX(CODABA), 0) + 1 AS proximo')
        ->value('proximo'); 

    // 2. Otimização: Faz apenas UM update salvando ambos os códigos gerados no seu banco local
    $proximoIdRaz = DB::connection('sqlsrv')->table('BANRAZ')->max('ID_RAZ') + 1;
    
    $pagamentoPix->update([
        'codaba' => $lancamento,
        'id_raz'  => $proximoIdRaz
    ]);

    try {
        $pagamentoPix->inserir_abastecimento(
            $pagamentoPix->placa,
            $pagamentoPix->cnpj,
            $pagamentoPix->km,
            $pagamentoPix->fornecedor,
            $pagamentoPix->produto,
            $pagamentoPix->litragem,
            $pagamentoPix->valor,
            $pagamentoPix->id,
            $pagamentoPix->codaba
        );

        // CORREÇÃO AQUI: Inicialização limpa do valor total
        $valor_total = (float) $pagamentoPix->valor;

        if ($pagamentoPix->produto_arla !== null) {
            $pagamentoPix->inserir_abastecimento(
                $pagamentoPix->placa,
                $pagamentoPix->cnpj,
                $pagamentoPix->km,
                $pagamentoPix->fornecedor,
                $pagamentoPix->produto_arla,
                $pagamentoPix->litragem_arla,
                $pagamentoPix->valor_arla,
                $pagamentoPix->id,
                $pagamentoPix->codaba
            );
            
            // CORREÇÃO AQUI: Soma matemática simples, sem atribuição dupla repetida
            $valor_total += (float) $pagamentoPix->valor_arla;
        }

        $pagamentoPix->abastecimentoBanRaz(
            $pagamentoPix->id,
            $valor_total,
            $pagamentoPix->solicitante,
            $pagamentoPix->placa,
            $user->name,
            $pagamentoPix->codaba
        );

    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Erro ao lançar no rodopar.',
            'error_debug' => $e->getMessage(),
            'line' => $e->getLine()
        ], 500);
    }

    $pagamentoPix->load('produtos');

    $placa = DB::connection('sqlsrv')
            ->table('RODVEI')
            ->select('NUMVEI')
            ->where('CODVEI', '=', $pagamentoPix->placa)
            ->first();

    // Envia o e-mail de finalização
    // Importante: Passando as variáveis necessárias via "use" de forma correta
    Mail::send('emails.pagamento_pix_aprovado', ['dados' => $pagamentoPix, 'pagamentoPix' => $pagamentoPix, 'placa' => $placa], function($message) use ($pagamentoPix, $user, $placa) {
        
        $message->to([
            'combustivel@grupocargopolo.com.br',
            'contasapagar@grupocargopolo.com.br', 
            'michel.plevka@grupocargopolo.com.br', 
            'vanderlei.nascimento@grupocargopolo.com.br',
            'jaine.paula@grupocargopolo.com.br'
        ]);

        // Evita quebra se $user ou e-mails do banco forem nulos
        $ccEmails = array_filter([$pagamentoPix->email, $user?->email, $pagamentoPix->email_gestor]);
        if (!empty($ccEmails)) {
            $message->cc($ccEmails);
        }

        $numVei = $placa ? $placa->NUMVEI : 'N/A';
        $message->subject('TRANSFERÊNCIA DE PIX; POSTO: ' . $pagamentoPix->cnpj . ' PLACA: ' . $numVei);
    });
    
    // Atualiza o status para ok após o envio bem-sucedido do e-mail
    $pagamentoPix->update(['status' => 'ok']);

    return 'Solicitação aprovada com sucesso!';
}


    public function reprovar($token)
    {
        $reserva = Pagamento_Pix::where('approval_token', $token)->firstOrFail();

        if ($reserva->status !== 'pendente') {
            return 'Solicitação já foi processada.';
        }

        
        // Obtém o usuário autenticado
        $user = Auth::user();

        if($user === null){
            //rota login
            return redirect()->route('login.form')->with('error', 'Você precisa estar logado para reprovar uma solicitação.');
        }


        // Envia o e-mail de cancelamento
        Mail::send('emails.cancelamento_pagamento_pix', ['dados' => $reserva, 'user' => $user], function($message) use ($user, $reserva) {
            $message->to([$reserva->email ,'combustivel@grupocargopolo.com.br', $user->email]);
            $message->subject('Pagamento Recusado!');
        });

        $reserva->update(['status' => 'reprovado']);


        return 'Solicitação reprovada com sucesso!';
    }


}
