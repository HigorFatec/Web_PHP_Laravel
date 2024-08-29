<?php

namespace App\Http\Controllers;

use App\Models\Hospedagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;


class HospedagemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reserva.hospedagem');
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
                'volta' => 'nullable|date|after:ida',
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
    
            ]);
    
            
            // Se precisar salvar em um banco de dados, adicione o código aqui
            // Exemplo:
             Hospedagem::create(array_merge(
                $request->all(),
                [
                    'user_name' => $user->name,
                    'user_id' => $user->id,
                    'user_cpf' => $user->cpf,
                    'user_email' => $user->email,
                ]
             ));

             $foto = $request->file('foto');

            // Envia o email com os dados do formulário
            Mail::send('emails.hospedagem', ['dados' => $validatedData, 'user' => $user], function($message) use ($user, $validatedData, $foto){
                $message->to([$validatedData['email_gestor'],'reservas@grupocargopolo.com.br', $user->email ]);
                //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                $message->subject('Nova Hospedagem solicitada');

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
            return redirect()->route('reserva.home')->with('success2', 'Reserva de hospedagem realizada com sucesso!');
        }


        public function cancelarHospedagem($id)
        {
            $hospedagem = Hospedagem::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
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
                $message->to([$hospedagem->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Hospedagem Cancelada');
            });
    
    
            return redirect()->route('reserva.reservas')->with('success', 'Hospedagem cancelada com sucesso.');
        }

        public function finalizarHospedagem($id)
        {
            $hospedagem = Hospedagem::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
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
            Mail::send('emails.cancelamento_hospedagem', ['hospedagem' => $hospedagem, 'user' => $user], function($message) use ($user, $hospedagem) {
                $message->to(['reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Hospedagem Finalizada');
            });
    
            return redirect()->route('reserva.reservas')->with('success2', 'Hospedagem cancelada com sucesso.');
        }





}
