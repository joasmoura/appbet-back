<?php

use App\Http\Controllers\ApostaController;
use App\Http\Controllers\CaixaController;
use App\Http\Controllers\ComissaoController;
use App\Http\Controllers\ExtracaoController;
use App\Http\Controllers\MercadoController;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\RegiaoController;
use App\Http\Controllers\ResultadoController;
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

Route::post('/login/{base}',[UsuarioController::class,'login']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->prefix('painel')->group(function(){
    Route::get('/regioes/select',[RegiaoController::class,'select']);
    Route::resource('/regioes',RegiaoController::class);

    Route::get('/usuario/limite',[UsuarioController::class,'limite']);
    Route::get('/usuarios/gerentes_select',[UsuarioController::class,'gerentes_select']);
    Route::get('/usuarios/gerentes',[UsuarioController::class,'gerentes']);
    Route::get('/usuarios/supervisores_select',[UsuarioController::class,'supervisores_select']);
    Route::get('/usuarios/supervisores',[UsuarioController::class,'supervisores']);
    Route::get('/usuarios/cambistas_select',[UsuarioController::class,'cambistas_select']);
    Route::get('/usuarios/cambistas',[UsuarioController::class,'cambistas']);
    Route::get('/usuarios/selectCambistas',[UsuarioController::class,'selectCambistas']);

    Route::resource('/usuarios',UsuarioController::class);

    Route::get('/comissoes/select',[ComissaoController::class,'select']);
    Route::resource('/comissoes',ComissaoController::class);

    Route::post('/extracoes/consultar_resultado',[ExtracaoController::class,'consultarResultado']);
    Route::get('/extracoes/extracoes_cambista',[ExtracaoController::class,'extracoes_cambista']);
    Route::get('/extracoes/setar_status/{id}',[ExtracaoController::class,'setarStatus']);
    Route::post('/extracoes/salvar_premios/{id}',[ExtracaoController::class,'salvarPremios']);
    Route::get('/extracoes/hora/{id}',[ExtracaoController::class,'hora']);
    Route::get('/extracoes/removerHora/{id}',[ExtracaoController::class,'removerHora']);
    Route::resource('/extracoes',ExtracaoController::class);

    Route::resource('/mercados',MercadoController::class);

    Route::get('/apostas/cancelar_aposta/{id}',[ApostaController::class,'cancelar_aposta']);
    Route::resource('/apostas',ApostaController::class);

    Route::resource('/resultados',ResultadoController::class);

    Route::resource('/movimentacao',MovimentacaoController::class);

    Route::get('/caixa/caixa_gerentes',[CaixaController::class,'caixa_gerentes']);
    Route::get('/caixa/caixa_supervisores',[CaixaController::class,'caixa_supervisores']);
    Route::get('/caixa/caixa_cambistas',[CaixaController::class,'caixa_cambistas']);
    Route::resource('/caixa',CaixaController::class);
});
