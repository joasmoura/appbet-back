<?php

namespace App\Http\Controllers;

use App\Models\Aposta;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApostaController extends Controller
{

    public function gerar_codigo()
    {
        $usuario = auth()->user();
        $apostas = $usuario->apostas()->count();

        $alphabet = '1234567890'.(string) $apostas;
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $serial = implode($pass); //turn the array into a string

        return $serial;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $apostas = Aposta::with('itens','cambista')->orderBy('created_at','desc')->paginate(1);

        if($apostas->first()){
            foreach($apostas as $key => $aposta){
                $apostas[$key]['horario'] = $aposta->horario;
                $extracao = $aposta->horario->extracao;

                if($extracao){
                    $extracao->data = date('d/m/Y',strtotime($extracao->data));
                }

                $apostas[$key]['extracao'] = $extracao;
                $apostas[$key]['hora'] = date('d/m/Y H:i',strtotime($aposta->created_at));
            }
        }
        return $apostas;
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
        $dados = $request->all();
        $dados = $dados['dados'];

        $usuario = auth()->user();
        $comissao = $usuario->comissao;
        $salvo = $usuario->apostas()->create([
            'horario_id' => $dados['id'],
            'codigo' => $this->gerar_codigo(),
            'total' => (float) $dados['valorTotal'],
            'status' => 'aberto',
            'tel_apostador' => $dados['telefone']
        ]);

        if($salvo){
            if($dados['itens']){
                foreach($dados['itens'] as $item){
                    $salvo->itens()->create([
                        'valor' => (float) $item['valor'],
                        'modalidade' => (int) $item['modalidade']['id'],
                        'subtotal' => (float) $item['subtotal'],
                        'numero' => json_encode($item['numero']),
                        'poss_ganho' => (float) $item['estimativa'],
                        'premio_de' => (int) $item['premios']['de'],
                        'premio_ate' => (int) $item['premios']['ate'],
                    ]);

                    if($comissao){
                        $valor = ((float) $item['valor'] * (float) $comissao->valor)/100;
                        $salvo->comissao_aposta()->create([
                            'valor' => $valor
                        ]);
                    }

                    $usuario->limite_credito -= $item['valor'];
                    $usuario->save();

                    $gerente = $usuario->gerente;
                    if($gerente){
                        $valorFaturamento = ((float) $item['valor'] * (float) $gerente->comissao_faturamento)/100;
                        $salvo->comissao_gerente()->create([
                            'valor' => $valorFaturamento,
                        ]);
                    }
                }
            }

            $salvo->data = date('d/m/Y H:i',strtotime($salvo->created_at));
            $salvo->cambista = $salvo->cambista;
            $salvo->horario = $salvo->horario;
            $salvo->horario->data = date('d/m/Y',strtotime($salvo->horario->created_at));

            return response()->json([
                'status' => true,
                'confirmada' => $salvo
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

    public function cancelar_aposta($id){
        $aposta = Aposta::find($id);
        if($aposta){
            $aposta->status = 'cancelado';

            $salvo = $aposta->save();
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
}
