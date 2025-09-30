<?php

namespace App\Http\Controllers;

use App\Models\Fiscal;
use Illuminate\Http\Request;
use App\Models\Filial;
use Illuminate\Support\Facades\Mail;



class FiscalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');
        
        return view('fiscal.index', compact('filiais'));
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
            'empresa_solicitante' => 'nullable|string',
            'cnpj' => 'nullable|string',
            'fornecedor' => 'nullable|string',
            'cnpj_fornecedor' => 'nullable|string',
            'nota_fiscal' => 'nullable|string',
            'quantidade_itens' => 'nullable|string',
            'codigo_rodopar_item' => 'nullable|string',
            'valor_devolucao' => 'nullable|string',
            'valor_nf' => 'nullable|string',
            'mercadoria_devolvida' => 'nullable|string',
            'motivo_operacao' => 'nullable|string',
            'informacoes_adicionais' => 'nullable|string',
            'cliente' => 'nullable|string',
            'cnpj_cliente' => 'nullable|string',
            'codigo_fornecedor_rodopar' => 'nullable|string',

            'produtos' => 'nullable|array|min:1',
            'produtos.*.quantidade' => 'nullable|string',
            'produtos.*.codigo_rodopar' => 'nullable|string',
            'produtos.*.valor_unitario' => 'nullable|string',

            'filial' => ['required', 'string', 'not_regex:/^\s*$/'],
            'email' => 'required|email',
            'email_gestor' => 'required|email',
            'tipo_pix' => 'nullable|string',
            'placa' => 'nullable|string',
            'prazo' => 'nullable|string',

            'finalidade_da_compra' => ['required', 'string', 'not_regex:/^\s*$/'],


        ]);

        // dd($validatedData);

        $fiscal = Fiscal::create($validatedData);

        $produtos = $validatedData['produtos'] ?? [];

        foreach ($produtos as $produto) {
            $fiscal->produtos()->create($produto);
        }


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
        ], function ($email) {
            return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        });

        //dd($validatedData['tipo']);

        $foto = $request->file('foto');

        if ($validatedData['tipo'] == 'devolucao' ){
            // Envia o email com os dados do formulário
            Mail::send('emails.fiscal_devolucao', ['dados' => $validatedData, 'fiscal' => $fiscal,'produtos' => $produtos], function($message) use ($validatedData, $foto, $fiscal, $emails){
                $message->to(['fiscal@grupocargopolo.com.br']);
                //$message->to('higor.05@hotmail.com');
                //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                $message->cc($emails);

                    $message->subject( 'EMISSÃO DE NF - DEVOLUÇÃO; PROTOCOLO:'. $fiscal->id  . ' FORNECEDOR: ' . $validatedData['fornecedor'] . ' FILIAL: ' . $validatedData['filial'] . ' EMPRESA: ' . $validatedData['empresa_solicitante'] );


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
        } elseif ($validatedData['tipo'] == 'remessa' ){
                        // Envia o email com os dados do formulário
                        Mail::send('emails.fiscal_remessa', ['dados' => $validatedData, 'fiscal' => $fiscal,'produtos' => $produtos], function($message) use ($validatedData, $fiscal,$emails){
                            $message->to(['fiscal@grupocargopolo.com.br']);
                            // $message->to('higor.05@hotmail.com');
                            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                            $message->cc($emails);
                            $message->subject( 'EMISSÃO DE NF - REMESSA; PROTOCOLO: '. $fiscal->id . ' FORNECEDOR: ' . $validatedData['fornecedor'] . ' FILIAL: ' . $validatedData['filial'] . ' EMPRESA: ' . $validatedData['empresa_solicitante'] );
            
                        });
        } elseif ($validatedData['tipo'] == 'venda' ){
                        // Envia o email com os dados do formulário
                        Mail::send('emails.fiscal_venda', ['dados' => $validatedData, 'fiscal' => $fiscal,'produtos' => $produtos], function($message) use ($validatedData, $fiscal,$emails){
                            $message->to(['fiscal@grupocargopolo.com.br']);
                            // $message->to('higor.05@hotmail.com');
                            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                            $message->cc($emails);
                            $message->subject( 'EMISSÃO DE NF - VENDA; PROTOCOLO: '. $fiscal->id . ' CLIENTE: ' . $validatedData['cliente'] . ' FILIAL: ' . $validatedData['filial'] . ' EMPRESA: ' . $validatedData['empresa_solicitante'] );
            
                        });

                                
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
        
        
        } else {
                        // Envia o email com os dados do formulário
                        Mail::send('emails.fiscal_descarte', ['dados' => $validatedData, 'fiscal' => $fiscal,'produtos' => $produtos], function($message) use ($validatedData, $fiscal,$emails){
                            $message->to(['fiscal@grupocargopolo.com.br','alef.bondezan@grupocargopolo.com.br']);
                            // $message->to('higor.05@hotmail.com');
                            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                            $message->cc($emails);
                            $message->subject( 'EMISSÃO DE NF - DESCARTE; PROTOCOLO: '. $fiscal->id . ' FORNECEDOR: ' . $validatedData['fornecedor'] . ' FILIAL: ' . $validatedData['filial'] . ' EMPRESA: ' . $validatedData['empresa_solicitante'] );
            
                        });
                    }
        // Redirecionar ou retornar uma resposta de sucesso
        return redirect()->route('fiscal.index')->with('success', 'Solicitação realizada com sucesso!');
    }


    /**
     * Display the specified resource.
     */
    public function show(fiscal $fiscal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(fiscal $fiscal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, fiscal $fiscal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(fiscal $fiscal)
    {
        //
    }
}
