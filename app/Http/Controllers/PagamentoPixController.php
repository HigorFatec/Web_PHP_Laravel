<?php

namespace App\Http\Controllers;

use App\Models\Pagamento_Pix;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Filial;
use App\Models\Produto_Arla;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Http;



class PagamentoPixController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $filiais = Filial::orderBy('filial')->pluck('filial');
        $produtos = Produto_Arla::orderBy('nome')->pluck('nome');

        return view('pagamento_pix.index', compact('filiais','produtos'));
        //
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
        $validatedData = $request->validate([
            'email' => 'required|email',
            'data' => 'required|string',
            'cupom' => 'required|string',
            'placa' => 'required|string',
            'km' => 'required|string',
            'cpf' => 'required|string',
            'name' => 'required|string',
            'cnpj' => 'required|string',
            'posto' => 'required|string',
            'produto' => ['required', 'string', 'not_regex:/^\s*$/'],
            'litragem' => 'required|string',
            'valor' => 'required|string',
            'produto_arla' => 'required|string',
            'litragem_arla' => 'required|string',
            'valor_arla' => 'required|string',
            'banco' => 'required|string',
            'agencia' => 'required|string',
            'conta' => 'required|string',
            'cnpj_2' => 'required|string',
            'favorecido' => 'required|string',
            'pix' => 'required|string',
            'valor_3' => 'required|string',
            'email_gestor' => 'required|email',
            'filial' => ['required', 'string', 'not_regex:/^\s*$/'],

        ]);

        $pagamentoPix = Pagamento_Pix::create($validatedData);

        $foto = $request->file('foto');

        // Envia o email com os dados do formulário
        Mail::send('emails.pagamento_pix', ['dados' => $validatedData], function($message) use ($validatedData, $foto, $pagamentoPix){
            //$message->to(['combustivel@grupocargopolo.com.br','contasapagar@grupocargopolo.com.br', 'ludmylla.gomes@grupocargopolo.com.br', 'michel.plevka@grupocargopolo.com.br', 'vanderlei.nascimento@grupocargopolo.com.br','jaine.paula@grupocargopolo.com.br']);
            $message->to(['arthur.abreu@grupocargopolo.com.br','combustivel@grupocargopolo.com.br']);
            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
            //$message->cc([$validatedData['email'],$validatedData['email_gestor']]);
            $message->subject( 'SOLICITAÇÃO DE TRANSFERÊNCIA DE PIX ; POSTO: '. $validatedData['cnpj'] );

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
        $reserva = Pagamento_Pix::findOrFail($id);

        if (auth()->user()->admin == 0) {
                return redirect()->route('pagamento_pix.index')->with('error', 'Você não tem permissão para finalizar esta reserva.');
        }

        //$reserva->delete();
        // Altera o status da coluna "Ok" para "Cancelada"
        $reserva->status = 'finalizada';
        $reserva->save();

        // Obtém o usuário autenticado
        $user = Auth::user();

        // Envia o e-mail de finalização
        Mail::send('emails.pagamento_pix', ['dados' => $reserva], function($message) use ($reserva, $user){
            $message->to(['combustivel@grupocargopolo.com.br','contasapagar@grupocargopolo.com.br', 'michel.plevka@grupocargopolo.com.br', 'vanderlei.nascimento@grupocargopolo.com.br','jaine.paula@grupocargopolo.com.br']);
            //$message->to('higor.05@hotmail.com');
            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
            $message->cc([$reserva->email,$user->email]);
            $message->subject( ' TRANSFERÊNCIA DE PIX; POSTO: '. $reserva->cnpj . ' PLACA: ' . $reserva->placa );
        });

        return redirect()->route('pagamento_pix.aprovacao')->with('success2', 'Veiculo finalizado com sucesso.');

    }


}
