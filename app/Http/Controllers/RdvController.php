<?php

namespace App\Http\Controllers;

use App\Models\Rdv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Adiantamento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\UnidadesNegocio;
use App\Models\Relatorio;
use Illuminate\Support\Facades\Mail;



class RdvController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('rdv.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produtos = Rdv::produtos();


        return view('rdv.despesas', compact('produtos'));
    }

    public function relatorio()
    {
        $filiais = UnidadesNegocio::orderBy('unidade_negocio')->get();

        $despesas = Rdv::where('user_id', auth()->id())
                        ->where('status', 'pendente')
                        ->get();

        return view('rdv.relatorio', compact('filiais','despesas'));
    }



    public function store_relatorio(Request $request)
    {
        // 1. Validação dos dados
        $validated = $request->validate([
            'titulo'                => 'required|string|max:255',
            'motivo'                => 'required|string',
            'cod_unidade'           => 'required',
            'cod_custo'             => 'required',
            'cod_gasto'             => 'required',
            'gestor_aprovador'      => 'required',
            'inicio_viagem'         => 'required|date',
            'fim_viagem'            => 'required|date|after_or_equal:inicio_viagem',
            'despesas_selecionadas' => 'required|array|min:1', // IDs das despesas vindos do checkbox
        ]);

        try {
            // Inicia a transação: ou grava tudo, ou não grava nada!
            DB::beginTransaction();

            // 1. CALCULAR O TOTAL (Novidade aqui)
            // Buscamos a soma de todos os 'valor' das despesas selecionadas
            $valorTotal = Rdv::whereIn('id', $request->despesas_selecionadas)
                            ->where('user_id', Auth::id())
                            ->sum('valor');

            // 2. Criar o Relatório (O "Pai")
            $relatorio = Relatorio::create([
                'titulo'           => $validated['titulo'],
                'motivo'           => $validated['motivo'],
                'cod_unidade'      => $validated['cod_unidade'],
                'cod_custo'        => $validated['cod_custo'],
                'cod_gasto'        => $validated['cod_gasto'],
                'gestor_aprovador' => $validated['gestor_aprovador'],
                'inicio_viagem'    => $validated['inicio_viagem'],
                'fim_viagem'       => $validated['fim_viagem'],
                'valor'            => $valorTotal,
                'user_id'          => Auth::id(),
                'user_name'        => Auth::user()->name,
                'user_email'       => Auth::user()->email,
                'status'           => 'pendente',
                'approval_token'   => Str::uuid(),
            ]);


            // 3. Atualizar as Despesas (Os "Filhos")
            // Aqui pegamos todos os IDs que vieram no array despesas_selecionadas
            Rdv::whereIn('id', $request->despesas_selecionadas)
                ->where('user_id', Auth::id()) // Segurança: garante que o usuário só altere as próprias despesas
                ->update([
                    'relatorio_id' => $relatorio->id,
                    'status'       => 'em_relatorio' 
                ]);


            $relatorio->load('unidades', 'despesas', 'centroGasto', 'centroCusto', 'gestorFinanceiro','unidadeAprovadora.gestorRegional');

            // Se chegou aqui sem erro, confirma no banco
            DB::commit();

            // Email só para o gestor aprovar/reprovar
            Mail::send('emails.relatorio_pendente', ['relatorio' => $relatorio], function ($message) use ($relatorio) {
                $message->to($relatorio->gestor_aprovador);
                $message->subject('Solicitação de Relatório de Despesa Pendente - Protocolo: ' . $relatorio->id);
                

            });

            Mail::send('emails.relatorio_solicitacao', ['relatorio' => $relatorio], function ($message) use ($relatorio) {
                $message->to($relatorio->user_email);
                $message->subject('Confirmação de Solicitação Financeiro - Protocolo: ' . $relatorio->id);


            });





            return redirect()->route('rdv.index')->with('success', 'Relatório criado e despesas vinculadas!');

        } catch (\Exception $e) {
            // Se der qualquer erro (banco caiu, campo faltando), desfaz tudo
            DB::rollBack();
            
            return redirect()->back()
                            ->withErrors(['error' => 'Falha ao salvar relatório: ' . $e->getMessage()])
                            ->withInput();
        }
    }




    public function buscarFornecedores(Request $request)
    {
        $search = $request->search;

        $fornecedores = Adiantamento::fornecedoresQuery($search);

        return response()->json($fornecedores);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $user = Auth::user();

        // dd($user);

        $validatedData = $request->validate([
            'fornecedor' => 'required|string',
            'despesa' => 'required|numeric',
            'date' => 'required|date',
            'valor' => 'required',
            'foto' => 'required',
        ]);

        $descricaoFornecedor = DB::connection('sqlsrv')
                                ->table('RODCLI')
                                ->where('CODCLIFOR', $validatedData['fornecedor'])
                                ->value('RAZSOC');

        
        $descricaoDespesa = DB::connection('sqlsrv')
                                ->table('ESTPRO')
                                ->where('CODPROD', $validatedData['despesa'])
                                ->value('DESCRI');

        $classificacao_sintetica = DB::connection('sqlsrv')
                                    ->table('ESTCPP')
                                    ->where('CODPROD', $validatedData['despesa'])
                                    ->value('SINTET');

        $classificacao_analitica = DB::connection('sqlsrv')
                                    ->table('ESTCPP')
                                    ->where('CODPROD', $validatedData['despesa'])
                                    ->value('ANALIT');

        // dd($validatedData);

        $despesa = Rdv::create(array_merge(
            $request->all(),
            [
                'approval_token' => Str::uuid(),
                'user_name' => $user->name,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'descricao_fornecedor' => $descricaoFornecedor,
                'descricao_despesa' => $descricaoDespesa,
                'SINTET' => $classificacao_sintetica,
                'ANALIT' => $classificacao_analitica
            ]
        ));

            // TRATAMENTO DO UPLOAD DO ANEXO
            // STORE
            if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
                $file = $request->file('foto');
                $extension = $file->guessExtension() ?? 'bin';
                $filename = time() . '_' . Str::uuid() . '.' . $extension;

                $file->move(storage_path('app/public/despesas'), $filename);

                $path = 'despesas/' . $filename;
                $despesa->update(['anexo' => $path]);


                $despesa->update(['anexo' => $path]);
            }
            // FIM ANEXO

        return redirect()->route('rdv.index')->with('success', 'Deu Certo');

    }

    public function telaAprovacao($token)
    {
        // Busca o relatório pelo token com as despesas
        $relatorio = Relatorio::where('approval_token', $token)->firstOrFail();
        
        // Carrega as despesas vinculadas
        $despesas = $relatorio->despesas; 

        // Soma apenas as despesas que o gestor já clicou em "Aprovar"
        $valorAprovado = $despesas->where('status', 'aprovado')->sum('valor');
        
        $existePendente = $despesas->contains('status', 'pendente');

        return view('rdv.aprovacao_gestor', compact('relatorio', 'despesas', 'valorAprovado', 'existePendente'));
    }

    // No Controller de Despesas
    public function updateStatus(Request $request, $id)
    {
        $despesa = Rdv::findOrFail($id);
        $despesa->update([
            'status' => $request->status, 
            'observacao'=> $request->observacao
        ]);

        return redirect()->back()->with('success', 'Status da despesa atualizado!');
    }

