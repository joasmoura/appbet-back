<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CaixaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function caixa_gerentes(Request $request){
        $gerentes = User::with('movimentacoes','cambistas_gerente','comissao_aposta')->where('perfil','gerente')->paginate(10);

        if($gerentes->first()){
            foreach($gerentes as $key => $gerente){
                $creditos = $gerente->movimentacoes()->where('tipo','credito')->sum('valor');
                $retiradas = $gerente->movimentacoes()->where('tipo','retirada')->sum('valor');

                $cambistas = $gerente->cambistas_gerente()->get();
                $entradas = 0;

                if($cambistas->first()){
                    foreach($cambistas as $cambista){
                        $entradas += (float) $cambista->apostas()->sum('total');
                    }
                }

                $gerentes[$key]['creditos'] = $creditos;
                $gerentes[$key]['retiradas'] = $retiradas;
                $gerentes[$key]['entradas'] = $entradas;
                $gerentes[$key]['saidas'] = (float) $gerente->comissao_aposta()->sum('valor');
            }
        }
        return $gerentes;
    }

    public function caixa_supervisores(Request $request){
        $supervisores = User::where('perfil','supervisor','cambistas_supervisor')->paginate(10);
        if($supervisores->first()){
            foreach($supervisores as $key => $supervisor){
                $creditos = $supervisor->movimentacoes()->where('tipo','credito')->sum('valor');
                $retiradas = $supervisor->movimentacoes()->where('tipo','retirada')->sum('valor');

                $cambistas = $supervisor->cambistas_supervisor()->get();
                $entradas = 0;

                if($cambistas->first()){
                    foreach($cambistas as $cambista){
                        $entradas += (float) $cambista->apostas()->sum('total');
                    }
                }

                $supervisores[$key]['creditos'] = $creditos;
                $supervisores[$key]['retiradas'] = $retiradas;
                $supervisores[$key]['entradas'] = $entradas;
            }
        }
        return $supervisores;
    }

    public function caixa_cambistas(Request $request){
        $cambistas = User::with('comissao_aposta','apostas','movimentacoes')->where('perfil','cambista')->paginate(10);

        $dataInicio = ($request['dataInicio'] ? dataParaBanco($request['dataInicio']) : null);
        $dataFim = ($request['dataFim'] ? dataParaBanco($request['dataFim']) : null);

        if($cambistas->first()){
            $entradas = 0;
            foreach($cambistas as $key => $cambista){
                $movimentacoes = $cambista->movimentacoes();
                $creditos = $movimentacoes->where(function($query) use($dataInicio, $dataFim) {
                    $query->where('tipo','credito');
                    $query->whereDate('data', '>=', $dataInicio);
                    $query->whereDate('data', '<=', $dataFim);
                })->sum('valor');

                $retiradas = $movimentacoes->where(function($query) use($dataInicio, $dataFim){
                    $query->where('tipo','retirada');
                    $query->whereDate('data', '>=', $dataInicio);
                    $query->whereDate('data', '<=', $dataFim);
                })->sum('valor');

                $apostas = $cambista->apostas()->with('itens')->where(function($query) use($dataInicio, $dataFim){
                    $query->where('status','!=','cancelado');
                    $query->whereDate('created_at', '>=', $dataInicio);
                    $query->whereDate('created_at', '<=', $dataFim);
                });

                $saldo_anterior = $this->saldo_anterior($cambista,$dataInicio);
                $cambistas[$key]['saldoAnterior'] = $saldo_anterior;
                $entradas = $apostas->sum('total');

                $cambistas[$key]['creditos'] = $creditos;
                $cambistas[$key]['retiradas'] = $retiradas;
                $cambistas[$key]['entradas'] = $entradas;

                $valoresSorteados = 0;
                $todasApostas = $apostas->get();
                $comissoes = 0;
                if($todasApostas->first()){
                    foreach($todasApostas as $aposta){
                        $itens = $aposta->itens()->with('sorteados')->get();
                        $comissoes += $aposta->comissao_aposta()->where(function($query) use($dataInicio, $dataFim) {
                            $query->whereDate('created_at', '>=', $dataInicio);
                            $query->whereDate('created_at', '<=', $dataFim);
                        })->sum('valor');;

                        if($itens->first()){
                            foreach($itens as $item){
                                $valoresSorteados += $item->sorteados()->sum('valor');
                            }
                        }
                    }
                }
                $cambistas[$key]['saidas'] = $comissoes+$valoresSorteados;
                $cambistas[$key]['saldo'] = ($saldo_anterior+$entradas+$creditos)-($cambistas[$key]['saidas']+$retiradas);
            }
        }
        return $cambistas;
    }

    public function caixa_cambista(Request $request){
        $usuario = auth()->user();

        $datas = $request->datas;
        $dataInicio = ($datas['dataInicio'] ? date('Y-m-d',strtotime($datas['dataInicio'])) : null);
        $dataFim = ($datas['dataFim'] ? date('Y-m-d',strtotime($datas['dataFim'])) : null);

        $usuario->load('comissao_aposta','apostas','movimentacoes');

        $movimentacoes = $usuario->movimentacoes();

        $creditos = $movimentacoes->where(function($query) use($dataInicio, $dataFim) {
            $query->where('tipo','credito');
            $query->whereDate('data', '>=', $dataInicio);
            $query->whereDate('data', '<=', $dataFim);
        })->sum('valor');

        $retiradas = $movimentacoes->where(function($query) use($dataInicio, $dataFim){
            $query->where('tipo','retirada');
            $query->whereDate('data', '>=', $dataInicio);
            $query->whereDate('data', '<=', $dataFim);
        })->sum('valor');

        $apostas = $usuario->apostas()->with('itens')->where(function($query) use($dataInicio, $dataFim){
            $query->where('status','!=','cancelado');
            $query->whereDate('created_at', '>=', $dataInicio);
            $query->whereDate('created_at', '<=', $dataFim);
        })->get();

        $saldo_anterior = $this->saldo_anterior($usuario,$dataInicio);
        $entradas = 0;// Soma dos valores das apostas feitas

        $usuario['creditos'] = $creditos;
        $usuario['retiradas'] = $retiradas;
        $usuario['entradas'] = $entradas;

        $valoresSorteados = 0;
        $comissoes = 0;
        if($apostas->first()){
            foreach($apostas as $aposta){
                $itens = $aposta->itens()->with('sorteados')->get();
                if($itens->first()){
                    foreach($itens as $item){
                        $entradas += $aposta->total;
                        $valoresSorteados += $item->sorteados()->sum('valor');// Soma dos valores dos prêmios do cambista
                        $comissoes += $aposta->comissao_aposta()->where(function($query) use($dataInicio, $dataFim) {
                            $query->whereDate('created_at', '>=', $dataInicio);
                            $query->whereDate('created_at', '<=', $dataFim);
                        })->sum('valor');
                    }
                }
            }
        }

        $usuario['saidas'] = (float) $comissoes+$valoresSorteados;
        $usuario['premios'] = $valoresSorteados;

        $usuario['valorApostas'] = $entradas;
        $usuario['valorComissoes'] = $comissoes;

        $usuario['saldoAnterior'] = $saldo_anterior;
        $usuario['saldo'] = ($saldo_anterior+$entradas+$creditos)-($usuario['saidas']+$retiradas);
        return $usuario;
    }

    public function saldo_anterior($cambista, $dataInicio){
        $apostas = $cambista->apostas()->with('itens')->where(function($query) use($dataInicio){
            $query->where('status','!=','cancelado');
            $query->whereDate('created_at', '<', $dataInicio);
        })->get();

        $movimentacoes = $cambista->movimentacoes();

        $creditos = $movimentacoes->where(function($query) use($dataInicio) {
            $query->where('tipo','credito');
            $query->whereDate('data', '<', $dataInicio);
        })->sum('valor');

        $retiradas = $movimentacoes->where(function($query) use($dataInicio){
            $query->where('tipo','retirada');
            $query->whereDate('data', '<', $dataInicio);
        })->sum('valor');

        $entradas = 0;

        $valoresSorteados = 0;
        $comissoes = 0;
        if($apostas->first()){
            foreach($apostas as $aposta){
                $itens = $aposta->itens()->with('sorteados')->get();
                $entradas += $aposta->total;
                if($itens->first()){
                    foreach($itens as $item){
                        $valoresSorteados += $item->sorteados()->sum('valor');// Soma dos valores dos prêmios do cambista
                        $comissoes += $aposta->comissao_aposta()->where(function($query) use($dataInicio) {
                            $query->whereDate('created_at', '>=', $dataInicio);
                        })->sum('valor');
                    }
                }
            }
        }

        $saidas = (float) $comissoes+$valoresSorteados+$retiradas;

        $totalEntradas = (float) $creditos+$entradas;

        $saldo = $totalEntradas-$saidas;

        return $saldo;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
