<?php

namespace App\Http\Controllers;

use App\Models\Fiscal;
use Illuminate\Http\Request;
use App\Models\Filial;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\FiscalAprovador;
use Illuminate\Support\Facades\Storage; 


class FiscalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');
        $aprovadores = FiscalAprovador::orderBy('filial')->get(['id','nome','email','filial']);

        return view('fiscal.index', compact('filiais', 'aprovadores'));
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
            'nome_gestor' => 'nullable|string',
            'tipo_pix' => 'nullable|string',
            'placa' => 'nullable|string',
            'prazo' => 'nullable|string',
            'emails' => 'nullable|string',

            'finalidade_da_compra' => 'nullable|string',
            'tipo_de_venda' => 'nullable|string',

        ]);




        $fiscal = Fiscal::create(array_merge(
            $validatedData,
            [
                'approval_token' => Str::uuid(),
                'status' => 'pendente'
            ]
        ));

        // dd($fiscal->approval_token);


        // dd($validatedData);

        // TRATAMENTO DO UPLOAD DO ANEXO
        // STORE
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $file = $request->file('foto');
            $extension = $file->guessExtension() ?? 'bin';
            $filename = time() . '_' . Str::uuid() . '.' . $extension;

            $file->move(storage_path('app/public/fiscais'), $filename);

            $path = 'fiscais/' . $filename;
            $fiscal->update(['anexo_path' => $path]);


            $fiscal->update(['anexo_path' => $path]);
        }
        // FIM ANEXO

        // $fiscal = Fiscal::create($validatedData);



        $produtos = $validatedData['produtos'] ?? [];

        foreach ($produtos as $produto) {
            $fiscal->produtos()->create($produto);
        }

        // --- Buscar o gestor pelo relacionamento ---
        $fiscal->load('gestor');


        //dd($validatedData['tipo']);


        // Email só para o gestor aprovar/reprovar
        Mail::send('emails.fiscal_pendente', ['fiscal' => $fiscal], function ($message) use ($validatedData, $fiscal) {
            $message->to($validatedData['email_gestor']);
            $message->subject('Solicitação Fiscal Pendente - ' . $validatedData['tipo'] . ' - Protocolo: ' . $fiscal->id);

            // anexa se o arquivo foi salvo (disk = public)
            if (!empty($fiscal->anexo_path) && Storage::disk('public')->exists($fiscal->anexo_path)) {
                // full path: storage/app/public/{anexo_path}
                $message->attach(storage_path('app/public/' . $fiscal->anexo_path));
            }

        });

                    
        // Redirecionar ou retornar uma resposta de sucesso
        return redirect()->route('fiscal.index')->with('success', 'Solicitação realizada com sucesso!');
    }



    // APROVAR - gestor clicou no link -> dispara para os setores
    public function aprovar($token)
    {
        $fiscal = Fiscal::where('approval_token', $token)->firstOrFail();

        if ($fiscal->status !== 'pendente') {
            return 'Solicitação já foi processada.';
        }

        $fiscal->update(['status' => 'aprovado']);


        // ENVIAR VÁRIOS E-MAILS

        $emailsString = $fiscal->emails;

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

        $emails = array_filter([
            ...$emailsValidos,
            $fiscal->email,
            $fiscal->email_gestor,
        ], function ($email) {
            return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        });

        //FIM



        // Decide qual e-mail disparar pelo tipo
        if ($fiscal->tipo == 'devolucao') {
            Mail::send('emails.fiscal_devolucao', [
                'fiscal' => $fiscal,
                'produtos' => $fiscal->produtos
            ], function($message) use ($fiscal,$emails){
                $message->to('fiscal@grupocargopolo.com.br');
                //$message->to('higor.05@hotmail.com');
                $message->cc($emails);
                $message->subject('EMISSÃO DE NF - DEVOLUÇÃO; PROTOCOLO: '. $fiscal->id);
            
            if (!empty($fiscal->anexo_path) && Storage::disk('public')->exists($fiscal->anexo_path)) {
                $message->attach(storage_path('app/public/' . $fiscal->anexo_path));
            }
    }
        );
        } elseif ($fiscal->tipo == 'remessa') {
            Mail::send('emails.fiscal_remessa', [
                'fiscal' => $fiscal,
                'produtos' => $fiscal->produtos
            ], function($message) use ($fiscal,$emails){
                $message->to('fiscal@grupocargopolo.com.br');
                $message->cc($emails);
                $message->subject('EMISSÃO DE NF - REMESSA; PROTOCOLO: '. $fiscal->id);

                if (!empty($fiscal->anexo_path) && Storage::disk('public')->exists($fiscal->anexo_path)) {
                    $message->attach(storage_path('app/public/' . $fiscal->anexo_path));
                }
            
            });
        } elseif($fiscal->tipo == 'venda') {
            Mail::send('emails.fiscal_venda', [
                'fiscal' => $fiscal,
                'produtos' => $fiscal->produtos
            ], function($message) use ($fiscal,$emails){
                $message->to('fiscal@grupocargopolo.com.br');
                $message->cc($emails);
                $message->subject('EMISSÃO DE NF - VENDA; PROTOCOLO: '. $fiscal->id);

                if (!empty($fiscal->anexo_path) && Storage::disk('public')->exists($fiscal->anexo_path)) {
                    $message->attach(storage_path('app/public/' . $fiscal->anexo_path));
                }
            });
        } elseif($fiscal->tipo == 'descarte') {
            Mail::send('emails.fiscal_descarte', [
                'fiscal' => $fiscal,
                'produtos' => $fiscal->produtos
            ], function($message) use ($fiscal,$emails){
                $message->to('fiscal@grupocargopolo.com.br');
                $message->cc($emails);
                $message->subject('EMISSÃO DE NF - DESCARTE; PROTOCOLO: '. $fiscal->id);
                            if (!empty($fiscal->anexo_path) && Storage::disk('public')->exists($fiscal->anexo_path)) {
                $message->attach(storage_path('app/public/' . $fiscal->anexo_path));
            }
            });
        }
        // idem para venda e descarte

        return 'Solicitação aprovada com sucesso!';
    }



    public function reprovar($token)
    {
        $fiscal = Fiscal::where('approval_token', $token)->firstOrFail();

        if ($fiscal->status !== 'pendente') {
            return 'Solicitação já foi processada.';
        }

        $fiscal->update(['status' => 'reprovado']);

        Mail::send('emails.fiscal_reprovado', ['fiscal' => $fiscal], function($message) use ($fiscal){
            $message->to($fiscal->email);
            $message->subject('Solicitação Reprovada - Protocolo: ' . $fiscal->id);
        });

        return 'Solicitação reprovada com sucesso!';
    }


}
