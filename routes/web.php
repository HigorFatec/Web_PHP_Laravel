<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FornecedorFisicoController;
use App\Http\Controllers\FornecedorFinanceiroController;
use App\Http\Controllers\SobreController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\HospedagemController;
use App\Http\Controllers\AdiantamentoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransfVeiculoController;
use App\Http\Controllers\PagamentoPixController;
use App\Http\Controllers\FlorestalPixController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\FiscalController;

use App\Http\Controllers\SaldoController;
use App\Http\Controllers\SinistroController;
use App\Http\Controllers\DescartePneusController;

use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

//CHAT
use App\Http\Controllers\ChatController;

//ORDEM DE SERVIÇO
use App\Http\Controllers\OSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;


// AZURE MICROSOFT LOGIN
use App\Http\Controllers\AuthMicrosoftController;



// FR
use App\Http\Controllers\FinanceiroFrController;

//ONFLY
use App\Http\Controllers\RdvController;


use App\Http\Controllers\NotificationController;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\AdminController2;

use App\Http\Controllers\PixController;

use App\Http\Controllers\ReembolsoController;

use App\Http\Controllers\ImplantacaoSaldoController;

use App\Http\Controllers\AjudaDeCustoController;

use Illuminate\Support\Facades\DB;


use App\Http\Controllers\AbastecimentoController;


Route::view('/login', 'login.form')->name('login.form');
Route::post('/auth', [LoginController::class, 'auth'])->name('login.auth');
Route::get('/logout', [LoginController::class, 'logout'])->name('login.logout');
Route::get('/register', [LoginController::class, 'create'])->name('login.create');

Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// AZURE MICROSOFT LOGIN
Route::get('/auth/microsoft', [AuthMicrosoftController::class, 'redirect']);
Route::get('/auth/microsoft/callback', [AuthMicrosoftController::class, 'callback']);
Route::get('/completar-cadastro', [AuthMicrosoftController::class, 'completarCadastro']);
Route::post('/completar-cadastro', [AuthMicrosoftController::class, 'salvarCadastro']);


