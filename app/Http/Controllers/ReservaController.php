<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Veiculo;
use App\Models\Hospedagem;
use App\Models\Adiantamento;
use App\Models\Filial;
use App\Models\UsersGestores;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 

use Illuminate\Support\Facades\Mail;


class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reserva.home');
    }

    public function passagemAerea()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');
        $aprovadores = UsersGestores::orderBy('nome')->get(['id','nome','email','operacao']);

        return view('reserva.passagem-aerea', compact('filiais', 'aprovadores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function minhasPassagens()
    {

        if (auth()->user()->admin == 1){
            $passagens = Reserva::where('status','=','ok')->orderBy('created_at')->paginate(3);
            $veiculos = Veiculo::where('status','=','ok')->orderBy('created_at')->paginate(3);
            $hospedagem = Hospedagem::where('status','=','ok')->orderBy('created_at')->paginate(3);
            $adiantamento = Adiantamento::where('status','=','ok')->orderBy('created_at')->paginate(3);


        } elseif (auth()->user()->admin == 100) {

            $passagens = Reserva::where('status','=','ok')->orderBy('created_at')->paginate(3);
            $veiculos = Veiculo::where('status','=','ok')->orderBy('created_at')->paginate(3);
            $hospedagem = Hospedagem::where('status','=','ok')->orderBy('created_at')->paginate(3);
            $adiantamento = Adiantamento::where('status','=','ok')->orderBy('created_at')->paginate(3);


        } else {
            $passagens = Reserva::where('user_id', auth()->id())
                                ->where('status', 'ok')
                                ->where('ida', '>=', Carbon::now())
                                ->orderBy('created_at', 'desc')
                                ->paginate(3);

            $veiculos = Veiculo::where('user_id', auth()->id())
                                ->where('status', 'ok')
                                ->where('ida', '>=', Carbon::now())
                                ->orderBy('created_at', 'desc')
                                ->paginate(3);

            $hospedagem = Hospedagem::where('user_id', auth()->id())
                                ->where('status', 'ok')
                                ->where('ida', '>=', Carbon::now())
                                ->orderBy('created_at', 'desc')
                                ->paginate(3);
            $adiantamento = Adiantamento::where('user_id', auth()->id())
                                ->where('status', 'ok')
                                ->where('ida', '>=', Carbon::now())
                                ->orderBy('created_at', 'desc')
                                ->paginate(3);


        }

        return view('reserva.reservas', compact('passagens', 'veiculos', 'hospedagem', 'adiantamento'));
    }

    public function reservas_pendentes()
    {
        if (auth()->user()->admin == 1 || auth()->user()->admin == 100){
            $pendentes = Reserva::where('status','=','pendente')->orderBy('created_at')->paginate(3);
            $pendente_hospedagem = Hospedagem::where('status','=','pendente')->orderBy('created_at')->paginate(3);
        }

        return view('reserva.reservas_pendentes', compact('pendentes', 'pendente_hospedagem'));
    }


    public function canceladas()
    {
        if (auth()->user()->admin == 1 || auth()->user()->admin == 100){
            $passagens = Reserva::where('status','=','cancelada')->orderBy('created_at')->paginate(3);
            $veiculos = Veiculo::where('status','=','cancelada')->orderBy('created_at')->paginate(3);
            $hospedagem = Hospedagem::where('status','=','cancelada')->orderBy('created_at')->paginate(3);
            $adiantamento = Adiantamento::where('status','=','cancelada')->orderBy('created_at')->paginate(3);
        }

        return view('admin.canceladas', compact('passagens', 'veiculos', 'hospedagem', 'adiantamento'));
    }

    public function finalizadas()
    {
        if (auth()->user()->admin == 1 || auth()->user()->admin == 100){
            $passagens = Reserva::where('status','=','finalizada')->orderBy('created_at')->paginate(3);
            $veiculos = Veiculo::where('status','=','finalizada')->orderBy('created_at')->paginate(3);
            $hospedagem = Hospedagem::where('status','=','finalizada')->orderBy('created_at')->paginate(3);
            $adiantamento = Adiantamento::where('status','=','finalizada')->orderBy('created_at')->paginate(3);
        }

        return view('admin.finalizadas', compact('passagens', 'veiculos', 'hospedagem', 'adiantamento'));
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
                'origem' => 'required|string',
                'destino' => 'required|string',
                'tipo' => 'required|string',
                'ida' => 'required|date',
                'volta' => 'nullable|date|after_or_equal:ida',
                'embarque' => 'required|string',
                'motivo' => 'required|string',
                'validacao' => 'required|string',
                'email_gestor' => 'required|email',
                'observacoes' => 'nullable|string',
                'nome' => 'required|string',
                'cpf' => 'required|numeric',
                'rg' => 'required|numeric',
                'data_nascimento' => 'required|date',
                'email' => 'required|email',
                'filial_viajante' => 'required|string',
            ],['tipo.required' => 'Selecione o TIPO de passagem (AEREA ou RODOVIÁRIA) obs: É obrigatório.',
        ]);
    
            
            // Se precisar salvar em um banco de dados, adicione o código aqui
            // Exemplo:
             $reserva = Reserva::create(array_merge(
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
                $reserva->update(['anexo_path' => $path]);


                $reserva->update(['anexo_path' => $path]);
            }
            // FIM ANEXO
            // Verifique se o arquivo foi capturado
            //dd($foto);
            $reserva->load('gestor');

            // Email só para o gestor aprovar/reprovar
            Mail::send('emails.reserva_pendente', ['reserva' => $reserva, 'user' => $user], function ($message) use ($validatedData, $reserva, $user) {
                $message->to($validatedData['email_gestor']);
                $message->subject('Solicitação Pendente de Reserva ' . $validatedData['tipo'] . ' - Protocolo: ' . $reserva->id);

                // anexa se o arquivo foi salvo (disk = public)
                if (!empty($reserva->anexo_path) && Storage::disk('public')->exists($reserva->anexo_path)) {
                    // full path: storage/app/public/{anexo_path}
                    $message->attach(storage_path('app/public/' . $reserva->anexo_path));
                }

            });

            Mail::send('emails.reserva_solicitacao', ['reserva' => $reserva, 'user' => $user], function ($message) use ($validatedData, $reserva, $user) {
                $message->to($validatedData['email']);
                $message->subject('Confirmação de Solicitação de Reserva - ' . $validatedData['tipo'] . ' - Protocolo: ' . $reserva->id);

            // anexa se o arquivo foi salvo (disk = public)
            if (!empty($reserva->anexo_path) && Storage::disk('public')->exists($reserva->anexo_path)) {
                // full path: storage/app/public/{anexo_path}
                $message->attach(storage_path('app/public/' . $reserva->anexo_path));
            }

        });

            // Envia o email com os dados do formulário
            // Mail::send('emails.passagem', ['dados' => $reserva, 'user' => $user], function($message) use ($user, $reserva, $foto){
            //     $message->to([$reserva['email'],$reserva['email_gestor'],'reservas@grupocargopolo.com.br', $user->email ]);
                
            //     //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
            //     $message->subject('Nova Reserva de Passagem '. $reserva['tipo'] .' Solicitada');

            //     //Verificar se existe imagem anexada
            //     if ($foto)  {
            //         $pathToFile = $foto->getPathname();
            //         $filename = $foto->getClientOriginalName();
            //         $message->attach($pathToFile, [
            //             'as' => $filename, // Nome do arquivo que será mostrado no email
            //             'mime' => $foto->getClientMimeType(), // Tipo MIME do arquivo
            //         ]);
            //     }
            // });
    
            // Redirecionar ou retornar uma resposta de sucesso
            return redirect()->route('reserva.home')->with('success2', 'Reserva de veiculo realizada com sucesso!');
            
        }


        public function cancelarPassagem($id)
        {
            $reserva = Reserva::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($reserva->user_id !== auth()->id()) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para cancelar este veiculo.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $reserva->status = 'cancelada';
            $reserva->save();

                    // Obtém o usuário autenticado
        $user = Auth::user();

        // Envia o e-mail de cancelamento
        Mail::send('emails.cancelamento_reserva', ['reserva' => $reserva, 'user' => $user], function($message) use ($user, $reserva) {
            $message->to([$reserva->email,$reserva->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
            $message->subject('Reserva de Passagem Cancelada');
        });
    
    
        return redirect()->route('reserva.reservas')->with('success', 'Veiculo cancelada com sucesso.');
        }

        public function finalizarPassagem($id)
        {
            $reserva = Reserva::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($reserva->user_id !== auth()->id()) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para finalizar esta reserva.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $reserva->status = 'finalizada';
            $reserva->save();

            // Obtém o usuário autenticado
            $user = Auth::user();

            // Envia o e-mail de finalização
            Mail::send('emails.finalizar_reserva', ['reserva' => $reserva, 'user' => $user], function($message) use ($user, $reserva) {
                $message->to([$reserva->email,$reserva->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Reserva Finalizada');
            });

            return redirect()->route('reserva.reservas')->with('success2', 'Veiculo finalizado com sucesso.');

        }




    // APROVAR - gestor clicou no link -> dispara para os setores
    public function aprovar($token)
    {
        $reserva = Reserva::where('approval_token', $token)->firstOrFail();

        if ($reserva->status !== 'pendente') {
            return 'Solicitação já foi processada.';
        }

        // Obtém o usuário autenticado
        $user = Auth::user();

        if($user === null){
            //rota login
            return redirect()->route('login.form')->with('error', 'Você precisa estar logado para aprovar uma solicitação.');
        }


        // Decide qual e-mail disparar pelo tipo

        Mail::send('emails.passagem', [
                'reserva' => $reserva, 'user' => $user
            ], function($message) use ($reserva,$user){
                $message->to('reservas@grupocargopolo.com.br');
                $message->cc([$user->email,$reserva->email,$reserva->email_gestor]);
                //$message->to('higor.05@hotmail.com');
                $message->subject('Nova Reserva de Passagem '. $reserva['tipo'] .' Solicitada');

            if (!empty($reserva->anexo_path) && Storage::disk('public')->exists($reserva->anexo_path)) {
                $message->attach(storage_path('app/public/' . $reserva->anexo_path));
            }
    }
        );
        
        $reserva->update(['status' => 'ok']);

        // idem para venda e descarte

        return 'Solicitação aprovada com sucesso!';
    }



    public function reprovar($token)
    {
        $reserva = Reserva::where('approval_token', $token)->firstOrFail();

        if ($reserva->status !== 'pendente') {
            return 'Solicitação já foi processada.';
        }

        
        // Obtém o usuário autenticado
        $user = Auth::user();

        if($user === null){
            //rota login
            return redirect()->route('login.form')->with('error', 'Você precisa estar logado para reprovar uma solicitação.');
        }


        Mail::send('emails.reserva_reprovado', ['reserva' => $reserva, 'user' => $user], function($message) use ($reserva, $user){
            $message->to($reserva->email);
            $message->cc($reserva->email_gestor);
            $message->subject('Solicitação Reprovada - Protocolo: ' . $reserva->id);
        });

        $reserva->update(['status' => 'reprovado']);


        return 'Solicitação reprovada com sucesso!';
    }



public function reenviar_pendencia($id)
        {
            //encontrar em reserva, hospedagem , veiculo ou adiantamento
            $reserva = Reserva::findOrFail($id);

    
            if (auth()->user()->admin !== 1 || auth()->user()->admin !== 100) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para reenviar a solicitação.');
                if ($reserva->user_id !== auth()->id()) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para reenviar a solicitação.');
                }
            }


            // Obtém o usuário autenticado
            $user = Auth::user();

            if($user === null){
                //rota login
                return redirect()->route('login.form')->with('error', 'Você precisa estar logado para aprovar uma solicitação.');
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $reserva->status = 'pendente';
            $reserva->save();


            // Email só para o gestor aprovar/reprovar
            Mail::send('emails.reserva_pendente', ['reserva' => $reserva, 'user' => $user], function ($message) use ($reserva,$user) {
                $message->to($reserva->email_gestor);
                $message->subject('Solicitação Pendente - Protocolo: ' . $reserva->id);

                // anexa se o arquivo foi salvo (disk = public)
                if (!empty($reserva->anexo_path) && Storage::disk('public')->exists($reserva->anexo_path)) {
                    // full path: storage/app/public/{anexo_path}
                    $message->attach(storage_path('app/public/' . $reserva->anexo_path));
                }

            });

            return redirect()->route('reserva.reservas')->with('success5', 'E-mail reenviado com sucesso.')->with('email_gestor', $reserva->email_gestor);

        }


    }