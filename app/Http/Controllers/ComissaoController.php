<?php

namespace App\Http\Controllers;

use App\Models\Comissao;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComissaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comissoes = Comissao::paginate(10);
        return $comissoes;
    }

    public function select()
    {
        $comissoes = Comissao::get();
        return $comissoes;
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
        $salvo = Comissao::create([
            'nome' => $request->nome,
            'regiao_id' => $request->regiao,
            'valor' => $request->valor
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
        $comissao = Comissao::find($id);

        if($comissao){
            return response()->json([
                'status' => true,
                'comissao' => $comissao
            ],Response::HTTP_OK);
        }else{
            return response()->json([
                'status' => false,
            ],Response::HTTP_OK);
        }
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
        $comissao = Comissao::find($id);

        if($comissao){
            $comissao->nome = $request->nome;
            $comissao->regiao_id = $request->regiao;
            $comissao->valor = $request->valor;

            $salvo = $comissao->save();

            if($salvo){
                return response()->json([
                    'status' => true,
                    'comissao' => $comissao
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