Route::middleware(['auth'])->group(function () {


    Route::get('/teste-sqlserver', function () {
        try {
            // Select simples para pegar a data/hora atual do SQL Server
            $dados = DB::connection('sqlsrv')->select('SELECT GETDATE() AS data_atual');

            return response()->json($dados);
        } catch (\Exception $e) {
            return response()->json([
                'erro' => $e->getMessage()
            ], 500);
        }
    });



    Route::get('/goto/{route}', [HomeController::class, 'goToRoute'])
        ->where('route', 'financeiro.index|saldo.combustivel|empresa.create|fiscal.index|produtos.create|transf_veiculo.index|descarte.index|rdv.index')
        ->name('goto.route');


    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::resource('users', UserController::class);


    Route::get('/fornecedor_juridico', [EmpresaController::class, 'create'])->name('empresa.create');
    Route::get('/fornecedor_fisico', [FornecedorFisicoController::class, 'create'])->name('fisico.fornecedor_fisico');

    Route::get('/fornecedor_financeiro', [FornecedorFinanceiroController::class, 'create'])->name('fisico.fornecedor_financeiro');

    Route::get('/produtos', [ProdutoController::class, 'create'])->name('produtos.create')->middleware('only.from.home');

    Route::get('/produtos/aprovacao', [ProdutoController::class, 'aprovacao'])->name('produtos.aprovacao');


    Route::get('/produtos/aprovar/{token}', [ProdutoController::class, 'finalizar'])->name('produtos.finalizar');
    Route::post('/produtos/cancelar/{token}', [ProdutoController::class, 'cancelar'])->name('produto.cancelar');
    Route::get('/produtos/cancelar/{token}', [ProdutoController::class, 'formCancelar'])->name('produtos.cancelar.form');







    Route::get('/transf_veiculo', [TransfVeiculoController::class, 'index'])->name('transf_veiculo.index')->middleware('only.from.home');

    Route::get('/combustivel', [SaldoController::class, 'combustivel'])->name('saldo.combustivel');


    Route::get('/pagamento_pix', [PagamentoPixController::class, 'index'])->name('pagamento_pix.index')->middleware('only.from.home');
    Route::post('/cancelar-pagamento/{id}', [PagamentoPixController::class, 'cancelarPagamento'])->name('cancelar.pagamento');
    Route::post('/finalizar-pagamento/{id}', [PagamentoPixController::class, 'finalizarPagamento'])->name('finalizar.pagamento');
    Route::get('/pagamento/aprovacoes', [PagamentoPixController::class, 'aprovacao'])->name('pagamento_pix.aprovacao');

    Route::get('/saldo', [SaldoController::class, 'index'])->name('saldo.index');
    Route::get('/valecard', [SaldoController::class, 'valecard'])->name('saldo.valecard');

    Route::get('/sinistro', [SinistroController::class, 'index'])->name('sinistro.index')->middleware('only.from.home');
    Route::post('/sinistro/store', [SinistroController::class, 'store'])->name('sinistro.store');
    Route::get('/sinistro_2', [SinistroController::class, 'sem_terceiro'])->name('sinistro.sem_terceiro');



    Route::get('/florestal_pix', [FlorestalPixController::class, 'index'])->name('florestal_pix.index')->middleware('only.from.home');



    Route::get('/financeiro', [FinanceiroController::class, 'index'])->name('financeiro.index');

    Route::post('/financeiro', [FinanceiroController::class, 'store'])->name('financeiro.store');





    Route::get('/financeiro_fr', [FinanceiroFrController::class, 'index'])->name('financeiro_fr.index');

    Route::post('/financeiro_fr', [FinanceiroFrController::class, 'store'])->name('financeiro_fr.store');

    Route::get('/financeiro/buscar-pedido/{numped}', [FinanceiroFrController::class, 'buscarDadosPedido']);

    // ROTAS DE REEMBOLSO
    Route::get('/reembolso_fr', [ReembolsoController::class, 'reembolso'])->name('reembolso.create');

    Route::post('/reembolso_fr', [ReembolsoController::class, 'store_reembolso'])->name('reembolso.store');

    Route::get('/despesas_fr', [ReembolsoController::class, 'despesas'])->name('despesas.create');

    Route::post('/despesas_fr', [ReembolsoController::class, 'despesas_store'])->name('despesas.store');

    // Tela que o gestor acessa pelo link do e-mail
    Route::get('/reembolso/analise/{token}', [ReembolsoController::class, 'telaAprovacao'])->name('reembolso.tela_aprovacao');

    // Rota para o gestor aprovar/reprovar CADA despesa (item por item)
    Route::post('/reembolso/{id}/status', [ReembolsoController::class, 'updateStatus'])->name('reembolso.status');

    Route::post('/reembolso/{id}/finalizar', [ReembolsoController::class, 'finalizar'])->name('reembolso.finalizar');






    Route::get('/fornecedores/buscar', [FinanceiroFrController::class, 'buscarFornecedores'])
        ->name('fornecedores.buscar');



    Route::get('/adiantamentos/buscar', [AdiantamentoController::class, 'buscarFornecedores'])
        ->name('adiantamentos.buscar');


    Route::get('/produtos/buscar', [ImplantacaoSaldoController::class, 'buscarProdutos'])
        ->name('produtos.buscar');

    Route::get('/pagamento_pix/buscar', [PagamentoPixController::class, 'buscarFornecedores'])
        ->name('pagamento_pix.buscar');


    Route::get('/financeiro_fr/saldo', [FinanceiroFrController::class, 'saldo'])->name('financeiro_fr.saldo');

    Route::post('/saldo/update', [FinanceiroFrController::class, 'update'])->name('saldo.update');
    Route::post('/gestor/update', [FinanceiroFrController::class, 'update_gestor'])->name('gestor.update');
    Route::post('/conta/update', [FinanceiroFrController::class, 'update_conta'])->name('conta.update');

    Route::post('/novo-saldo/update', [FinanceiroFrController::class, 'update_novo_saldo'])->name('novo-saldo.update');








    Route::get('/fiscal', [FiscalController::class, 'index'])->name('fiscal.index')->middleware('only.from.home');


    //Descarte de Pneus
    Route::get('/descarte', [DescartePneusController::class, 'index'])->name('descarte.index')->middleware('only.from.home');
    Route::post('/descarte/store', [DescartePneusController::class, 'store'])->name('descarte.store');


    Route::post('/empresa/store', [EmpresaController::class, 'store'])->name('empresa.store');
    Route::post('/fornecedor_fisico/store', [FornecedorFisicoController::class, 'store'])->name('fornecedor_fisico.store');

    Route::post('/fornecedor_financeiro/store', [FornecedorFinanceiroController::class, 'store'])->name('fornecedor_financeiro.store');

    Route::post('/produtos/store', [ProdutoController::class, 'store'])->name('produtos.store');
    Route::post('/transf_veiculo/store', [TransfVeiculoController::class, 'store'])->name('transf_veiculo.store');

    Route::post('/pagamento_pix', [PagamentoPixController::class, 'store'])->name('pagamento_pix.store');

    Route::post('/florestal_pix', [FlorestalPixController::class, 'store'])->name('florestal_pix.store');

    Route::post('/fiscal', [FiscalController::class, 'store'])->name('fiscal.store');


    Route::get('/empresa/success', function () {
        return view('empresa.success');
    })->name('empresa.success');

    Route::get('/fisico/success', function () {
        return view('fisico.success');
    })->name('fisico.success');

    Route::get('/produtos/success', function () {
        return view('produtos.success');
    })->name('produtos.success');


    Route::get('/sobre', [SobreController::class, 'index'])->name('site.sobre');

    Route::get('/reserva', [ReservaController::class, 'index'])->name('reserva.home');
    Route::get('/reserva/passagem', [ReservaController::class, 'passagemAerea'])->name('reserva.passagem-aerea');
    Route::post('/reserva/passagem-aerea', [ReservaController::class, 'store']);
    Route::get('/reserva/reservas', [ReservaController::class, 'minhasPassagens'])->name('reserva.reservas');
    Route::post('/cancelar-passagem/{id}', [ReservaController::class, 'cancelarPassagem'])->name('cancelar.passagem');
    Route::post('/finalizar-passagem/{id}', [ReservaController::class, 'finalizarPassagem'])->name('finalizar.passagem');
    Route::get('/reserva/canceladas', [ReservaController::class, 'canceladas'])->name('admin.canceladas');
    Route::get('/reserva/finalizadas', [ReservaController::class, 'finalizadas'])->name('admin.finalizadas');

    Route::get('/reserva/pendentes', [ReservaController::class, 'reservas_pendentes'])->name('reserva.pendentes');
    Route::post('/reserva/reenviar/{id}', [ReservaController::class, 'reenviar_pendencia'])->name('reserva.reenviar');
    Route::post('/hospedagem/reenviar/{id}', [HospedagemController::class, 'reenviar_pendencia'])->name('hospedagem.reenviar');





    Route::get('/reserva/veiculo-leve', [VeiculoController::class, 'index'])->name('reserva.veiculo');
    Route::post('/reserva/veiculo', [VeiculoController::class, 'store']);
    Route::post('/cancelar-veiculo/{id}', [VeiculoController::class, 'cancelarVeiculo'])->name('cancelar.veiculo');
    Route::post('/finalizar-veiculo/{id}', [VeiculoController::class, 'finalizarVeiculo'])->name('finalizar.veiculo');


    Route::get('/reserva/hospedagem', [HospedagemController::class, 'index'])->name('reserva.hospedagem');
    Route::post('/reserva/hospedagem', [HospedagemController::class, 'store']);
    Route::post('/cancelar-hospedagem/{id}', [HospedagemController::class, 'cancelarHospedagem'])->name('cancelar.hospedagem');
    Route::post('/finalizar-hospedagem/{id}', [HospedagemController::class, 'finalizarHospedagem'])->name('finalizar.hospedagem');




    Route::get('/reserva/adiantamento', [AdiantamentoController::class, 'index'])->name('reserva.adiantamento');
    Route::post('/reserva/adiantamento', [AdiantamentoController::class, 'store']);
    Route::post('/cancelar-adiantamento/{id}', [AdiantamentoController::class, 'cancelarAdiantamento'])->name('cancelar.adiantamento');
    Route::post('/finalizar-adiantamento/{id}', [AdiantamentoController::class, 'finalizarAdiantamento'])->name('finalizar.adiantamento');




    Route::get('/reserva/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/reserva/sobre', [SobreController::class, 'index'])->name('reserva.sobre');

    Route::get('/reserva/indicadores', [DashboardController::class, 'exibirBI'])->name('reserva.bi');

    Route::get('/admin/financeiro/dashboard', [FinanceiroController::class, 'dashboard'])->name('admin.financeiro-dashboard');


    Route::get('/pneu/indicadores', [DashboardController::class, 'exibirBI_PNEU'])->name('pneu.bi');

    Route::get('/manutencao/indicadores', [DashboardController::class, 'exibirBI_MANUTENCAO'])->name('manutencao.bi');

    Route::get('/florestal/indicadores', [DashboardController::class, 'exibirBI_florestal'])->name('florestal.bi');





    // Em routes/web.php — remova depois de usar!
    Route::get('/limpar-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        return 'Cache e views limpos!';
    });


    Route::get('/exportar-fiscais', [FiscalController::class, 'exportar'])->name('exportar.fiscais');

    Route::get('/fiscal/aprovar/{token}', [FiscalController::class, 'aprovar'])->name('fiscal.aprovar');
    Route::get('/fiscal/reprovar/{token}', [FiscalController::class, 'reprovar'])->name('fiscal.reprovar');

    Route::get('/fiscal/aprovacoes', [FiscalController::class, 'aprovacao'])->name('fiscal.aprovacao');
    Route::post('/fiscal/emitir-nf/{id}', [FiscalController::class, 'emitirNf'])->name('emitir.nf');
    Route::post('/fiscal/credito-pendente/{id}', [FiscalController::class, 'creditoPendente'])->name('credito.pendente');
    Route::post('/fiscal/filial-pendente/{id}', [FiscalController::class, 'filialPendente'])->name('filial.pendente');
    Route::post('/fiscal/filial-retorno/{id}', [FiscalController::class, 'filialRetorno'])->name('filial.retorno');
    Route::post('/fiscal/concluido/{id}', [FiscalController::class, 'concluido'])->name('fiscal.concluido');
    Route::post('/fiscal/reenviar/{id}', [FiscalController::class, 'reenviar_pendencia'])->name('fiscal.reenviar');
    Route::post('/fiscal/reprovado/{id}', [FiscalController::class, 'fiscal_reprovar'])->name('fiscal.reprovado');
    Route::post('/fiscal/confirmacao_entrega/{id}', [FiscalController::class, 'confirmacao_entrega'])->name('filial.confirmacao_entrega');



    Route::get('/reserva/aprovar/{token}', [ReservaController::class, 'aprovar'])->name('reserva.aprovar');
    Route::get('/reserva/reprovar/{token}', [ReservaController::class, 'reprovar'])->name('reserva.reprovar');

    Route::get('/hospedagem/aprovar/{token}', [HospedagemController::class, 'aprovar'])->name('hospedagem.aprovar');
    Route::get('/hospedagem/reprovar/{token}', [HospedagemController::class, 'reprovar'])->name('hospedagem.reprovar');

    Route::get('/produto/aprovar/{token}', [ProdutoController::class, 'aprovar'])->name('produto.aprovar');
    Route::get('/produto/reprovar/{token}', [ProdutoController::class, 'reprovar'])->name('produto.reprovar');


    Route::get('/financeiro/aprovar/{token}', [FinanceiroFrController::class, 'aprovar'])->name('financeiro.aprovar');
    Route::get('/financeiro/reprovar/{token}', [FinanceiroFrController::class, 'reprovar'])->name('financeiro.reprovar');


    Route::get('/adiantamento/aprovar/{token}', [AdiantamentoController::class, 'aprovar'])->name('adiantamento.aprovar');
    Route::post('/adiantamento/reprovar/{token}', [AdiantamentoController::class, 'reprovar'])->name('adiantamento.reprovar');
    Route::get('/adiantamento/reprovar/{token}', [AdiantamentoController::class, 'formReprovar'])->name('adiantamento.reprovar.form');



    Route::get('/pagamento_pix/aprovar/{token}', [PagamentoPixController::class, 'aprovar'])->name('pagamento_pix.aprovar');
    Route::get('/pagamento_pix/reprovar/{token}', [PagamentoPixController::class, 'reprovar'])->name('pagamento_pix.reprovar');


    Route::get('/empresa/aprovacao', [EmpresaController::class, 'aprovacao'])->name('empresa.aprovacao');
    Route::get('/empresa/aprovar/{token}', [EmpresaController::class, 'aprovar'])->name('empresa.aprovar');
    Route::post('/empresa/reprovar/{token}', [EmpresaController::class, 'reprovar'])->name('empresa.reprovar');
    Route::get('/empresa/reprovar/{token}', [EmpresaController::class, 'formReprovar'])->name('empresa.reprovar.form');




    Route::get('/rdv', [RdvController::class, 'index'])->name('rdv.index');

    Route::get('/rdv/despesa', [RdvController::class, 'create'])->name('rdv.despesas');
    Route::post('/rdv/despesa/post', [RdvController::class, 'store'])->name('despesa.store');

    Route::get('/rdv/relatorio', [RdvController::class, 'relatorio'])->name('rdv.relatorio');
    Route::post('/rdv/relatorio/post', [RdvController::class, 'store_relatorio'])->name('relatorio.store');


    // Tela que o gestor acessa pelo link do e-mail
    Route::get('/relatorio/analise/{token}', [RdvController::class, 'telaAprovacao'])->name('relatorio.tela_aprovacao');

    // Rota para o gestor aprovar/reprovar CADA despesa (item por item)
    Route::post('/despesa/{id}/status', [RdvController::class, 'updateStatus'])->name('despesa.status');

    // Rota para o botão final de aprovação do Relatório todo
    Route::post('/relatorio/{id}/finalizar', [RdvController::class, 'finalizar'])->name('relatorio.finalizar');

    Route::get('/relatorio/resumo', [RdvController::class, 'resumo'])->name('relatorio.resumo');

    Route::post('/cancelar-relatorio/{id}', [RdvController::class, 'cancelarRelatorio'])->name('cancelar.relatorio');

    Route::post('/finalizar-relatorio/{id}', [RdvController::class, 'finalizarRelatorio'])->name('finalizar.relatorio');

    Route::post('/cancelar-relatorio/{id}', [RdvController::class, 'cancelarRelatorio'])->name('cancelar.relatorio');

    Route::post('/cancelar-despesa/{id}', [RdvController::class, 'cancelarDespesa'])->name('cancelar.despesa');



    Route::get('/financeiro/resumo', [FinanceiroFrController::class, 'resumo'])->name('financeiro.resumo');

    Route::get('/financeiro/finalizados', [FinanceiroFrController::class, 'indexFinalizados'])->name('financeiro.finalizados');


    Route::post('/atualizar-nota-fiscal/{id}', [FinanceiroFrController::class, 'updateFiscalStatus'])
        ->name('update.fiscal.status');

    Route::post('/financeiro/aprovar_financeiro/{id}', [FinanceiroFrController::class, 'aprovar_financeiro'])->name('financeiro.aprovar_financeiro');
    Route::post('/financeiro/reprovar_financeiro/{id}', [FinanceiroFrController::class, 'reprovar_financeiro'])->name('financeiro.reprovar_financeiro');

    Route::get('/financeiro/reprovar/solicitacao/{id}', [FinanceiroFrController::class, 'formReprovar'])->name('financeiro.reprovar.form');

    // Rota para consulta rápida de pedido via AJAX
    Route::get('/financeiro/consultar-pedido/{pedido}', [FinanceiroFrController::class, 'consultarPedido'])
        ->name('financeiro.consultar_pedido');

    Route::get('/financeiro/indicadores', [FinanceiroFrController::class, 'exibirBI'])->name('financeiro.bi');



    Route::post('/financeiro/finalizar_reembolso/{id}', [FinanceiroFrController::class, 'finalizar_reembolso'])->name('financeiro.finalizar_reembolso');



    // Route::get('/processar-massa-financeiro-xyz123', [FinanceiroFrController::class, 'dispararEmailsAtrasados'])
    //     ->middleware('auth'); // Garante que só você logado consiga disparar


    Route::get('/consultar-status/{numped}', function ($numped) {
        // Se não houver número de pedido, retorna N/A
        if (!$numped || $numped == 'N/I') {
            return response()->json(['situacao' => '(N/A)', 'codfil' => null]);
        }

        $resultado = DB::connection('sqlsrv')->select("
            SELECT 
            CODFIL,
                CASE SITUAC
                    WHEN 'A' THEN 'APROVADO'
                    WHEN 'B' THEN 'BAIXADO'
                    WHEN 'C' THEN 'CANCELADO'
                    WHEN 'D' THEN 'LIBERADO'
                    WHEN 'P' THEN 'PENDENTE'
                    WHEN 'R' THEN 'REPROVADO'
                    WHEN 'X' THEN 'BAIXADO PARCIAL'
                    ELSE 'OUTRO'
                END AS situacao
            FROM ESTPED
            WHERE NUMPED = ?
        ", [$numped]);

        if (count($resultado) > 0) {
            return response()->json([
                'situacao' => $resultado[0]->situacao,
                'codfil'   => $resultado[0]->CODFIL
            ]);
        }

        return response()->json(['situacao' => '(N/A)', 'codfil' => null]);

        // $textoStatus = count($resultado) > 0 ? $resultado[0]->situacao : '(N/A)';

        // return response()->json(['situacao' => $textoStatus, 'codfil' => null]);
    });

    Route::get('/financeiro/buscar', [FinanceiroFrController::class, 'buscar'])->name('financeiro.buscar');


    Route::get('/notificacoes/buscar', [NotificationController::class, 'fetch'])->name('notifications.fetch');

    // Rota para marcar uma notificação específica como lida
    Route::get('/notificacoes/ler/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');


    Route::get('/fix-permissions', function () {
        $path = storage_path('app/public/zips');
        
        if (file_exists($path)) {
            // Tenta definir a permissão para 775
            chmod($path, 0777);
            return "Permissões de '{$path}' alteradas para 775 com sucesso!";
        }
        
        return "Diretório não encontrado.";
    });

    Route::get('/despesa/pix/{relatorio_id}', [RdvController::class, 'createPix']);
    Route::post('/despesa/pix/store', [RdvController::class, 'storePix'])->name('despesa.store_pix');


    // Route::get('/storage/despesas/{filename}', function ($filename) {
    //     $path = 'public/despesas/' . $filename;

    //     if (!Storage::exists($path)) {
    //         abort(404);
    //     }

    //     $file = Storage::get($path);
    //     $type = Storage::mimeType($path);

    //     return Response::make($file, 200)->header("Content-Type", $type);
    // });


    // ROTAS PARA CHAT
    // Rotas para o chat
    Route::middleware('auth')->group(function () {
        // 1. Enviar nova mensagem (POST)
        Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
        
        // 2. Buscar mensagens novas (GET)
        Route::get('/chat/fetch', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
    });





    // Somente usuários com o setor 'admin' acessam essa gestão
    Route::middleware(['auth', 'setor:admin'])->group(function () {
        Route::get('/admin/painel', [AdminController2::class, 'painel'])->name('admin.painel');
        Route::put('/admin/usuarios/{user}/setores', [AdminController2::class, 'atualizarPermissoes'])->name('admin.setores.update');
        
        Route::post('/admin/usuarios/{id}/toggle-status', [AdminController2::class, 'toggleStatus']);

        // ADICIONE ESTA LINHA ABAIXO:
        Route::post('/admin/usuarios/{id}/toggle-setor', [AdminController2::class, 'toggleSetor']);
    });



    // Rota para disparar o comando manualmente
    Route::get('/admin/atualizar-saldos', function () {
        // Chama o comando que você criou via código
        Artisan::call('tabela:atualizar');

        return back()->with('success', 'Saldos do Gustavo, Ronaldo e demais gestores atualizados com sucesso!');
    })->middleware(['auth', 'setor:admin,diretoria']); // Importante: Proteja essa rota!


    // // Somente usuários com o setor 'admin' acessam essa gestão
    // Route::middleware(['auth'])->group(function () {
    // });


    Route::get('/reset-bi', function () {
        Cache::forget('pbi_access_token');
        Cache::forget('pbi_embed_token_' . env('POWERBI_REPORT_ID'));
        Cache::forget('pbi_embed_token_' . env('POWERBI_REPORT_ID_PNEU'));
        Cache::forget('pbi_embed_token_' . env('POWERBI_REPORT_ID_RESERVA'));

        return redirect()->route('financeiro.bi'); // Volta para a página do BI
    });

    // ROTA PARA SABER STATUS DO SERVIDOR (RAM, DISCO, CPU)

    Route::get('/server-monitor', function () {
        // 1. MEMÓRIA RAM (Sistema)
        $free = shell_exec('free -m');
        if ($free) {
            $data = explode("\n", trim($free));
            $mem = explode(" ", preg_replace("/\s+/", " ", $data[1]));
            $ram_total = $mem[1] . ' MB';
            $ram_uso = $mem[2] . ' MB';
        } else {
            // Fallback caso shell_exec esteja bloqueado
            $ram_total = ini_get('memory_limit');
            $ram_uso = round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB (Script)';
        }

        // 2. DISCO (HD)
        $disk_total = round(disk_total_space("/") / (1024 * 1024 * 1024), 2) . ' GB';
        $disk_free = round(disk_free_space("/") / (1024 * 1024 * 1024), 2) . ' GB';
        $disk_uso = round(floatval($disk_total) - floatval($disk_free), 2) . ' GB';

        // 3. CPU / CARGA DO SISTEMA
        $load = sys_getloadavg(); // Retorna carga em 1, 5 e 15 min

        return response()->json([
            'gerenciador_de_tarefas' => [
                'memoria_ram' => [
                    'total_servidor' => $ram_total,
                    'uso_atual' => $ram_uso,
                    'pico_do_laravel' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
                ],
                'armazenamento_hd' => [
                    'total' => $disk_total,
                    'usado' => $disk_uso,
                    'livre' => $disk_free,
                ],
                'processador_cpu' => [
                    'carga_media_1min' => $load[0],
                    'uso_php' => php_uname('m'), // Arquitetura
                ],
                'power_bi_status' => [
                    'token_em_cache' => Cache::has('pbi_access_token') ? 'Sim' : 'Não',
                ]
            ],
            'aviso' => 'Placa de Vídeo (GPU) não disponível em ambiente de hospedagem compartilhada.'
        ]);
    })->middleware(['auth']);







    Route::get('/implantacao_saldo', [ImplantacaoSaldoController::class, 'index'])->name('implantacao_saldo.index');

    Route::post('/implantacao_saldo', [ImplantacaoSaldoController::class, 'store'])->name('implantacao_saldo.store');


    Route::get('/menu/implantacao_saldo', [ImplantacaoSaldoController::class, 'menu'])->name('implantacao_saldo.menu');



    Route::get('/implantacao/aprovar/{id}', [ImplantacaoSaldoController::class, 'aprovar'])->name('implantacao_saldo.aprovar');
    Route::get('/implantacao/reprovar/{id}', [ImplantacaoSaldoController::class, 'reprovar'])->name('implantacao_saldo.reprovar');



    Route::get('/implantacao/monitoramento', [ImplantacaoSaldoController::class, 'monitoramento'])->name('implantacao_saldo.monitoramento');
    Route::get('/implantacao/reenviar-email/{id}', [ImplantacaoSaldoController::class, 'reenviarEmail'])->name('implantacao_saldo.reenviar_email');

    Route::post('/implantacao-saldo/item-temporario', [ImplantacaoSaldoController::class, 'adicionarItemTemporario'])->name('implantacao_saldo.add_temp');
    Route::post('/implantacao-saldo/item-temporario/remover', [ImplantacaoSaldoController::class, 'removerItemTemporario'])->name('implantacao_saldo.remove_temp');

    Route::get('/implantacao-saldo/finalizar/{id}', [ImplantacaoSaldoController::class, 'finalizar'])->name('implantacao_saldo.finalizar');



    Route::get('/ajuda_de_custo', [AjudaDeCustoController::class, 'index'])->name('ajuda_de_custo.index');
    Route::post('/ajuda_de_custo', [AjudaDeCustoController::class, 'store'])->name('ajuda_custo.store');


    Route::get('/ajuda-custo/aprovar/{token}', [AjudaDeCustoController::class, 'aprovar'])->name('ajuda_custo.aprovar');
    Route::get('/ajuda-custo/reprovar/{token}', [AjudaDeCustoController::class, 'exibirFormReprovar'])->name('ajuda_custo.form_reprovar');
    Route::post('/ajuda-custo/reprovar/{token}', [AjudaDeCustoController::class, 'reprovar'])->name('ajuda_custo.post_reprovar');




    Route::post('/financeiro/configuracao/status-pix', [FinanceiroFrController::class, 'atualizarStatusPix'])->name('financeiro.atualizarStatusPix');

    Route::post('/financeiro/disparar-pix', [FinanceiroFrController::class, 'processarPix'])->name('financeiro.dispararPix');


    // Rota para exibir a página do formulário (GET)
    Route::get('/abastecimento/importar', [AbastecimentoController::class, 'index'])->name('abastecimento.importar.index');

    // Rota para processar o envio do arquivo CSV (POST)
    Route::post('/abastecimento/importar', [AbastecimentoController::class, 'importar'])->name('abastecimento.importar');

    // Rota para descarregar o CSV de exemplo
    Route::get('/abastecimento/exemplo-csv', [AbastecimentoController::class, 'exemploCsv'])->name('abastecimento.exemploCsv');

    Route::post('/implantacao-saldo/importar-csv-temp', [ImplantacaoSaldoController::class, 'importarCsvTemp'])->name('implantacao_saldo.importar_csv_temp');

    // Rota para descarregar o modelo CSV de Ajuste de Saldo
    Route::get('/implantacao-saldo/exemplo-csv', [ImplantacaoSaldoController::class, 'exemploCsv'])->name('implantacao_saldo.exemplo_csv');


}); // Fim do grupo auth

//ROTAS DA ORDEM DE SERVIÇO
// LOGIN
Route::get('/os/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/os/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/os/logout', [AuthController::class, 'logout'])->name('logout');


// Redireciona / para login ou dashboard
Route::get('/os', function() {
    return redirect()->route('login');
});

// ROTAS PROTEGIDAS COM 'auth:os'
Route::middleware('auth.os:os')->group(function () {

    // DASHBOARD
    Route::get('/os/dashboard', [OSController::class, 'index'])->name('dashboard');
    Route::get('/os/dashboard/{codord}/{codire}/{codvei}', [OSController::class, 'details'])->name('site.details');

    Route::get('/os/servicos', [OSController::class, 'servicos_realizados'])->name('servicos');

    // AÇÕES DE OS
    Route::post('/os/assign/{codord}/{codire}', [OSController::class, 'assign'])->name('os.assign');
    Route::post('/os/action/{codord}/{codire}', [OSController::class, 'updateStatus'])->name('os.action');
    Route::delete('/os/delete/{codord}/{codire}', [OSController::class, 'delete'])->name('os.delete');


    // BOTÕES DE CONTROLE
    Route::post('/os/{codord}/{codire}/start', [OSController::class, 'start'])->name('os.start');
    Route::post('/os/{codord}/{codire}/pause', [OSController::class, 'pause'])->name('os.pause');
    Route::post('/os/{codord}/{codire}/resume', [OSController::class, 'resume'])->name('os.resume');
    Route::post('/os/{codord}/{codire}/finish', [OSController::class, 'finish'])->name('os.finish');

    Route::post('/os/update-time', [OSController::class, 'updateTime'])->name('os.update_time');
    Route::delete('/os/delete/{codord}/{codire}', [OSController::class, 'delete'])->name('os.delete');
    Route::delete('/os/delete/{id}', [OSController::class, 'delete_service'])->name('os.delete_service');


    




    // ROTAS DE ADMIN – sem proteção adicional
    Route::get('/os/admin/create', [AdminController::class, 'showCreateAdminForm'])->name('admin.create.form');
    Route::post('/os/admin/create', [AdminController::class, 'createAdmin'])->name('admin.create');
    Route::get('/os/admin/update', [AdminController::class, 'showUpdateUnidade'])->name('admin.update.form');
    Route::post('/os/admin/update', [AdminController::class, 'updateUnidade'])->name('admin.update');

    // ROTAS DE OS – atribuição
    Route::get('/os/atribuir/{codord}', [OsController::class, 'showAssignOsForm'])->name('site.assignOs');
    Route::post('/os/atribuir/{codord}', [OsController::class, 'assignOsToMechanic'])->name('site.assignOs.submit');

    // ROTA ATRIBUIR OS SELECIONADAS
    Route::post('/os/atribuir-servicos', [OSController::class, 'assignSelectedServices'])->name('site.assignSelectedServices');

});