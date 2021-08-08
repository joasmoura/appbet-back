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
        $gerentes = User::with('movimentacoes','cambistas_gerente')->where('perfil','gerente')->paginate(10);

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
        $cambistas = User::where('perfil','cambista','apostas')->paginate(10);
        if($cambistas->first()){
            $entradas = 0;
            foreach($cambistas as $key => $cambista){
                $creditos = $cambista->movimentacoes()->where('tipo','credito')->sum('valor');
                $retiradas = $cambista->movimentacoes()->where('tipo','retirada')->sum('valor');

                $entradas = (float) $cambista->apostas()->sum('total');

                $cambistas[$key]['creditos'] = $creditos;
                $cambistas[$key]['retiradas'] = $retiradas;
                $cambistas[$key]['entradas'] = $entradas;
            }
        }
        return $cambistas;
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
