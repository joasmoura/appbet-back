<?php

use App\Http\Controllers\ComissaoController;
use App\Http\Controllers\ExtracaoController;
use App\Http\Controllers\RegiaoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login',[UsuarioController::class,'login']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->prefix('painel')->group(function(){
    Route::resource('/regioes',RegiaoController::class);

    Route::get('/usuarios/gerentes',[UsuarioController::class,'gerentes']);
    Route::get('/usuarios/supervisores',[UsuarioController::class,'supervisores']);
    Route::get('/usuarios/cambistas',[UsuarioController::class,'cambistas']);

    Route::resource('/usuarios',UsuarioController::class);

    Route::resource('/comissoes',ComissaoController::class);

    Route::get('/extracoes/removerHora/{id}',[ExtracaoController::class,'removerHora']);
    Route::resource('/extracoes',ExtracaoController::class);

});
