<?php

namespace App\Http\Controllers;

use App\Models\Financeiro;
use Illuminate\Http\Request;
use App\Models\Filial;
use Illuminate\Support\Facades\Mail;



class FinanceiroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');
        
        return view('financeiro.index', compact('filiais'));
        //
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
            'tipo' => 'nullable|string',
            'pedido' => 'nullable|string',
            'referencia' => 'nullable|string',
            'cnpj' => 'nullable|string',
            'name' => 'nullable|string',
            'pamcard' => 'nullable|string',
            'banco' => 'nullable|string',
            'agencia' => 'nullable|string',
            'conta' => 'nullable|string',
            'pix' => 'nullable|string',
            'favorecido' => 'nullable|string',
            'valor' => 'nullable|string',
            'motivo' => 'nullable|string',
            'filial' => ['required', 'string', 'not_regex:/^\s*$/'],
            'email' => 'required|email',
            'email_gestor' => 'required|email',
            'tipo_pix' => 'nullable|string',
            'placa' => 'nullable|string',
            'prazo' => 'nullable|string',

        ]);

        // dd($validatedData);

        $financeiro = Financeiro::create($validatedData);


        // ENVIAR VÁRIOS E-MAILS

        $emailsString = $request->input('emails');

        $emailsValidos = []; // inicializa como array vazio

        if (!empty($emailsString)) {

            // Divide a string em array usando ';' como separador
            $emailsArray = array_map('trim', explode(';', $emailsString));

            // Filtra apenas e-mails válidos
            $emailsValidos = array_filter($emailsArray, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

            // Opcional: retornar erro se algum e-mail for inválido
            if (count($emailsValidos) !== count($emailsArray)) {
                return back()->withErrors(['emails' => 'Um ou mais e-mails são inválidos.']);
            }
        }
        //FIM

       

        $emails = array_filter([
            ...$emailsValidos,
            $validatedData['email'],
            $validatedData['email_gestor'],
            $validatedData['tipo'] === 'adiantamento' ? 'vitor.marques@grupocargopolo.com.br' : null,
        ], function ($email) {
            return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        });

        //dd($validatedData['tipo']);

        $foto = $request->file('foto');

        if ($validatedData['tipo'] == 'avista' || $validatedData['tipo'] == 'adiantamento'){
            // Envia o email com os dados do formulário
            Mail::send('emails.financeiro', ['dados' => $validatedData], function($message) use ($validatedData, $foto, $financeiro, $emails){
                $message->to(['contasapagar@grupocargopolo.com.br']);
                //$message->to('higor.05@hotmail.com');
                //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                if($validatedData['tipo'] == 'adiantamento'){
                    $message->cc($emails);
                } else {
                    $message->cc($emails);
                }
                if($validatedData['tipo'] == 'avista') { 
                    $message->subject( 'PAGAMENTO A VISTA; PEDIDO: '. $validatedData['pedido'] . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] . ' PLACA: ' . $validatedData['placa'] );
                } elseif ($validatedData['tipo'] == 'adiantamento'){
                    $message->subject( 'ADIANTAMENTO À FORNECEDOR; PEDIDO: '. $validatedData['pedido'] . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] );

                }
                

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
        } else {
                        // Envia o email com os dados do formulário
                        Mail::send('emails.financeiro_reembolso', ['dados' => $validatedData, 'financeiro' => $financeiro,], function($message) use ($validatedData, $foto, $financeiro,$emails){
                            $message->to(['contasapagar@grupocargopolo.com.br']);
                            //$message->to('higor.05@hotmail.com');
                            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                            $message->cc($emails);
                            $message->subject( 'DESPESAS/REEMBOLSO; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] . ' PLACA: ' . $validatedData['placa'] );

                            
            
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
        }
        
        

        // Redirecionar ou retornar uma resposta de sucesso
        return redirect()->route('financeiro.index')->with('success', 'Solicitação realizada com sucesso!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Financeiro $financeiro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Financeiro $financeiro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Financeiro $financeiro)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Financeiro $financeiro)
    {
        //
    }
}
