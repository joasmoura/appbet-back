<?php

namespace App\Http\Controllers;

use App\Models\Mercado;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MercadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
        $mercado = $request->mercado;
        $salvo = Mercado::create([
            'regiao_id' => $request->regiao,
            'grupo' => $mercado['grupo'],
            'dezena' => $mercado['dezena'],
            'centena' => $mercado['centena'],
            'milhar' => $mercado['milhar'],
            'duque_grupo' => $mercado['duque_grupo'],
            'terno_grupo' => $mercado['terno_grupo'],
            'terno_dezena' => $mercado['terno_dezena'],
            'milhar_centena' => $mercado['milhar_centena'],
            'milhar_invertida' => $mercado['milhar_invertida'],
            'mc_invertida' => $mercado['mc_invertida'],
            'centena_invertida' => $mercado['centena_invertida'],
            'duque_dezena' => $mercado['duque_dezena'],
            'passe_combinado' => $mercado['passe_combinado'],
            'terno_grupo_combinado' => $mercado['terno_grupo_combinado'],
            'passe_seco' => $mercado['passe_seco'],
            'terno_dezena_cercado' => $mercado['terno_dezena_cercado'],
            'grupo_combinado' => $mercado['grupo_combinado']
        ]);

        if ($salvo) {
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
        $mercado = Mercado::where('regiao_id', $id)->first();
        return $mercado;
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
        $dados = Mercado::where('regiao_id',$id)->first();

        if($dados){
            $mercado = $request->mercado;

            $dados->grupo = $mercado['grupo'];
            $dados->dezena = $mercado['dezena'];
            $dados->centena = $mercado['centena'];
            $dados->milhar = $mercado['milhar'];
            $dados->duque_grupo = $mercado['duque_grupo'];
            $dados->terno_grupo = $mercado['terno_grupo'];
            $dados->terno_dezena = $mercado['terno_dezena'];
            $dados->milhar_centena = $mercado['milhar_centena'];
            $dados->milhar_invertida = $mercado['milhar_invertida'];
            $dados->mc_invertida = $mercado['mc_invertida'];
            $dados->centena_invertida = $mercado['centena_invertida'];
            $dados->duque_dezena = $mercado['duque_dezena'];
            $dados->passe_combinado = $mercado['passe_combinado'];
            $dados->terno_grupo_combinado = $mercado['terno_grupo_combinado'];
            $dados->passe_seco = $mercado['passe_seco'];
            $dados->terno_dezena_cercado = $mercado['terno_dezena_cercado'];
            $dados->grupo_combinado = $mercado['grupo_combinado'];

            $salvo = $dados->save();

            if ($salvo) {
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
