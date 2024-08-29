<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;


class VeiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reserva.veiculo');
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
                'origem' => 'required|string',
                'destino' => 'required|string',
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
             Veiculo::create(array_merge(
                $request->all(),
                [
                    'user_name' => $user->name,
                    'user_id' => $user->id,
                    'user_cpf' => $user->cpf,
                    'user_email' => $user->email,
                ]
             ));

            $foto = $request->file('imagem');

            // Envia o email com os dados do formulário
            Mail::send('emails.veiculo', ['dados' => $validatedData, 'user' => $user], function($message) use ($user, $validatedData, $foto){
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


        public function cancelarVeiculo($id)
        {
            $veiculo = Veiculo::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($veiculo->user_id !== auth()->id()) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para cancelar este veiculo.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $veiculo->status = 'cancelada';
            $veiculo->save();

            // Obtém o usuário autenticado
            $user = Auth::user();

            // Envia o e-mail de cancelamento
            Mail::send('emails.cancelamento_veiculo', ['veiculo' => $veiculo, 'user' => $user], function($message) use ($user, $veiculo) {
                $message->to([$veiculo->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Reserva de Veiculo Cancelada');
            });
    
    
            return redirect()->route('reserva.reservas')->with('success', 'Veiculo cancelada com sucesso.');
        }

        public function finalizarVeiculo($id)
        {
            $veiculo = Veiculo::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($veiculo->user_id !== auth()->id()) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para cancelar este veiculo.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $veiculo->status = 'finalizada';
            $veiculo->save();
    
            // Obtém o usuário autenticado
            $user = Auth::user();

            // Envia o e-mail de cancelamento
            Mail::send('emails.cancelamento_veiculo', ['veiculo' => $veiculo, 'user' => $user], function($message) use ($user, $veiculo) {
                $message->to(['reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Reserva de Veiculo Finalizada');
            });

            return redirect()->route('reserva.reservas')->with('success2', 'Veiculo cancelada com sucesso.');
        }

}