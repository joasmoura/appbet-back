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
        $cambistas = User::with('comissao_aposta','apostas')->where('perfil','cambista')->paginate(10);
        if($cambistas->first()){
            $entradas = 0;
            foreach($cambistas as $key => $cambista){
                $creditos = $cambista->movimentacoes()->where('tipo','credito')->sum('valor');
                $retiradas = $cambista->movimentacoes()->where('tipo','retirada')->sum('valor');

                $entradas = (float) $cambista->apostas()->sum('total');

                $cambistas[$key]['creditos'] = $creditos;
                $cambistas[$key]['retiradas'] = $retiradas;
                $cambistas[$key]['entradas'] = $entradas;

                $apostas = $cambista->apostas()->where('status','ganhou')->get();
                $valoresSorteados = 0;
                if($apostas->first()){
                    foreach($apostas as $aposta){
                        $itens = $aposta->itens()->with('sorteados')->get();
                        if($itens->first()){
                            foreach($itens as $item){
                                $valoresSorteados += $item->sorteados()->sum('valor');
                            }
                        }
                    }
                }
                $cambistas[$key]['saidas'] = (float) $cambista->comissao_aposta()->sum('valor')+$valoresSorteados;
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
        });

        $saldo_anterior = $this->saldo_anterior($usuario,$dataInicio);
        $entradas = (float) $apostas->sum('total');// Soma dos valores das apostas feitas

        $usuario['creditos'] = $creditos;
        $usuario['retiradas'] = $retiradas;
        $usuario['entradas'] = $entradas;

        $valoresSorteados = 0;

        $todasApostas = $apostas->get();
        if($todasApostas->first()){
            foreach($todasApostas as $aposta){
                $itens = $aposta->itens()->with('sorteados')->get();
                if($itens->first()){
                    foreach($itens as $item){
                        $valoresSorteados += $item->sorteados()->sum('valor');// Soma dos valores dos prêmios do cambista
                    }
                }
            }
        }
        $valorComissoes = $usuario->comissao_aposta()->where(function($query) use($dataInicio, $dataFim) {
            $query->whereDate('created_at', '>=', $dataInicio);
            $query->whereDate('created_at', '<=', $dataFim);
        })->sum('valor');
        $usuario['saidas'] = (float) $valorComissoes+$valoresSorteados;
        $usuario['premios'] = $valoresSorteados;

        $usuario['valorApostas'] = $entradas;
        $usuario['valorComissoes'] = $valorComissoes;

        $usuario['saldoAnterior'] = $saldo_anterior;
        // $usuario['saldo'] = ($saldo_anterior+
        return $usuario;
    }

    public function saldo_anterior($cambista, $dataInicio){
        $apostas = $cambista->apostas()->with('itens')->where(function($query) use($dataInicio){
            $query->where('status','!=','cancelado');
            $query->whereDate('created_at', '<', $dataInicio);
        });

        $movimentacoes = $cambista->movimentacoes();

        $creditos = $movimentacoes->where(function($query) use($dataInicio) {
            $query->where('tipo','credito');
            $query->whereDate('data', '<', $dataInicio);
        })->sum('valor');

        $retiradas = $movimentacoes->where(function($query) use($dataInicio){
            $query->where('tipo','retirada');
            $query->whereDate('data', '<', $dataInicio);
        })->sum('valor');

        $apostas = $cambista->apostas()->with('itens')->where(function($query) use($dataInicio){
            $query->where('status','!=','cancelado');
            $query->whereDate('created_at', '<', $dataInicio);
        });

        $entradas = (float) $apostas->sum('total');

        $valoresSorteados = 0;

        $todasApostas = $apostas->get();
        if($todasApostas->first()){
            foreach($todasApostas as $aposta){
                $itens = $aposta->itens()->with('sorteados')->get();
                if($itens->first()){
                    foreach($itens as $item){
                        $valoresSorteados += $item->sorteados()->sum('valor');// Soma dos valores dos prêmios do cambista
                    }
                }
            }
        }

        $valorComissoes = $cambista->comissao_aposta()->where(function($query) use($dataInicio) {
            $query->whereDate('created_at', '<', $dataInicio);
        })->sum('valor');
        $saidas = (float) $valorComissoes+$valoresSorteados+$retiradas;

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
