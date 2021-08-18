<?php

namespace App\Http\Controllers;

use App\Models\Movimentacao;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MovimentacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $movimentacoes = Movimentacao::with('usuario')->orderBy('created_at','desc')->paginate(10);

        if($movimentacoes->first()){
            foreach($movimentacoes as $key => $movimentacao){
                $movimentacoes[$key]['data'] = date('d/m/Y',strtotime($movimentacao->data));
                $movimentacoes[$key]['data_criacao'] = date('d/m/Y',strtotime($movimentacao->created_at));
            }
        }
        return $movimentacoes;
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
        $salvo = Movimentacao::create([
            'user_id' => $request->usuario,
            'descricao' => $request->descricao,
            'valor' => $request->valor,
            'tipo' => $request->tipo,
            'data' => dataParaBanco($request->data)
        ]);

        if($salvo){
            return response()->json([
                'status' => true,
            ],Response::HTTP_OK);
        }else{
            return response()->json([
                'status' => false,
            ],Response::HTTP_OK);
        }
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
        $movimentacao = Movimentacao::with('usuario')->find($id);

        if($movimentacao){
            $movimentacao['data'] = date('d/m/Y',strtotime($movimentacao->data));
            $movimentacao['data_criacao'] = date('d/m/Y',strtotime($movimentacao->created_at));
        }
        return $movimentacao;
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
        $movimentacao = Movimentacao::find($id);

        if($movimentacao){
            $movimentacao->user_id = $request->cambista;
            $movimentacao->descricao = $request->descricao;
            $movimentacao->valor = $request->valor;
            $movimentacao->tipo = $request->tipo;
            $movimentacao->data = date('Y-m-d',strtotime($request->data));

            $salvo = $movimentacao->save();

            if($salvo){
                return response()->json([
                    'status' => true,
                ],Response::HTTP_OK);
            }else{
                return response()->json([
                    'status' => false,
                ],Response::HTTP_OK);
            }
        }

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
