<?php

namespace App\Http\Controllers;

use App\Models\Adiantamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;


class AdiantamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reserva.adiantamento');
    }

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
                'validacao' => 'required|string',
                'email_gestor' => 'required|email',
                'observacoes' => 'nullable|string',
                'nome' => 'required|string',
                'cpf' => 'required|numeric',
                'rg' => 'required|numeric',
                'data_nascimento' => 'required|date',
                'email' => 'required|email',
                'banco' => 'required|string',
                'agencia' => 'required|numeric',
                'conta' => 'required|numeric',
                'tipo_conta' => 'required|string',
                'titular' => 'required|string',
                'pix'=> 'required|string',
    
            ]);

            $ida = Carbon::parse($validatedData['ida']);
            $volta = Carbon::parse($validatedData['volta']);

            if ($volta->diffInDays($ida) < 5){
                return redirect()->route('reserva.adiantamento')->with('error_dias', 'A data de volta deve ser maior que a data de ida.');
            }
    
            
            // Se precisar salvar em um banco de dados, adicione o código aqui
            // Exemplo:
             Adiantamento::create(array_merge(
                $request->all(),
                [
                    'user_name' => $user->name,
                    'user_filial' => $user->filial,
                    'user_id' => $user->id,
                    'user_cpf' => $user->cpf,
                    'user_email' => $user->email,
                ]
             ));

            $foto = $request->file('foto');


            // Envia o email com os dados do formulário
            Mail::send('emails.adiantamento', ['dados' => $validatedData, 'user' => $user], function($message) use ($user, $validatedData, $foto){
                $message->to([$validatedData['email_gestor'],'reservas@grupocargopolo.com.br', $user->email ]);
                //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                $message->subject('Novo Adiantamento solicitado');
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
            return redirect()->route('reserva.home')->with('success2', 'Adiantamento realizado com sucesso!');
        }


        public function cancelarAdiantamento($id)
        {
            $adiantamento = Adiantamento::findOrFail($id);

            if (auth()->user()->admin == 0) {
                if ($adiantamento->user_id !== auth()->id()) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para cancelar este adiantamento.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $adiantamento->status = 'cancelada';
            $adiantamento->save();

            // Obtém o usuário autenticado
            $user = Auth::user();

            // Envia o e-mail de cancelamento
            Mail::send('emails.cancelamento_adiantamento', ['adiantamento' => $adiantamento, 'user' => $user], function($message) use ($user, $adiantamento) {
                $message->to([$adiantamento->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Adiantamento Cancelado');
            });
    
    
            return redirect()->route('reserva.reservas')->with('success', 'Adiantamento cancelado com sucesso.');
        }

        public function finalizarAdiantamento($id)
        {
            $adiantamento = Adiantamento::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($adiantamento->user_id !== auth()->id()) {
                    return redirect()->route('reserva.reservas')->with('error', 'Você não tem permissão para cancelar este adiantamento.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $adiantamento->status = 'finalizada';
            $adiantamento->save();

            // Obtém o usuário autenticado
            $user = Auth::user();

            // Envia o e-mail de cancelamento
            Mail::send('emails.cancelamento_adiantamento', ['adiantamento' => $adiantamento, 'user' => $user], function($message) use ($user, $adiantamento) {
                $message->to(['reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Adiantamento Finalizado');
            });
    
    
            return redirect()->route('reserva.reservas')->with('success2', 'Adiantamento cancelado com sucesso.');
        }

}