public function finalizar(Request $request, $id)
{
    try {
        DB::beginTransaction();

        // 1. Carregamos as relações necessárias
        $relatorio = Relatorio::with(['despesas', 'unidades', 'centroGasto', 'centroCusto'])
            ->where('status', 'pendente')
            ->findOrFail($id);

        // 2. Validação de pendências
        if ($relatorio->despesas->where('status', 'pendente')->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Existem despesas pendentes!']);
        }

        // 3. Recálculo do valor (apenas aprovados)
        $despesasAprovadas = $relatorio->despesas->where('status', 'aprovado');
        $novoValorTotal = $despesasAprovadas->sum('valor');

        // 4. Atualiza o Relatório
        $relatorio->update([
            'valor'  => $novoValorTotal,
            'status' => 'aprovado'
        ]);

        // 5. Integração do Cabeçalho
        // CORREÇÃO: Pegamos o fornecedor da primeira despesa aprovada da lista
        $primeiraDespesa = $despesasAprovadas->first();
        $fornecedorId = $primeiraDespesa->fornecedor ?? '0'; // ID/Código do fornecedor
        
        $relatorio->entradaMateriais(
            $relatorio->id, 
            $relatorio->valor, 
            $fornecedorId, 
            $relatorio->unidades->nome_gestor ?? 'Gestor não definido'
        );

        // dd($despesasAprovadas);

        $x = 0;

        // 6. Integração dos Itens
        foreach ($despesasAprovadas as $item) {

            $ultimoId = DB::connection('sqlsrv')->table('ESTAIE')->max('ID_AIE');
            $proximo = $ultimoId + 1;
  
            $x = $x + 1;

            $item->produtosMateriais(
                $item->id, 
                $item->fornecedor, 
                $item->despesa, 
                $item->valor,
                $proximo,
                $item->relatorio_id,
                $x
            );
        }

        // 7. Limpeza: Desvincular despesas reprovadas
        Rdv::where('relatorio_id', $relatorio->id)
            ->where('status', 'reprovado')
            ->update(['relatorio_id' => null]);

        // 8. Atualiza ID Rodopar (Usando o fornecedor da despesa aprovada)
        $relatorio->update([
            'id_rodopar' => ($fornecedorId) . '-A-' . $relatorio->id
        ]);

        // CORREÇÃO AQUI: Use $fornecedorId em vez de $relatorio->fornecedor
        $adiantamentoModel = Adiantamento::where('fornecedor', $fornecedorId)
            ->where('status', 'aprovado')->orderBy('id','desc')
            ->first();

        // Debug temporário: se quiser testar, descomente a linha abaixo
        // if (!$adiantamentoModel) { dd("Não encontrou adiantamento para o fornecedor: " . $fornecedorId); }

        $situacaoPadrao = 'L';

        if ($adiantamentoModel) {
            $relatorio->adiantamentos(
                $fornecedorId, 
                $relatorio->id, 
                $relatorio->valor, 
                $adiantamentoModel->id_raz,
            );
            $adiantamentoModel->update(['status' => 'utilizado']);

            // 3. O SEGREDO ESTÁ AQUI: Força a variável a buscar os dados novos no banco!
            $adiantamentoModel->refresh();

            // 4. REDE DE SEGURANÇA: Se a coluna estiver vazia no banco, ele assume o padrão e NÃO envia NULL
            $situacaoParaEnviar = $adiantamentoModel->situac ?? $situacaoPadrao;
            $valorLiquidoParaEnviar = $adiantamentoModel->valor_liquido ?? $relatorio->valor;
            $valorUtilizadoParaEnviar = $adiantamentoModel->valor_utilizado ?? 0;

            $relatorio->finalizar(
            $fornecedorId,
            $relatorio->valor,
            $relatorio->id,
            $relatorio->cod_unidade,
            $relatorio->cod_custo,
            $relatorio->cod_gasto,
            $relatorio->unidades->nome_gestor ?? 'Gestor não definido',
            $valorLiquidoParaEnviar,
            $valorUtilizadoParaEnviar,
            $situacaoParaEnviar);

        } else {$relatorio->finalizar(
            $fornecedorId,
            $relatorio->valor,
            $relatorio->id,
            $relatorio->cod_unidade,
            $relatorio->cod_custo,
            $relatorio->cod_gasto,
            $relatorio->unidades->nome_gestor ?? 'Gestor não definido',
            $relatorio->valor,
            0,
            $situacaoPadrao
            

        );}


                // 6. Integração dos Itens
        foreach ($despesasAprovadas as $item) {
            $item->pagrat(
                $fornecedorId, 
                $item->valor,
                $relatorio->id, 
                $relatorio->cod_unidade,
                $relatorio->cod_custo,
                $relatorio->cod_gasto,
                $item->despesa,
                $item->SINTET,
                $item->ANALIT
            );
        }

        
        // 4. REDE DE SEGURANÇA: Se a coluna estiver vazia no banco, ele assume o padrão e NÃO envia NULL
        $situacaoParaEnviar = $adiantamentoModel->situac ?? $situacaoPadrao;


        $relatorio->finalizar_2(
            $fornecedorId,
            $relatorio->valor,
            $relatorio->id,
            $relatorio->cod_unidade,
            $relatorio->cod_custo,
            $relatorio->cod_gasto,
            $relatorio->unidades->nome_gestor ?? 'Gestor não definido',
            $adiantamentoModel->valor_liquido,
            $adiantamentoModel->valor_utilizado,
            $situacaoParaEnviar);







        DB::commit();

        // 9. Envio de E-mail
        try {
            Mail::send('emails.relatorio_finalizar', ['relatorio' => $relatorio], function ($message) use ($relatorio) {
                $message->to('contasapagar@grupocargopolo.com.br');
                $message->cc([$relatorio->unidades?->email_gestor ?? null,$relatorio->user_email]);
                $message->subject('Relatório de Despesa Aprovado - Protocolo: ' . $relatorio->id);
            });
        } catch (\Exception $e) {
            // E-mail falhou, mas o processo no banco foi um sucesso.
        }

        return redirect()->route('rdv.index')
            ->with('success', 'Relatório finalizado! Valor aprovado: R$ ' . number_format($novoValorTotal, 2, ',', '.'));

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['error' => 'Falha ao finalizar relatório: ' . $e->getMessage()])
            ->withInput();
    }
}



    /**
     * Display the specified resource.
     */
    public function show(Nofly $nofly)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nofly $nofly)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nofly $nofly)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nofly $nofly)
    {
        //
    }






    public function estent(){


    // INSERT INTO ESTENT (DATINC,USUINC,CODPAD,USUATU,DATATU,CODMOT,CODFEC,NUMDFE,CODLIN,REFSER,REFDOC,CODLOT,RESPFR,SUBCON,CODTPS,CODMUN,CLIREB,NFE_ID,CODOPE,TIPONF,CODTAR,CODFPG,SITUAC,CODBCO,CODTAX,CODCLIFOR,REFERE,NUMDOC,CODFIL,OBSERV,SERIE,VLRDOC,DATEMI,DATREF,VLRLIQ,DESCAN,JURDIA,VLRPED,DESPEX,ICMSEX,PERJUR,DESISS,DESPIS,DESIR,DESINS,DESCSL,DESCOF,ICMSST,DESADT,DESIPI,REFDAT,DEDBAS,DESPIN,DESCOB,VLRFRE,VLRSEG,NUMCNO) 

    // VALUES('02/20/2026 09:55:00','HIGOR.MACHADO',1,'HIGOR.MACHADO','02/20/2026 09:55:00',null,null,NULL,NULL,NULL,0,0,'S','N',null,null,null,NULL,null,'REL',1717,null,'I',237,1,154189,NULL,'123',5,NULL,'A',0,'02/20/2026','02/20/2026',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,0,NULL)




    // INSERINDO ADIANTAMENTO

    // INSERT INTO BANRNF (CODCLIFOR,SERIE,NUMDOC,TIPONF,SITNOT,ID_RAZ,DATATU,USUATU,VLRDOC,TEMPOR,NOMEPC,ORIGEM,NUMDUP,FILDUP,NUMPAR) VALUES (154189,'A','123','REL','I',507299,getdate(),'HIGOR.MACHADO',420, Null ,'DESKTOP-AEN566N','NFE', Null , Null , Null )

    // UPDATE ESTENT SET CODPAD=1,USUATU='HIGOR.MACHADO',DATATU='02/20/2026 09:56:00',CODMOT=NULL,CODFEC=NULL,NUMDFE=NULL,CODLIN=NULL,REFSER=NULL,REFDOC=0,CODLOT=0,RESPFR='S',SUBCON='N',CODTPS=NULL,CODMUN=NULL,CLIREB=NULL,NFE_ID=NULL,CODOPE=NULL,CODTAR=1717,CODFPG=NULL,SITUAC='I',CODBCO=237,CODTAX=1,REFERE=NULL,CODFIL=5,OBSERV=NULL,VLRDOC=0,DATEMI='02/20/2026',DATREF='02/20/2026',VLRLIQ=-420.00,DESCAN=0,JURDIA=0,VLRPED=0,DESPEX=0,ICMSEX=0,PERJUR=0,DESISS=0.00,DESPIS=0.00,DESIR=0,DESINS=0.00,DESCSL=0.00,DESCOF=0.00,ICMSST=0,DESADT=420,DESIPI=0,REFDAT=NULL,DEDBAS=0,DESPIN=0,DESCOB=0,VLRFRE=0,VLRSEG=0,NUMCNO=NULL  WHERE TIPONF='REL' AND CODCLIFOR=154189 AND NUMDOC='123' AND SERIE='A'

    // UPDATE OSEONF SET VLRDOC = 0 , DATNOT = '02/20/2026 00:00' WHERE CODCLIFOR = 154189 AND SERIE = 'A' AND NUMDOC = '123'


    // INSERINDO AS DESPESAS

    // INSERT INTO ESTAIE (ID_AIE,CODCLIFOR,TIPONF,SERIE,NUMDOC,CODPROD,QTDENT,VLRUNI,VLRTOT,VLRIPI,PRICMS,PRDCMR,DATATU,USUATU,NUMPED,VLRPIS,VLRCOF,PERIPI,DESCRI,CODFIS,BASSUB,REFINT,REFFOR,VLRFRE,DESPIN,VLRSEG,BASCAL,VLRICM,CSTICM,OUTROS,VLRISE,DIFALI,VLRDIF,ALISUB,ICMSUB,CSTSUB,BASIPI,CSTIPI,BASPIS,CSTPIS,BASCOF,CSTCOF,CODGRUFIS,DESTAC,CODCHA,CODDEP,CODAUT,CMBAMB,UFCONS,BASCID,PERCID,VLRCID,OBSFIS,CODVEI,VLRIMP,TOTPAR,ALICOT,VCTPAR,DEPCAL,ANALIT,ORDEM,ICMDED,UNIDAD,BASCBS, ALICBS, VLRCBS, CSTCBS, CLACBS,BASIBS, ALIIBS, VLRIBS, CSTIBS, CLAIBS, BASIS, ALIIS, VLRIS, CSTIS, CLAIS,ALIREM) VALUES(717017,'154189','REL','A','123','946367', Null ,420,0.000000,0,0, Null ,getdate(),'HIGOR.MACHADO',null,0.00,0.00,0,'ALMOÇO',1102, Null , Null , Null , Null , Null , Null ,0,0,'90',0,0, Null , Null , Null , Null ,'90', Null ,'03',0,'70',0,'70',30,'N' , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null ,Null, Null , Null ,'1', Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null ,0)

    // OUTRO EXEMPLO COM CAFE DA MANHA
    // INSERT INTO ESTAIE (ID_AIE,CODCLIFOR,TIPONF,SERIE,NUMDOC,CODPROD,QTDENT,VLRUNI,VLRTOT,VLRIPI,PRICMS,PRDCMR,DATATU,USUATU,NUMPED,VLRPIS,VLRCOF,PERIPI,DESCRI,CODFIS,BASSUB,REFINT,REFFOR,VLRFRE,DESPIN,VLRSEG,BASCAL,VLRICM,CSTICM,OUTROS,VLRISE,DIFALI,VLRDIF,ALISUB,ICMSUB,CSTSUB,BASIPI,CSTIPI,BASPIS,CSTPIS,BASCOF,CSTCOF,CODGRUFIS,DESTAC,CODCHA,CODDEP,CODAUT,CMBAMB,UFCONS,BASCID,PERCID,VLRCID,OBSFIS,CODVEI,VLRIMP,TOTPAR,ALICOT,VCTPAR,DEPCAL,ANALIT,ORDEM,ICMDED,UNIDAD,BASCBS, ALICBS, VLRCBS, CSTCBS, CLACBS,BASIBS, ALIIBS, VLRIBS, CSTIBS, CLAIBS, BASIS, ALIIS, VLRIS, CSTIS, CLAIS,ALIREM) VALUES(717018,'154189','REL','A','123','946366',1,15,15.000000,0,0, Null ,getdate(),'HIGOR.MACHADO',null,0.00,0.00,0,'CAFÉ DA MANHA',1102, Null , Null , Null , Null , Null , Null ,0,0,'90',0,15, Null , Null , Null , Null ,'90', Null ,'03',0,'70',0,'70',30,'N' , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null ,Null, Null , Null ,'2', Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null , Null ,0)

    // INSERT INTO ESTAIL VALUES(717017,'154189','REL','A','123',946367,1,0,getdate(),getdate(),'HIGOR.MACHADO')

    // INSERT INTO ESTAIL VALUES(717018,'154189','REL','A','123',946366,1,1,getdate(),getdate(),'HIGOR.MACHADO')

    // UPDATE ESTENT SET VLRDOC = 0, VLRLIQ = -420.00, DESIPI = 0  WHERE CODCLIFOR = 154189 AND NUMDOC = '123' AND SERIE = 'A' AND TIPONF = 'REL'

    // UPDATE ESTENT SET VLRDOC = 15, VLRLIQ = -405.00, DESIPI = 0  WHERE CODCLIFOR = 154189 AND NUMDOC = '123' AND SERIE = 'A' AND TIPONF = 'REL'


    // UPDATE OSEONF SET VLRDOC = 629 , DATNOT = '02/20/2026 00:00' WHERE CODCLIFOR = 154189 AND SERIE = 'A' AND NUMDOC = '123'


    // UPDATE ESTENT SET SITUAC = 'O' WHERE  NUMDOC = '123' AND CODCLIFOR = 154189 AND SERIE = 'A' AND TIPONF = 'REL'



    // INSERT INTO RATPAG (CODCLIFOR,TIPONF,SERIE,NUMDOC,CODVEI,CODEVE,CODFRO,CODLIN,CODFIL,NUMACE, DATREF,TIPEVE,TIPDOC,VLRTOT,ATUKMT,CODMOT,ID_ACE,USUINC,DATINC,USUATU,DATATU,QUANTI,VLRUNI) SELECT  CODCLIFOR, 'ACV', 'A', '123' ,CODVEI, CODEVE,CODFRO,CODLIN,CODFIL,NUMACE,DATREF,TIPEVE,TIPDOC,QUANTI*VLRUNI,ATUKMT,CODMOT,ID_ACE,USUINC,DATINC,USUATU,DATATU,QUANTI,VLRUNI FROM ESTNEV WHERE NUMDOC = '123' AND SERIE = 'A' AND CODCLIFOR = 154189 AND TIPONF = 'REL'
    

    }





}









