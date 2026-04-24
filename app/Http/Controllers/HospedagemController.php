<?php

namespace App\Http\Controllers;

use App\Models\Hospedagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Filial;
use App\Models\UsersGestores;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 


use Illuminate\Support\Facades\Mail;
use App\Models\FornecedorFinanceiro;


class HospedagemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');

        $aprovadores = UsersGestores::orderBy('nome')->get(['id','nome','email','operacao']);


        $cidades = FornecedorFinanceiro::cidades();

        return view('reserva.hospedagem', compact('filiais', 'cidades', 'aprovadores'));
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

            //Obter usuário autenticado
            $user = Auth::user();
            
            // Validação dos campos
            $validatedData = $request->validate([
                'destino' => 'required|string',
                'ida' => 'required|date',
                'volta' => 'nullable|date|after_or_equal:ida',
                'motivo' => 'required|string',
                'referencia' => 'required|string',
                'validacao' => 'required|string',
                'email_gestor' => 'required|email',
                'observacoes' => 'nullable|string',
                'nome' => 'required|string',
                'cpf' => 'required|numeric',
                'rg' => 'required|numeric',
                'data_nascimento' => 'required|date',
                'email' => 'required|email',
                'filial_viajante' => 'required|string',
            ]);
    
            
            // Se precisar salvar em um banco de dados, adicione o código aqui
            // Exemplo:
             $hospedagem = Hospedagem::create(array_merge(
                $request->all(),
                [
                    'approval_token' => Str::uuid(),
                    'user_name' => $user->name,
                    'user_id' => $user->id,
                    'user_cpf' => $user->cpf,
                    'user_email' => $user->email,
                ]
             ));



            // TRATAMENTO DO UPLOAD DO ANEXO
            // STORE
            if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
                $file = $request->file('foto');
                $extension = $file->guessExtension() ?? 'bin';
                $filename = time() . '_' . Str::uuid() . '.' . $extension;

                $file->move(storage_path('app/public/reservas'), $filename);

                $path = 'reservas/' . $filename;
                $hospedagem->update(['anexo_path' => $path]);


                $hospedagem->update(['anexo_path' => $path]);
            }
            // FIM ANEXO
            // Verifique se o arquivo foi capturado
            //dd($foto);
            $hospedagem->load('gestor');



            
            // Email só para o gestor aprovar/reprovar
            Mail::send('emails.hospedagem_pendente', ['hospedagem' => $hospedagem, 'user' => $user], function ($message) use ($validatedData, $hospedagem, $user) {
                $message->to($validatedData['email_gestor']);
                $message->subject('Solicitação Pendente de Hospedagem - Protocolo: ' . $hospedagem->id);

                // anexa se o arquivo foi salvo (disk = public)
                if (!empty($hospedagem->anexo_path) && Storage::disk('public')->exists($hospedagem->anexo_path)) {
                    // full path: storage/app/public/{anexo_path}
                    $message->attach(storage_path('app/public/' . $hospedagem->anexo_path));
                }

            });

                Mail::send('emails.hospedagem_solicitacao', ['hospedagem' => $hospedagem, 'user' => $user], function ($message) use ($validatedData, $hospedagem, $user) {
                $message->to($validatedData['email']);
                $message->subject('Confirmação de Solicitação de Hospedagem - Protocolo: ' . $hospedagem->id);

            // anexa se o arquivo foi salvo (disk = public)
            if (!empty($hospedagem->anexo_path) && Storage::disk('public')->exists($hospedagem->anexo_path)) {
                // full path: storage/app/public/{anexo_path}
                $message->attach(storage_path('app/public/' . $hospedagem->anexo_path));
            }

        });

    
            // Redirecionar ou retornar uma resposta de sucesso
            return redirect()->route('reserva.home')->with('success2', 'Reserva de hospedagem realizada com sucesso!');
        }



    // APROVAR - gestor clicou no link -> dispara para os setores
    public function aprovar($token)
    {
        $hospedagem = Hospedagem::where('approval_token', $token)->firstOrFail();

        if ($hospedagem->status !== 'pendente') {
            return 'Solicitação já foi processada.';
        }

        // Obtém o usuário autenticado
        $user = Auth::user();

        if($user === null){
            //rota login
            return redirect()->route('login.form')->with('error', 'Você precisa estar logado para aprovar uma solicitação.');
        }


        // Decide qual e-mail disparar pelo tipo

        Mail::send('emails.hospedagem', [
                'hospedagem' => $hospedagem, 'user' => $user
            ], function($message) use ($hospedagem,$user){
                $message->to('reservas@grupocargopolo.com.br');
                $message->cc([$user->email,$hospedagem->email,$hospedagem->email_gestor]);
                //$message->to('higor.05@hotmail.com');
                $message->subject('Nova Reserva de Hospedagem Solicitada');

            if (!empty($hospedagem->anexo_path) && Storage::disk('public')->exists($hospedagem->anexo_path)) {
                $message->attach(storage_path('app/public/' . $hospedagem->anexo_path));
            }
    }
        );
        
        $hospedagem->update(['status' => 'ok']);

        // idem para venda e descarte

        return 'Solicitação aprovada com sucesso!';
    }


    public function reprovar($token)
    {
        $hospedagem = Hospedagem::where('approval_token', $token)->firstOrFail();

        if ($hospedagem->status !== 'pendente') {
            return 'Solicitação já foi processada.';
        }

        
        // Obtém o usuário autenticado
        $user = Auth::user();

        if($user === null){
            //rota login
            return redirect()->route('login.form')->with('error', 'Você precisa estar logado para reprovar uma solicitação.');
        }


        Mail::send('emails.hospedagem_reprovado', ['hospedagem' => $hospedagem, 'user' => $user], function($message) use ($hospedagem, $user){
            $message->to($hospedagem->email);
            $message->cc($hospedagem->email_gestor);
            $message->subject('Solicitação Reprovada - Protocolo: ' . $hospedagem->id);
        });

        $hospedagem->update(['status' => 'reprovado']);


        return 'Solicitação reprovada com sucesso!';
    }



