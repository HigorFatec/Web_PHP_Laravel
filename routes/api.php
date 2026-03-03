<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KmController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware([
    'check.supplier.token',
    'throttle:100,60' // 1 requisição a cada 60 minutos
])->group(function () {

    Route::get('/km', [KmController::class, 'index']);
    Route::get('/km/{numvei}', [KmController::class, 'show']);

});

