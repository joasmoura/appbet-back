<?php

namespace App\Http\Controllers;

use App\Models\Extracao;
use App\Models\Horarios_Extracao;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExtracaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $extracoes = Extracao::with('horas')->get();
        if($extracoes->first()){
            foreach($extracoes as $key => $extracao){
                $extracoes[$key]['data'] = (!empty($extracao->data) ? date('d/m/Y',strtotime($extracao->data)) : null);
            }
        }
        return $extracoes;
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
        $salvo = Extracao::create([
            'data' => dataParaBanco($request->data),
            'status' => true
        ]);

        if($salvo){
            if(isset($request->horarios)){
                foreach($request->horarios as $horario){
                    $salvo->horas()->create([
                        'nome' => $horario['nome'],
                        'hora' => $horario['hora'],
                        'regiao_id' => ($horario['regiao'] ? $horario['regiao']['value'] : null)
                    ]);
                }
            }

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
        $extracao = Extracao::with('horas')->find($id);
        if($extracao){
            $extracao->data = date('d/m/Y',strtotime($extracao->data));
            return response()->json([
                'status' => true,
                'extracao' => $extracao
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
        $extracao = Extracao::find($id);
        if($extracao){
            $extracao->data = dataParaBanco($request->data);
            $salvo = $extracao->save();

            if($salvo){
                if(isset($request->horarios)){
                    foreach($request->horarios as $horario){
                        if($horario['id'] != ''){
                            $hora = $extracao->horas()->find($horario['id']);
                            if($hora){
                                $hora->nome = $horario['nome'];
                                $hora->hora = $horario['hora'];
                                $hora->regiao_id = ($horario['regiao'] ? $horario['regiao']['value'] : null);
                                $hora->save();
                            }
                        }else{
                            $extracao->horas()->create([
                                'nome' => $horario['nome'],
                                'hora' => $horario['hora'],
                                'regiao_id' => ($horario['regiao'] ? $horario['regiao']['value'] : null)
                            ]);
                        }
                    }
                }

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

    public function removerHora($id){
        $hora = Horarios_Extracao::find($id);
        if($hora){
            $hora->delete();
        }
    }
}
