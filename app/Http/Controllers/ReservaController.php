<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Veiculo;
use App\Models\Hospedagem;
use App\Models\Adiantamento;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        return view('reserva.passagem-aerea');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function minhasPassagens()
    {
        if (auth()->user()->admin == 0) {
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

        elseif (auth()->user()->admin == 1){
        $passagens = Reserva::where('status','=','ok')->orderBy('created_at')->paginate(3);
        $veiculos = Veiculo::where('status','=','ok')->orderBy('created_at')->paginate(3);
        $hospedagem = Hospedagem::where('status','=','ok')->orderBy('created_at')->paginate(3);
        $adiantamento = Adiantamento::where('status','=','ok')->orderBy('created_at')->paginate(3);
        
        }

        return view('reserva.reservas', compact('passagens', 'veiculos', 'hospedagem', 'adiantamento'));
    }
    public function canceladas()
    {
        if (auth()->user()->admin == 1){
            $passagens = Reserva::where('status','=','cancelada')->orderBy('created_at')->paginate(3);
            $veiculos = Veiculo::where('status','=','cancelada')->orderBy('created_at')->paginate(3);
            $hospedagem = Hospedagem::where('status','=','cancelada')->orderBy('created_at')->paginate(3);
            $adiantamento = Adiantamento::where('status','=','cancelada')->orderBy('created_at')->paginate(3);
        }

        return view('admin.canceladas', compact('passagens', 'veiculos', 'hospedagem', 'adiantamento'));
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
                'volta' => 'nullable|date|after:ida',
                'motivo' => 'required|string',
                'validacao' => 'required|string',
                'email_gestor' => 'required|email',
                'observacoes' => 'nullable|string',
                'nome' => 'required|string',
                'cpf' => 'required|numeric',
                'rg' => 'required|numeric',
                'data_nascimento' => 'required|date',
                'email' => 'required|email',
    
            ]);
    
            
            // Se precisar salvar em um banco de dados, adicione o código aqui
            // Exemplo:
             Reserva::create(array_merge(
                $request->all(),
                [
                    'user_name' => $user->name,
                    'user_id' => $user->id,
                    'user_cpf' => $user->cpf,
                    'user_email' => $user->email,
                ]
             ));

            $foto = $request->file('foto');

            // Verifique se o arquivo foi capturado
            //dd($foto);

            // Envia o email com os dados do formulário
            Mail::send('emails.passagem', ['dados' => $validatedData, 'user' => $user], function($message) use ($user, $validatedData, $foto){
                $message->to([$validatedData['email_gestor'],'reservas@grupocargopolo.com.br', $user->email ]);
                //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                $message->subject('Nova Reserva de Veículo solicitada');

                //Verificar se existe imagem anexada
                if ($foto)  {
                    $pathToFile = $foto->getPathname();
                    $filename = $foto->getClientOriginalName();
                    $message->attach($pathToFile, [
                        'as' => $filename, // Nome do arquivo que será mostrado no email
                        'mime' => $foto->getClientMimeType(), // Tipo MIME do arquivo
                    ]);
                }
            });
    
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
            $message->to([$reserva->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
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
            Mail::send('emails.cancelamento_reserva', ['reserva' => $reserva, 'user' => $user], function($message) use ($user, $reserva) {
                $message->to(['reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Reserva Finalizada');
            });

            return redirect()->route('reserva.reservas')->with('success2', 'Veiculo finalizado com sucesso.');

        }

    }