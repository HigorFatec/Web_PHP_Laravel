<?php

namespace App\Http\Controllers;

use App\Models\Fiscal;
use Illuminate\Http\Request;
use App\Models\Filial;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\FiscalAprovador;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Auth;


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
        Mail::send('emails.fiscal_pendente', ['fiscal' => $fiscal, 'produtos' => $fiscal->produtos], function ($message) use ($validatedData, $fiscal) {
            $message->to($validatedData['email_gestor']);
            $message->subject('Solicitação Fiscal Pendente - ' . $validatedData['tipo'] . ' - Protocolo: ' . $fiscal->id);

            // anexa se o arquivo foi salvo (disk = public)
            if (!empty($fiscal->anexo_path) && Storage::disk('public')->exists($fiscal->anexo_path)) {
                // full path: storage/app/public/{anexo_path}
                $message->attach(storage_path('app/public/' . $fiscal->anexo_path));
            }

        });

        Mail::send('emails.fiscal_solicitacao', ['fiscal' => $fiscal, 'produtos' => $fiscal->produtos], function ($message) use ($validatedData, $fiscal) {
            $message->to($validatedData['email']);
            $message->subject('Confirmação de Solicitação Fiscal - ' . $validatedData['tipo'] . ' - Protocolo: ' . $fiscal->id);

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
                $message->to('emissaonf@grupocargopolo.com.br');
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
                $message->to('emissaonf@grupocargopolo.com.br');
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
                $message->to('emissaonf@grupocargopolo.com.br');
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
                $message->to('emissaonf@grupocargopolo.com.br');
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
            $message->cc($fiscal->email_gestor);
            $message->subject('Solicitação Reprovada - Protocolo: ' . $fiscal->id);
        });

        return 'Solicitação reprovada com sucesso!';
    }


    public function aprovacao()
    {
        // Devoluções
        $aprovado_devolucao = Fiscal::whereIn('status',['aprovado','emitido','credito_pendente'])->where('tipo', 'devolucao')->orderBy('created_at', 'desc')->paginate(3);

        //Remessas
        $aprovado_remessa = Fiscal::whereIn('status',['aprovado','aguardando confirmacao de entrega','emitido','pendente_entrada_estoque_filial','retorno_pendente'])->where('tipo', 'remessa')->orderBy('created_at', 'desc')->paginate(3);
 
        //Vendas
        $aprovado_venda = Fiscal::whereIn('status',['aprovado','emitido'])->where('tipo', 'venda')->orderBy('created_at', 'desc')->paginate(3);

        //Descarte
        $aprovado_descarte = Fiscal::whereIn('status',['aprovado','emitido'])->where('tipo', 'descarte')->orderBy('created_at', 'desc')->paginate(3);

        //PENDENTES
        $pendente = Fiscal::where('status','pendente')->orderBy('created_at', 'desc')->paginate(3);

        return view('fiscal.aprovacao', compact('aprovado_devolucao','aprovado_remessa','aprovado_venda','aprovado_descarte','pendente'));
    }

    public function exportar()
    {
        $fiscais = Fiscal::all();

        if ($fiscais->isEmpty()) {
            return response('Nenhum dado encontrado', 404);
        }

        // Pega os nomes das colunas (chaves do primeiro registro)
        $colunas = array_keys($fiscais->first()->getAttributes());

        // Cabeçalho do CSV
        $csv = implode(',', $colunas) . "\n";

        // Linhas do CSV
        foreach ($fiscais as $fiscal) {
            $valores = array_map(function ($valor) {
                // Escapa vírgulas e aspas
                return '"' . str_replace('"', '""', $valor) . '"';
            }, $fiscal->getAttributes());

            $csv .= implode(',', $valores) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="fiscais.csv"');
    }


    public function emitirNf($id)
        {
            $fiscal = Fiscal::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($fiscal->user_id !== auth()->id()) {
                    return redirect()->route('fiscal.aprovacao')->with('error', 'Você não tem permissão para emitir NF.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "aprovado" para "Emitido"
            if($fiscal->tipo_de_venda == 'transferencia'){
                $fiscal->status = 'aguardando confirmacao de entrega';
            } else {
                $fiscal->status = 'emitido';
            }
            $fiscal->save();

                    // Obtém o usuário autenticado
        $user = Auth::user();

        // Envia o e-mail de emitida
        // Mail::send('emails.fiscal_emitida', ['fiscal' => $fiscal], function($message) use ($fiscal,$user){
        //     $message->to([$fiscal->email,$user->email,'emissaonf@grupocargopolo.com.br']);
        //     $message->subject('Nota Fiscal Emitida - Protocolo: ' . $fiscal->id);
        // });
    
    
        return redirect()->route('fiscal.aprovacao')->with('success', 'Nota emitida.');
        }

    public function filialPendente($id)
        {
            $fiscal = Fiscal::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($fiscal->user_id !== auth()->id()) {
                    return redirect()->route('fiscal.aprovacao')->with('error', 'Você não tem permissão para emitir NF.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $fiscal->status = 'pendente_entrada_estoque_filial';
            $fiscal->save();

            // Obtém o usuário autenticado
            $user = Auth::user();

        // Envia o e-mail de emitida
        // Mail::send('emails.fiscal_filial_pendente', ['fiscal' => $fiscal], function($message) use ($fiscal,$user){
        //     $message->to([$fiscal->email,$user->email,'emissaonf@grupocargopolo.com.br']);
        //     $message->subject('Nota Fiscal - Entrada de Estoque Filial Pendente - Protocolo: ' . $fiscal->id);
        // });

            return redirect()->route('fiscal.aprovacao')->with('success2', 'Veiculo finalizado com sucesso.');

        }

    public function filialRetorno($id)
    {
        $fiscal = Fiscal::findOrFail($id);

        if (auth()->user()->admin == 0) {
            if ($fiscal->user_id !== auth()->id()) {
                return redirect()->route('fiscal.aprovacao')->with('error', 'Você não tem permissão para emitir NF.');
            }
        }

        //$reserva->delete();
        // Altera o status da coluna "Ok" para "Cancelada"
        $fiscal->status = 'retorno_pendente';
        $fiscal->save();

        // Obtém o usuário autenticado
        $user = Auth::user();

    // Envia o e-mail de emitida
    // Mail::send('emails.fiscal_filial_pendente', ['fiscal' => $fiscal], function($message) use ($fiscal,$user){
    //     $message->to([$fiscal->email,$user->email,'emissaonf@grupocargopolo.com.br']);
    //     $message->subject('Nota Fiscal - Entrada de Estoque Filial Pendente - Protocolo: ' . $fiscal->id);
    // });

        return redirect()->route('fiscal.aprovacao')->with('success2', 'Veiculo finalizado com sucesso.');

    }

    public function creditoPendente($id)
        {
            $fiscal = Fiscal::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($fiscal->user_id !== auth()->id()) {
                    return redirect()->route('fiscal.aprovacao')->with('error', 'Você não tem permissão para emitir NF.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $fiscal->status = 'credito_pendente';
            $fiscal->save();

            // Obtém o usuário autenticado
            $user = Auth::user();

            // Envia o e-mail de finalização
            // Mail::send('emails.finalizar_reserva', ['reserva' => $reserva, 'user' => $user], function($message) use ($user, $reserva) {
            //     $message->to([$reserva->email,$reserva->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
            //     $message->subject('Reserva Finalizada');
            // });

            return redirect()->route('fiscal.aprovacao')->with('success3', 'Veiculo finalizado com sucesso.');

        }


    public function concluido($id)
        {
            $fiscal = Fiscal::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($fiscal->user_id !== auth()->id()) {
                    return redirect()->route('fiscal.aprovacao')->with('error', 'Você não tem permissão para emitir NF.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $fiscal->status = 'concluido';
            $fiscal->save();

            // Obtém o usuário autenticado
            $user = Auth::user();

            // Envia o e-mail de finalização
            // Mail::send('emails.finalizar_reserva', ['reserva' => $reserva, 'user' => $user], function($message) use ($user, $reserva) {
            //     $message->to([$reserva->email,$reserva->email_gestor ,'reservas@grupocargopolo.com.br', $user->email]);
            //     $message->subject('Reserva Finalizada');
            // });

            return redirect()->route('fiscal.aprovacao')->with('success4', 'Veiculo finalizado com sucesso.');

        }

        public function fiscal_reprovar($id)
        {
            $fiscal = Fiscal::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($fiscal->user_id !== auth()->id()) {
                    return redirect()->route('fiscal.aprovacao')->with('error', 'Você não tem permissão para emitir NF.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $fiscal->status = 'reprovado';
            $fiscal->save();

            // Obtém o usuário autenticado
            $user = Auth::user();


            Mail::send('emails.fiscal_reprovado_2', ['fiscal' => $fiscal], function($message) use ($fiscal,$user){
                $message->to($fiscal->email);
                $message->cc($fiscal->email_gestor);
                $message->subject('Solicitação Reprovada - Protocolo: ' . $fiscal->id);
            });

            return redirect()->route('fiscal.aprovacao')->with('success6', 'Veiculo finalizado com sucesso.');

        }

        public function reenviar_pendencia($id)
        {
            $fiscal = Fiscal::findOrFail($id);
    
            if (auth()->user()->admin == 0) {
                if ($fiscal->user_id !== auth()->id()) {
                    return redirect()->route('fiscal.aprovacao')->with('error', 'Você não tem permissão para emitir NF.');
                }
            }
    
            //$reserva->delete();
            // Altera o status da coluna "Ok" para "Cancelada"
            $fiscal->status = 'pendente';
            $fiscal->save();

            // Obtém o usuário autenticado
            $user = Auth::user();


            // Email só para o gestor aprovar/reprovar
            Mail::send('emails.fiscal_pendente', ['fiscal' => $fiscal, 'produtos' => $fiscal->produtos], function ($message) use ($fiscal) {
                $message->to($fiscal->email_gestor);
                $message->subject('Solicitação Fiscal Pendente - ' . $fiscal->tipo . ' - Protocolo: ' . $fiscal->id);

                // anexa se o arquivo foi salvo (disk = public)
                if (!empty($fiscal->anexo_path) && Storage::disk('public')->exists($fiscal->anexo_path)) {
                    // full path: storage/app/public/{anexo_path}
                    $message->attach(storage_path('app/public/' . $fiscal->anexo_path));
                }

            });

            return redirect()->route('fiscal.aprovacao')->with('success5', 'E-mail reenviado com sucesso.')->with('email_gestor', $fiscal->email_gestor);;

        }

}