// CONTINUIDADE



// ADIANTAMENTO
// INSERT INTO BANRNF (CODCLIFOR,SERIE,NUMDOC,TIPONF,SITNOT,ID_RAZ,DATATU,USUATU,VLRDOC,TEMPOR,NOMEPC,ORIGEM,NUMDUP,FILDUP,NUMPAR) VALUES (154189,'A','31','REL','I',507299,getdate(),'HIGOR.MACHADO',90, Null ,'DESKTOP-AEN566N','NFE', Null , Null , Null )

// UPDATE ESTENT SET CODPAD=1,USUATU='HIGOR.MACHADO',DATATU='02/25/2026 17:43:00',CODMOT=NULL,CODFEC=NULL,NUMDFE=NULL,CODLIN=NULL,REFSER=NULL,REFDOC=0,CODLOT=0,RESPFR='S',SUBCON='N',CODTPS=NULL,CODMUN=NULL,CLIREB=NULL,NFE_ID=NULL,CODOPE=NULL,CODTAR=1717,CODFPG=NULL,SITUAC='I',CODBCO=237,CODTAX=1,REFERE=NULL,CODFIL=5,OBSERV='Reembolso de Despesa - Aprovado por Higor Machado',VLRDOC=90,DATEMI='02/25/2026',DATREF='02/25/2026',VLRLIQ=0.00,DESCAN=0,JURDIA=0,VLRPED=0,DESPEX=0,ICMSEX=0,PERJUR=0,DESISS=0.00,DESPIS=0.00,DESIR=0,DESINS=0.00,DESCSL=0.00,DESCOF=0.00,ICMSST=0,DESADT=90,DESIPI=0,REFDAT=NULL,DEDBAS=0,DESPIN=0,DESCOB=0,VLRFRE=0,VLRSEG=0,NUMCNO=NULL  WHERE TIPONF='REL' AND CODCLIFOR=154189 AND NUMDOC='31' AND SERIE='A'