public function reenviar_pendencia($id)
        {
            //encontrar em reserva, hospedagem , veiculo ou adiantamento
            $hospedagem = Hospedagem::findOrFail($id);

    
            $user = auth()?->user();

            if (!$user) {
                return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
            }

            //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
            if (!$user->temSetor(['suprimentos','admin'])){
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para reenviar a solicitação.');
                if ($hospedagem->user_id !== auth()->id()) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para reenviar a solicitação.');
                }
            }


            // Obtém o usuário autenticado
            $user = Auth::user();

            if($user === null){
                //rota login
                return redirect()->route('login.form')->with('error', 'Você precisa estar logado para aprovar uma solicitação.');
            }
    
            //$hospedagem->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $hospedagem->status = 'pendente';
            $hospedagem->save();


            // Email só para o gestor aprovar/reprovar
            Mail::send('emails.hospedagem_pendente', ['hospedagem' => $hospedagem, 'user' => $user], function ($message) use ($hospedagem,$user) {
                $message->to($hospedagem->email_gestor);
                $message->subject('Solicitação Pendente - Protocolo: ' . $hospedagem->id);

                // anexa se o arquivo foi salvo (disk = public)
                if (!empty($hospedagem->anexo_path) && Storage::disk('public')->exists($hospedagem->anexo_path)) {
                    // full path: storage/app/public/{anexo_path}
                    $message->attach(storage_path('app/public/' . $hospedagem->anexo_path));
                }

            });

            return redirect()->route('reserva.reservas')->with('success5', 'E-mail reenviado com sucesso.')->with('email_gestor', $hospedagem->email_gestor);

        }










        public function cancelarHospedagem($id)
        {
            $hospedagem = Hospedagem::findOrFail($id);
    
            $user = auth()?->user();

            if (!$user) {
                return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
            }

            //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
            if (!$user->temSetor(['suprimentos','admin',])){
                if ($hospedagem->user_id !== auth()->id()) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para cancelar esta hospedagem.');
                }
            }
            
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $hospedagem->status = 'cancelada';
            $hospedagem->save();

            // Obtém o usuário autenticado
            $user = Auth::user();

            // Envia o e-mail de cancelamento
            Mail::send('emails.cancelamento_hospedagem', ['hospedagem' => $hospedagem, 'user' => $user], function($message) use ($user, $hospedagem) {
                $message->to([$hospedagem->email,$hospedagem->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Hospedagem Cancelada');
            });
    
    
            return redirect()->route('reserva.reservas')->with('success', 'Hospedagem cancelada com sucesso.');
        }

        public function finalizarHospedagem($id)
        {
            $hospedagem = Hospedagem::findOrFail($id);
    
            $user = auth()?->user();

            if (!$user) {
                return redirect()->route('login.form')->withErrors('Usuário não autenticado. Por favor, faça login para acessar o resumo financeiro.');
            }

            //if (auth()->user()?->admin == 5 || auth()->user()?->admin == 100){
            if (!$user->temSetor(['suprimentos','admin'])){
                if ($hospedagem->user_id !== auth()->id()) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para cancelar esta hospedagem.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $hospedagem->status = 'finalizada';
            $hospedagem->save();

            // Obtém o usuário autenticado
            $user = Auth::user();

            // Envia o e-mail de cancelamento
            Mail::send('emails.finalizar_hospedagem', ['hospedagem' => $hospedagem, 'user' => $user], function($message) use ($user, $hospedagem) {
                $message->to([$hospedagem->email,$hospedagem->email_gestor,'reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Hospedagem Finalizada');
            });
    
            return redirect()->route('reserva.reservas')->with('success2', 'Hospedagem cancelada com sucesso.');
        }





}
