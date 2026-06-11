<?php

namespace App\Http\Controllers;

use App\Models\Adiantamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Mail;
use App\Models\Filial;
use App\Models\FornecedorFinanceiro;
use App\Models\UnidadesNegocio;
use App\Models\GestorFinanceiro;
use DB;


class AdiantamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiais = UnidadesNegocio::orderBy('unidade_negocio')->get();

        $cidades = FornecedorFinanceiro::cidades();

        return view('reserva.adiantamento', compact('filiais','cidades'));
    }


    public function buscarFornecedores(Request $request)
    {
        $search = $request->search;

        $fornecedores = Adiantamento::fornecedoresQuery($search);

        return response()->json($fornecedores);
    }


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
                'validacao' => 'required|string',
                'email_gestor' => 'required|email',
                'observacoes' => 'nullable|string',
                'nome' => 'required|string',
                'cpf' => 'required|string',
                'rg' => 'required|numeric',
                'data_nascimento' => 'required|date',
                'email' => 'required|email',
                'banco' => 'required|string',
                'agencia' => 'required|numeric',
                'conta' => 'required|string',
                'tipo_conta' => 'required|string',
                'titular' => 'required|string',
                'pix'=> 'required|string',
                'filial_viajante' => 'nullable|string',

                'fornecedor' => ['required', 'string', 'not_regex:/^\s*$/'],
                'cod_unidade' => ['required', 'string', 'not_regex:/^\s*$/'],
                'cod_custo' => ['required', 'string', 'not_regex:/^\s*$/'],
                'cod_gasto' => ['required', 'string', 'not_regex:/^\s*$/'],
                'gestor_aprovador' => ['required', 'string', 'not_regex:/^\s*$/'],
                'anexo_path' => 'nullable|string',
                'valor' => 'nullable|numeric',
            ],[
                'valor.numeric' => 'O campo valor deve ser um número válido (ex: 1250.50).',
            ]);

            // $ida = Carbon::parse($validatedData['ida']);
            // $volta = Carbon::parse($validatedData['volta']);

            // if ($volta->diffInDays($ida) < 5){
            //     return redirect()->route('reserva.adiantamento')->with('error_dias', 'A data de volta deve ser maior que a data de ida.');
            // }

            if(Adiantamento::where('fornecedor', $validatedData['fornecedor'])->where('status', 'aprovado')->exists()){
                return back()->withErrors('Você já possui uma solicitação de adiantamento realizada. Por favor, faça uma nova solicitação somente após a finalização da anterior.');
            }
            
            // Se precisar salvar em um banco de dados, adicione o código aqui
            // Exemplo:
             $adiantamento = Adiantamento::create(array_merge(
                $request->all(),
                [
                    'user_name' => $user->name,
                    'user_filial' => $user->filial,
                    'user_id' => $user->id,
                    'user_cpf' => $user->cpf,
                    'user_email' => $user->email,
                    'approval_token' => Str::uuid(),
                    'status' => 'pendente'
                ]
             ));


            $adiantamento->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            $gestor = $adiantamento->unidadeAprovadora?->gestorRegional;

            if (!$gestor) {
                return back()->withErrors('Gestor regional não encontrado para esta unidade.');
            }


            // LOGICA PARA SALDO
            // $calculo = $gestor->saldo - $adiantamento->valor;

            // if ($calculo < 0) {
            //     return back()->withErrors('Saldo insuficiente do gestor para aprovar esta solicitação.');
            // } else {
            //     $gestor->saldo = $calculo;
            //     $gestor->save();
            // }



            // TRATAMENTO DO UPLOAD DO ANEXO
            // STORE
            if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
                $file = $request->file('foto');
                $extension = $file->guessExtension() ?? 'bin';
                $filename = time() . '_' . Str::uuid() . '.' . $extension;

                $file->move(storage_path('app/public/adiantamento'), $filename);

                $path = 'adiantamento/' . $filename;
                $adiantamento->update(['anexo_path' => $path]);


                $adiantamento->update(['anexo_path' => $path]);
            }
            // FIM ANEXO


            // Email só para o gestor aprovar/reprovar
            Mail::send('emails.adiantamento_pendente', ['adiantamento' => $adiantamento], function ($message) use ($validatedData, $adiantamento) {
                $message->to($validatedData['gestor_aprovador']);
                $message->subject('Solicitação Adiantamento Pendente - Protocolo: ' . $adiantamento->id);
                
                // anexa se o arquivo foi salvo (disk = public)
                if (!empty($adiantamento->anexo_path) && Storage::disk('public')->exists($adiantamento->anexo_path)) {
                    // full path: storage/app/public/{anexo_path}
                    $message->attach(storage_path('app/public/' . $adiantamento->anexo_path));
                }

            });

            Mail::send('emails.adiantamento_solicitacao', ['adiantamento' => $adiantamento], function ($message) use ($validatedData, $adiantamento) {
                $message->to($validatedData['email']);
                $message->subject('Confirmação de Solicitação Adiantamento - Protocolo: ' . $adiantamento->id);

                // anexa se o arquivo foi salvo (disk = public)
                if (!empty($adiantamento->anexo_path) && Storage::disk('public')->exists($adiantamento->anexo_path)) {
                    // full path: storage/app/public/{anexo_path}
                    $message->attach(storage_path('app/public/' . $adiantamento->anexo_path));
                }

            });
            return redirect()->route('reserva.adiantamento')->with('success', 'Solicitação realizada com sucesso!');

        }



        // APROVAR - gestor clicou no link -> dispara para os setores
        public function aprovar($token)
        {
            $adiantamento = Adiantamento::where('approval_token', $token)->firstOrFail();

            if ($adiantamento->status !== 'pendente') {
                return 'Solicitação já foi processada.';
            }


            $adiantamento->load('unidades', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            
            $adiantamento->update(['id_raz' => DB::connection('sqlsrv')->table('BANRAZ')->max('ID_RAZ') + 1]);


            // Decide qual e-mail disparar pelo tipo
                Mail::send('emails.adiantamento', [
                    'adiantamento' => $adiantamento,
                ], function($message) use ($adiantamento){
                    $message->to('contasapagar@grupocargopolo.com.br');
                    //$message->to('higor.machado@grupocargopolo.com.br');
                    $message->cc([$adiantamento->email_gestor,$adiantamento->email,$adiantamento->gestor_aprovador,$adiantamento->unidadeAprovadora?->gestorRegional?->email_gestor]);
                    
                    $message->subject( 'ADIANTAMENTO; PROTOCOLO:'. $adiantamento->id  . ' FUNCIONARIO: ' . $adiantamento->name . ' FILIAL: ' . $adiantamento->unidades->unidade_negocio);

                
                if (!empty($adiantamento->anexo_path) && Storage::disk('public')->exists($adiantamento->anexo_path)) {
                    $message->attach(storage_path('app/public/' . $adiantamento->anexo_path));
                }
        }
            );
    

            $adiantamento->financeiroAvista($adiantamento->id, $adiantamento->valor, $adiantamento->solicitante, $adiantamento->fornecedor, $adiantamento->unidadeAprovadora->nome_gestor, $adiantamento->cod_unidade, '39020-5', $adiantamento->cod_unidade, $adiantamento->cod_custo, $adiantamento->cod_gasto);
            //$financeiro->update(['id_raz' => DB::connection('sqlsrv')->table('BANRAZ')->max('ID_RAZ')]);
            

            $adiantamento->update(['status' => 'aprovado']);

            return 'Solicitação de Adiantamento aprovada com sucesso.';
        }


        public function reprovar(Request $request, $token)
        {

            $adiantamento = Adiantamento::where('approval_token', $token)->firstOrFail();

            if ($adiantamento->status !== 'pendente') {
                return back()->withErrors('Solicitação já foi processada.');
            }

            $request->validate([
                'motivo' => 'required|string|min:5'
            ]);

            $adiantamento->motivo_reprovacao = $request->motivo;
            $adiantamento->save();



            Mail::send('emails.adiantamento_reprovado', ['adiantamento' => $adiantamento], function($message) use ($adiantamento){
                $message->to($adiantamento->email);
                $message->cc([$adiantamento->gestor_aprovador]);
                $message->subject('Solicitação Reprovada - Protocolo: ' . $adiantamento->id);
            });


            $adiantamento->update(['status' => 'reprovado']);

            return 'Solicitação de Adiantamento reprovada com sucesso.';
        }


        public function formReprovar($token)
        {
            $adiantamento = Adiantamento::where('approval_token', $token)->firstOrFail();

            return view('reserva.adiantamento_reprovar', compact('adiantamento'));
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
                $message->to([$adiantamento->email,$adiantamento->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
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
            Mail::send('emails.finalizar_adiantamento', ['adiantamento' => $adiantamento, 'user' => $user], function($message) use ($user, $adiantamento) {
                $message->to([$adiantamento->email,$adiantamento->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
                $message->subject('Adiantamento Finalizado');
            });
    
    
            return redirect()->route('reserva.reservas')->with('success2', 'Adiantamento cancelado com sucesso.');
        }

}
