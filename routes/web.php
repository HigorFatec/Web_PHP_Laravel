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


Route::get('/', [HomeController::class, 'index'])->name('index');
Route::resource('users', UserController::class);


Route::get('/fornecedor_juridico', [EmpresaController::class, 'create'])->name('empresa.create');
Route::get('/fornecedor_fisico', [FornecedorFisicoController::class, 'create'])->name('fisico.fornecedor_fisico');
Route::get('/produtos', [ProdutoController::class, 'create'])->name('produtos.create');


Route::post('/empresa/store', [EmpresaController::class, 'store'])->name('empresa.store');
Route::post('/fornecedor_fisico/store', [FornecedorFisicoController::class, 'store'])->name('fornecedor_fisico.store');
Route::post('/produtos/store', [ProdutoController::class, 'store'])->name('produtos.store');

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