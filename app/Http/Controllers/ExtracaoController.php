<?php

namespace App\Http\Controllers;

use App\Models\Aposta;
use App\Models\Extracao;
use App\Models\Horarios_Extracao;
use App\Models\Sorteados;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExtracaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $extracoes = Extracao::with('horas')->orderBy('created_at','desc')->paginate(10);
        if($extracoes->first()){
            foreach($extracoes as $key => $extracao){
                $extracoes[$key]['data'] = (!empty($extracao->data) ? date('d/m/Y',strtotime($extracao->data)) : null);
                $extracoes[$key]['status'] = ($extracoes[$key]['status'] == 0 ? null : 1);
            }
        }
        return $extracoes;
    }

    public function bilhetes(Request $request){
        $dados = $request->all();
        $dados = $dados['dados'];

        $codigo = $dados['codigo'];
        $de = date('Y-m-d',strtotime($dados['dataInicio']));
        $fim = date('Y-m-d',strtotime($dados['dataFim']));

        $usuario = auth()->user();

        $apostas = $usuario->apostas()->with('itens','horario','cambista')->where(function($query) use($codigo, $de, $fim) {
            $query->where('status','!=', 'cancelado');
            $query->whereDate('created_at', '>=',$de);
            $query->whereDate('created_at', '<=',$fim);
            $query->where('codigo','like','%'.$codigo.'%');
        })->get();

        if($apostas->first()){
            foreach($apostas as $key => $aposta){
                $itens = $aposta->itens()->get();
                if($itens->first()){
                    foreach($itens as $keyItem => $item){
                        $apostas[$key]['itens'][$keyItem]['sorteado'] = $item->sorteados;
                    }
                }
            }
        }

        return $apostas;
    }

    public function extracoes_cambista(){
        $user = auth()->user();
        $regioes = $user->regioes()->with('horarios')->get();

        $extracao = Extracao::where(function($query){
            $data_atual = date('Y-m-d');
            $query->whereDate('data',$data_atual)->get();
            $query->where('status',true)->get();
        })->first();
        $idsRegioes = [];

        if($extracao){
            if($regioes->first()){
                foreach($regioes as $regiao){
                    array_push($idsRegioes, $regiao->id);
                }
            }
            $horas = $extracao->horas()->with('regiao')->whereIn('regiao_id',$idsRegioes)->get();

            if($horas->first()){
                foreach($horas as $key => $hora){
                    $reg = $hora->regiao;
                    if($reg){
                        $horas[$key]['mercado'] = $reg->mercado;
                    }
                }
            }
            $extracao->horas = $horas;

            return $extracao;
        }
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

    public function hora($id)
    {
        $hora = Horarios_Extracao::with('extracao','premios')->find($id);
        if($hora){
            $hora->extracao->data = date('d/m/Y',strtotime($hora->extracao->data));
            return response()->json([
                'status' => true,
                'hora' => $hora
            ],Response::HTTP_OK);
        }else{
            return response()->json([
                'status' => false,
            ],Response::HTTP_OK);
        }
    }

    public function salvarPremios($id, Request $request){
        $hora = Horarios_Extracao::find($id);

        if($hora){
            $apostas = $hora->apostas()->with('itens')->get();
            $sorteados = [];
            $sorteios_iguais = [];

            if($apostas->first()){
                foreach($apostas as $aposta){
                    $itens = $aposta->itens;
                    $aposta_sorteada = false;

                    foreach($itens as $item){
                        $de = $item->premio_de;
                        $ate = $item->premio_ate;
                        $numero = json_decode($item->numero,true);

                        foreach($numero as $n){
                            for($i = $de; $i <= $ate; $i++){
                                if($i == 1 && $n == $request->premio_1){
                                    $aposta_sorteada = true;
                                    array_push($sorteados, [
                                        'item_aposta_id' => $item->id,
                                        'numero_premio' => (int) $i,
                                        'numero_sorteado' => (int) $n,
                                        'valor' => $item->poss_ganho
                                    ]);
                                } elseif($i == 2 && $n == $request->premio_2){
                                    $aposta_sorteada = true;
                                    array_push($sorteados, [
                                        'item_aposta_id' => $item->id,
                                        'numero_premio' => (int) $i,
                                        'numero_sorteado' => (int) $n,
                                        'valor' => $item->poss_ganho
                                    ]);
                                } elseif($i == 3 && $n == $request->premio_3){
                                    $aposta_sorteada = true;
                                    array_push($sorteados, [
                                        'item_aposta_id' => $item->id,
                                        'numero_premio' => (int) $i,
                                        'numero_sorteado' => (int) $n,
                                        'valor' => $item->poss_ganho
                                    ]);
                                } elseif($i == 4 && $n == $request->premio_4){
                                    $aposta_sorteada = true;
                                    array_push($sorteados, [
                                        'item_aposta_id' => $item->id,
                                        'numero_premio' => (int) $i,
                                        'numero_sorteado' => (int) $n,
                                        'valor' => $item->poss_ganho
                                    ]);
                                } elseif($i == 5 && $n == $request->premio_5){
                                    $aposta_sorteada = true;
                                    array_push($sorteados, [
                                        'item_aposta_id' => $item->id,
                                        'numero_premio' => (int) $i,
                                        'numero_sorteado' => (int) $n,
                                        'valor' => $item->poss_ganho
                                    ]);
                                } elseif($i == 6 && $n == $request->premio_6){
                                    $aposta_sorteada = true;
                                    array_push($sorteados, [
                                        'item_aposta_id' => $item->id,
                                        'numero_premio' => (int) $i,
                                        'numero_sorteado' => (int) $n,
                                        'valor' => $item->poss_ganho
                                    ]);
                                } elseif($i == 7 && $n == $request->premio_7){
                                    $aposta_sorteada = true;
                                    array_push($sorteados, [
                                        'item_aposta_id' => $item->id,
                                        'numero_premio' => (int) $i,
                                        'numero_sorteado' => (int) $n,
                                        'valor' => $item->poss_ganho
                                    ]);
                                }
                            }
                        }
                    }

                    if($aposta_sorteada){
                        $aposta->status = 'ganhou';
                    }else{
                        $aposta->status = 'perdeu';
                    }

                    $aposta->save();
                }
            }

            $premio = $hora->premios()->first();
            if($premio){
                $premio->premio_1 = $request->premio_1;
                $premio->premio_2 = $request->premio_2;
                $premio->premio_3 = $request->premio_3;
                $premio->premio_4 = $request->premio_4;
                $premio->premio_5 = $request->premio_5;
                $premio->premio_6 = $request->premio_6;
                $premio->premio_7 = $request->premio_7;

                $salvo = $premio->save();

                if($salvo){
                    if(!empty($sorteados)){
                        $premio->sorteados()->delete();
                        foreach($sorteados as $sorteado){
                            $premio->sorteados()->create($sorteado);
                        }
                    }
                }
            }else{
                $salvo = $hora->premios()->create([
                    'premio_1' => $request->premio_1,
                    'premio_2' => $request->premio_2,
                    'premio_3' => $request->premio_3,
                    'premio_4' => $request->premio_4,
                    'premio_5' => $request->premio_5,
                    'premio_6' => $request->premio_6,
                    'premio_7' => $request->premio_7,
                ]);

                if($salvo){
                    if(!empty($sorteados)){
                        foreach($sorteados as $sorteado){
                            $salvo->sorteados()->create($sorteado);
                        }
                    }
                }
            }

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

    public function verifica_ganhadores(){

    }

    public function setarStatus($id){
        $extracao = Extracao::find($id);
        if($extracao){
            $extracao->status = ($extracao->status ? 0 : 1);
            $salvo = $extracao->save();

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

    public function consultarResultado(Request $request){
        $data = $request->data;

        if(!empty($data)){
            $usuario = auth()->user();
            $data = date('Y-m-d',strtotime($data));
            $regioes = $usuario->regioes()->with('horarios')->get();

            $extracao = Extracao::where(function($query) use($data) {
                $query->whereDate('data',$data)->get();
                $query->where('status',true)->get();
            })->first();
            $idsRegioes = [];

            if($extracao){
                if($regioes->first()){
                    foreach($regioes as $regiao){
                        array_push($idsRegioes, $regiao->id);
                    }
                }
                $horas = $extracao->horas()->with('premios')->whereIn('regiao_id',$idsRegioes)->get();
                $extracao->horas = $horas;
            }

            return $extracao;
        }
    }
}
