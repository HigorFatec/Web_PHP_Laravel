<?php

namespace App\Http\Controllers;

use App\Models\Financeiro;
use Illuminate\Http\Request;
use App\Models\Filial;
use Illuminate\Support\Facades\Mail;

use App\Models\FornecedorFinanceiro;

use Carbon\Carbon;
use DB;



class FinanceiroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');
        $fornecedores = FornecedorFinanceiro::fornecedoresQuery();
        //dd($fornecedores->count(), $fornecedores->first());

        
        return view('financeiro.index', compact('filiais','fornecedores'));
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
            // 'socorro_em_rota' => 'nullable|string',
        ]);


        // dd($validatedData);

        $financeiro = Financeiro::create($validatedData);

        // if($validatedData['socorro_em_rota'] == 'sim'){
            
        // }




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
        $nota_fiscal = $request->file('nota_fiscal');


        $tipos = $request->input('tipo_reembolso', []);

        // dd($tipos);



        if ($validatedData['tipo'] == 'avista' || $validatedData['tipo'] == 'adiantamento'){
            // Envia o email com os dados do formulário
            Mail::send('emails.financeiro', ['dados' => $validatedData, 'financeiro' => $financeiro,], function($message) use ($validatedData, $foto, $financeiro, $emails){
                $message->to(['contasapagar@grupocargopolo.com.br']);
                //$message->to('higor.05@hotmail.com');
                //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                if($validatedData['tipo'] == 'adiantamento'){
                    $message->cc($emails);
                } else {
                    $message->cc($emails);
                }
                if($validatedData['tipo'] == 'avista' && $validatedData['pedido'] != null){ 
                    $message->subject( 'PAGAMENTO A VISTA; PEDIDO: '. $validatedData['pedido'] . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] . ' PLACA: ' . $validatedData['placa'] );
                } elseif ($validatedData['tipo'] == 'avista' && $validatedData['pedido'] == null){
                    $message->subject( 'PAGAMENTO A VISTA; PROTOCOLO:'. $financeiro->id  . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] . ' PLACA: ' . $validatedData['placa'] );
                } elseif ($validatedData['tipo'] == 'adiantamento' && $validatedData['pedido'] != null){
                    $message->subject( 'ADIANTAMENTO À FORNECEDOR; PEDIDO: '. $validatedData['pedido'] . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] );
                } elseif ($validatedData['tipo'] == 'adiantamento' && $validatedData['pedido'] == null){
                    $message->subject( 'ADIANTAMENTO À FORNECEDOR; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] );
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
                        Mail::send('emails.financeiro_reembolso', ['dados' => $validatedData, 'financeiro' => $financeiro, 'tipos' => $tipos,], function($message) use ($validatedData, $foto, $nota_fiscal,$financeiro,$emails){
                            $message->to(['contasapagar@grupocargopolo.com.br']);
                            // $message->to('higor.05@hotmail.com');
                            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
                            $message->cc($emails);
                            $message->subject( 'DESPESAS/REEMBOLSO; PROTOCOLO: '. $financeiro->id . ' FORNECEDOR: ' . $validatedData['name'] . ' FILIAL: ' . $validatedData['filial'] . ' PLACA: ' . $validatedData['placa'] );

                            if ($nota_fiscal) {
                                $pathToFile = $nota_fiscal->getPathname();
                                $filename = $nota_fiscal->getClientOriginalName();
                                $message->attach($pathToFile, [
                                    'as' => $filename,
                                    'mime' => $nota_fiscal->getClientMimeType(),
                                ]);
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

public function dashboard(Request $request)
    {
        // --- 1. Filtro de Data ---
        $startDate = $request->input('startDate') ? Carbon::parse($request->input('startDate'))->startOfDay() : Carbon::now()->startOfYear();
        $endDate = $request->input('endDate') ? Carbon::parse($request->input('endDate'))->endOfDay() : Carbon::now()->endOfYear();

        // --- NOVO: Captura o filtro de Tipo ---
        $selectedTipo = $request->input('tipo');

        $query = Financeiro::query()->whereBetween('created_at', [$startDate, $endDate]);

        // --- NOVO: Aplica o filtro de Tipo se estiver presente ---
        if ($selectedTipo) {
            $query->where('tipo', $selectedTipo);
        }

        // 🚨 CHAME AGORA USANDO $this->cleanMoneyValue() 🚨
        $cleanSql = $this->cleanMoneyValue('valor');

        // O valor a ser comparado (500 mil)
        $limite = 500000;

        // APLICAÇÃO DO FILTRO DE VALOR (menor que 500k)
        $query->whereRaw("{$cleanSql} < ?", [$limite]);

        // -----------------------------------------------------------------

        // --- 2. Métricas Principais (KPIs) ---
        
        // CALCULA O VALOR TOTAL USANDO A LIMPEZA
        $valorTotal = (clone $query)
            ->select(DB::raw("SUM({$cleanSql}) as total_sum"))
            ->first()
            ->total_sum ?? 0;


        $totalPedidos = (clone $query)->count('id');
        $valorMedio = $totalPedidos > 0 ? $valorTotal / $totalPedidos : 0;

        $valorTotalFormatado = $this->formatBigNumber($valorTotal);
        $valorMedioFormatado = $this->formatBigNumber($valorMedio);

        // --- 3. Análise de Prazo --- (REMOVIDO CONFORME SOLICITADO)
        // Removendo $atrasados e $noPrazo daqui.
        
        
        // --- 4. Dados para Gráficos ---
        // A. Movimentação Mensal
        $movimentacaoMensal = (clone $query)
            ->select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw("SUM({$cleanSql}) as total_mes") 
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
            
        $meses = $movimentacaoMensal->pluck('mes')->map(fn($m) => Carbon::create(null, $m, 1)->translatedFormat('M'))->toArray();
        // APLICAR number_format AQUI:
        $totaisMensais = $movimentacaoMensal->pluck('total_mes')->map(fn($v) => number_format($v, 2, '.', ''))->toArray(); 


        // B. Top 5 Filiais por Valor
        $topFiliais = (clone $query)
            ->select('filial', DB::raw("SUM({$cleanSql}) as total_filial")) 
            ->groupBy('filial')
            ->orderByDesc('total_filial')
            ->take(5)
            ->get();
            
        $filiaisLabels = $topFiliais->pluck('filial')->toArray();
        // APLICAR number_format AQUI:
        $filiaisTotais = $topFiliais->pluck('total_filial')->map(fn($v) => number_format($v, 2, '.', ''))->toArray();

        // C. Distribuição por Tipo (Não usa 'valor', está OK)
        $distribuicaoTipo = (clone $query)
            ->select('tipo', DB::raw('COUNT(*) as contagem'))
            ->groupBy('tipo')
            ->get();
            
        $tiposLabels = $distribuicaoTipo->pluck('tipo')->toArray();
        $tiposContagem = $distribuicaoTipo->pluck('contagem')->toArray();

        // --- D. NOVO GRÁFICO: Tendência do Valor Médio Mensal ---
        $valorMedioMensalData = (clone $query)
            ->select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw("COUNT(*) as total_pedidos"),
                DB::raw("SUM({$cleanSql}) as total_valor") // Usa o valor limpo
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $valorMedioMensal = $valorMedioMensalData->map(function ($item) {
            $valorMedioBruto = $item->total_pedidos > 0 ? $item->total_valor / $item->total_pedidos : 0;
            // APLICAR number_format AQUI:
            return number_format($valorMedioBruto, 2, '.', '');
        })->toArray();
        
        // --- E. NOVO GRÁFICO: Distribuição de Pedidos por Ano ---
        $contagemAnualData = (clone $query)
            ->select(
                DB::raw('YEAR(created_at) as ano'),
                DB::raw('COUNT(*) as contagem')
            )
            ->groupBy('ano')
            ->orderBy('ano')
            ->get();
            
        $anos = $contagemAnualData->pluck('ano')->toArray();
        $contagemAnual = $contagemAnualData->pluck('contagem')->toArray();


        // --- 5. Retorno para a View ---
        return view('admin.financeiro-dashboard', compact(
            'valorTotal', 
            'totalPedidos', 
            'valorMedio',
            'meses', 
            'totaisMensais',
            'filiaisLabels',
            'filiaisTotais',
            'tiposLabels',
            'tiposContagem',
            'startDate', 
            'endDate',
            'valorTotalFormatado',
            'valorMedioFormatado',
            'selectedTipo',
            'valorMedioMensal', // NOVO DADO
            'anos',             // NOVO DADO
            'contagemAnual'     // NOVO DADO ← TIRE A VÍRGULA AQUI
        ));

    }

    // Dentro da classe FinanceiroController, adicione este método privado:
private function formatBigNumber(float $number): string
{
    // Array com os sufixos e seus respectivos valores
    $units = [
        1000000000000 => 'T', // Trilhão
        1000000000 => 'B',  // Bilhão
        1000000 => 'M',     // Milhão
        1000 => 'K',        // Mil
    ];

    // Verifica se o número é zero
    if ($number == 0) {
        return '0';
    }

    // Garante que o número seja positivo para o cálculo
    $absNumber = abs($number);

    // Itera sobre as unidades de maior para menor
    foreach ($units as $unit => $suffix) {
        if ($absNumber >= $unit) {
            // Calcula o valor formatado com 2 casas decimais
            $formattedValue = number_format($number / $unit, 2, ',', '.');
            
            // Retorna o valor com o sufixo
            return 'R$ ' . $formattedValue . $suffix;
        }
    }

    // Se for menor que mil, retorna o número normal formatado
    return 'R$ ' . number_format($number, 2, ',', '.');
}

// Você pode criar um Helper ou colocar esta função dentro do seu Model ou Controller temporariamente

private function cleanMoneyValue(string $columnName): string
{
    // Regex: [^0-9,] significa "qualquer caractere que NÃO seja (^) um número (0-9) OU uma vírgula (,)".
    // Substituímos tudo o que for diferente de número ou vírgula por uma string vazia ('').
    $removeSymbols = "REGEXP_REPLACE({$columnName}, '[^0-9,]', '')";

    // Em seguida, substituímos a vírgula restante pelo ponto decimal, preparando para a soma.
    $cleanedValue = "REPLACE({$removeSymbols}, ',', '.')";
    
    return $cleanedValue;
}

}
