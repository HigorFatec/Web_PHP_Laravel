<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FornecedorFisicoController;
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



//ORDEM DE SERVIÇO
use App\Http\Controllers\OSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;


use Illuminate\Support\Facades\DB;
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
    ->where('route', 'financeiro.index|empresa.create|fiscal.index|pagamento_pix.index|florestal_pix.index|saldo.index|produtos.create|transf_veiculo.index|descarte.index|sinistro.index')
    ->name('goto.route');


Route::get('/', [HomeController::class, 'index'])->name('index');
Route::resource('users', UserController::class);


Route::get('/fornecedor_juridico', [EmpresaController::class, 'create'])->name('empresa.create')->middleware('only.from.home');
Route::get('/fornecedor_fisico', [FornecedorFisicoController::class, 'create'])->name('fisico.fornecedor_fisico');
Route::get('/produtos', [ProdutoController::class, 'create'])->name('produtos.create')->middleware('only.from.home');
Route::get('/transf_veiculo', [TransfVeiculoController::class, 'index'])->name('transf_veiculo.index');


Route::get('/pagamento_pix', [PagamentoPixController::class, 'index'])->name('pagamento_pix.index');
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

Route::get('/fiscal', [FiscalController::class, 'index'])->name('fiscal.index');


//Descarte de Pneus
Route::get('/descarte', [DescartePneusController::class, 'index'])->name('descarte.index')->middleware('only.from.home');
Route::post('/descarte/store', [DescartePneusController::class, 'store'])->name('descarte.store');


Route::post('/empresa/store', [EmpresaController::class, 'store'])->name('empresa.store');
Route::post('/fornecedor_fisico/store', [FornecedorFisicoController::class, 'store'])->name('fornecedor_fisico.store');
Route::post('/produtos/store', [ProdutoController::class, 'store'])->name('produtos.store');
Route::post('/transf_veiculo/store', [TransfVeiculoController::class, 'store'])->name('transf_veiculo.store');

Route::post('/pagamento_pix', [PagamentoPixController::class, 'store'])->name('pagamento_pix.store');

Route::post('/florestal_pix', [FlorestalPixController::class, 'store'])->name('florestal_pix.store');

Route::post('/financeiro', [FinanceiroController::class, 'store'])->name('financeiro.store');

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




Route::view('/login', 'login.form')->name('login.form');
Route::post('/auth', [LoginController::class, 'auth'])->name('login.auth');
Route::get('/logout', [LoginController::class, 'logout'])->name('login.logout');
Route::get('/register', [LoginController::class, 'create'])->name('login.create');

Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');




// Em routes/web.php — remova depois de usar!
Route::get('/limpar-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return 'Cache e views limpos!';
});



Route::get('/fiscal/aprovar/{token}', [FiscalController::class, 'aprovar'])->name('fiscal.aprovar');
Route::get('/fiscal/reprovar/{token}', [FiscalController::class, 'reprovar'])->name('fiscal.reprovar');

Route::get('/fiscal/aprovacoes', [FiscalController::class, 'aprovacao'])->name('fiscal.aprovacao');
Route::post('/fiscal/emitir-nf/{id}', [FiscalController::class, 'emitirNf'])->name('emitir.nf');
Route::post('/fiscal/credito-pendente/{id}', [FiscalController::class, 'creditoPendente'])->name('credito.pendente');
Route::post('/fiscal/filial-pendente/{id}', [FiscalController::class, 'filialPendente'])->name('filial.pendente');
Route::post('/fiscal/concluido/{id}', [FiscalController::class, 'concluido'])->name('fiscal.concluido');




//ROTAS DA ORDEM DE SERVIÇO
// LOGIN
Route::get('/os/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/os/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/os/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/os/atribuir-servicos', [OSController::class, 'assignSelectedServices'])->name('site.assignSelectedServices');

// Redireciona / para login ou dashboard
Route::get('/os', function() {
    return redirect()->route('login');
});

// ROTAS PROTEGIDAS COM 'auth:os'
Route::middleware('auth:os')->group(function () {

    // DASHBOARD
    Route::get('/os/dashboard', [OSController::class, 'index'])->name('dashboard');
    Route::get('/os/dashboard/{codord}/{codire}', [OSController::class, 'details'])->name('site.details');



    // AÇÕES DE OS
    Route::post('/os/assign/{codord}/{codire}', [OSController::class, 'assign'])->name('os.assign');
    Route::post('/os/action/{codord}/{codire}', [OSController::class, 'updateStatus'])->name('os.action');

    // BOTÕES DE CONTROLE
    Route::post('/os/{codord}/{codire}/start', [OSController::class, 'start'])->name('os.start');
    Route::post('/os/{codord}/{codire}/pause', [OSController::class, 'pause'])->name('os.pause');
    Route::post('/os/{codord}/{codire}/resume', [OSController::class, 'resume'])->name('os.resume');
    Route::post('/os/{codord}/{codire}/finish', [OSController::class, 'finish'])->name('os.finish');

    // ROTAS DE ADMIN – sem proteção adicional
    Route::get('/os/admin/create', [AdminController::class, 'showCreateAdminForm'])->name('admin.create.form');
    Route::post('/os/admin/create', [AdminController::class, 'createAdmin'])->name('admin.create');

    // ROTAS DE OS – atribuição
    Route::get('/os/atribuir/{codord}', [OsController::class, 'showAssignOsForm'])->name('site.assignOs');
    Route::post('/os/atribuir/{codord}', [OsController::class, 'assignOsToMechanic'])->name('site.assignOs.submit');
});
