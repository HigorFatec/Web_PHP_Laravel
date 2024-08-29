<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FornecedorFisicoController;
use App\Http\Controllers\SobreController;

Route::get('/', [HomeController::class, 'index'])->name('index');

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


Route::view('/login', 'login.form')->name('login.form');
Route::post('/auth', [LoginController::class, 'auth'])->name('login.auth');
Route::get('/logout', [LoginController::class, 'logout'])->name('login.logout');
Route::get('/register', [LoginController::class, 'create'])->name('login.create');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('auth');