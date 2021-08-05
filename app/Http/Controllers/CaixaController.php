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
        $gerentes = User::with('movimentacoes')->where('perfil','gerente')->get();

        if($gerentes->first()){
            foreach($gerentes as $key => $gerente){
                $creditos = $gerente->movimentacoes()->where('tipo','credito')->sum('valor');
                $retiradas = $gerente->movimentacoes()->where('tipo','retirada')->sum('valor');
                $gerentes[$key]['creditos'] = $creditos;
                $gerentes[$key]['retiradas'] = $retiradas;
            }
        }

        return $gerentes;
    }

    public function caixa_supervisores(Request $request){
        $supervisores = User::where('perfil','supervisor')->get();
        if($supervisores->first()){
            foreach($supervisores as $key => $supervisor){
                $creditos = $supervisor->movimentacoes()->where('tipo','credito')->sum('valor');
                $retiradas = $supervisor->movimentacoes()->where('tipo','retirada')->sum('valor');
                $supervisores[$key]['creditos'] = $creditos;
                $supervisores[$key]['retiradas'] = $retiradas;
            }
        }
        return $supervisores;
    }

    public function caixa_cambistas(Request $request){
        $cambistas = User::where('perfil','cambista')->get();
        if($cambistas->first()){
            foreach($cambistas as $key => $cambista){
                $creditos = $cambista->movimentacoes()->where('tipo','credito')->sum('valor');
                $retiradas = $cambista->movimentacoes()->where('tipo','retirada')->sum('valor');
                $cambistas[$key]['creditos'] = $creditos;
                $cambistas[$key]['retiradas'] = $retiradas;
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
