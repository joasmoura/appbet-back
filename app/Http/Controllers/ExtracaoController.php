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

    private $grupos = [
        '01' => ['01', '02', '03' ,'04'],
        '02' => ['05', '06', '07' ,'08'],
        '03' => ['09', '10', '11' ,'12'],
        '04' => ['13', '14', '15' ,'16'],
        '05' => ['17', '18', '19' ,'20'],
        '06' => ['21', '22', '23' ,'24'],
        '07' => ['25', '26', '27' ,'28'],
        '08' => ['29', '30', '31' ,'32'],
        '09' => ['33', '34', '35' ,'36'],
        '10' => ['37', '38', '39' ,'40'],
        '11' => ['41', '42', '43' ,'44'],
        '12' => ['45', '46', '47' ,'48'],
        '13' => ['49', '50', '51' ,'52'],
        '14' => ['53', '54', '55' ,'56'],
        '15' => ['57', '58', '59' ,'60'],
        '16' => ['61', '62', '63' ,'64'],
        '17' => ['65', '66', '67' ,'68'],
        '18' => ['69', '70', '71' ,'72'],
        '19' => ['73', '74', '75' ,'76'],
        '20' => ['77', '78', '79' ,'80'],
        '21' => ['81', '82', '83' ,'84'],
        '22' => ['85', '86', '87' ,'88'],
        '23' => ['89', '90', '91' ,'92'],
        '24' => ['93', '94', '95' ,'96'],
        '25' => ['97', '98', '99' ,'00'],
    ];

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
            $query->where('codigo','like','%'.$codigo.'%');
            $query->whereDate('created_at', '>=',$de);
            $query->whereDate('created_at', '<=',$fim);
            $query->where('status','!=', 'cancelado');
        })->get();

        if($apostas->first()){
            foreach($apostas as $key => $aposta){
                $itens = $aposta->itens()->get();
                $aposta->data = date('d/m/Y',strtotime($aposta->created_at));

                if($aposta->horario){
                    $aposta->horario->data = date('d/m/Y',strtotime($aposta->horario->created_at));
                }
                if($itens->first()){
                    foreach($itens as $keyItem => $item){
                        $apostas[$key]['itens'][$keyItem]['sorteado'] = $item->sorteados;
                        $apostas[$key]['itens'][$keyItem]['numero'] = json_decode($item->numero,true);
                    }
                }
            }
        }

        return $apostas;
    }

    public function extracoes_cambista(){
        $user = auth()->user();
        $regioes = $user->regioes()->get();

        $extracao = Extracao::where(function($query){
            $data_atual = date('Y-m-d');
            $query->whereDate('data','>=',$data_atual)->get();
            $query->where('status',true)->get();
        })->get();

        $idsRegioes = [];

        if($extracao->first()){
            if($regioes->first()){
                foreach($regioes as $regiao){
                    array_push($idsRegioes, $regiao->id);
                }
            }
            foreach($extracao as $keye => $ext){
                $horas = $ext->horas()->with('regiao')->where(function($query) use($idsRegioes, $ext) {
                    $query->whereIn('regiao_id',$idsRegioes);
                    if(date('Y-m-d') === $ext->data){
                        $query->where('hora','>=',date('H:s'));
                    }
                })->get();
                $extracao[$keye]['data'] = date('d/m/Y',strtotime($ext->data));

                if($horas->first()){
                    foreach($horas as $key => $hora){
                        $reg = $hora->regiao;
                        if($reg){
                            $horas[$key]['mercado'] = $reg->mercado;
                        }
                    }
                }
                $extracao[$keye]['horas'] = $horas;
            }

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
            'data' => $request->data,
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
            $extracao->data = $extracao->data;
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
            $hora->premios;
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
            $apostas = $hora->apostas()->with('itens')->where('status','!=','cancelado')->get();
            $sorteados = [];

            $premios = [
                1 => $request->premio_1,
                2 => $request->premio_2,
                3 => $request->premio_3,
                4 => $request->premio_4,
                5 => $request->premio_5,
                6 => $request->premio_6,
                7 => $request->premio_7,
            ];

            if($apostas->first()){
                foreach($apostas as $aposta){
                    $itens = $aposta->itens;
                    $aposta_sorteada = false;

                    foreach($itens as $item){
                        $de = $item->premio_de;
                        $ate = $item->premio_ate;
                        $numero = json_decode($item->numero,true);


                        if((int) $item->modalidade == 1 || (int) $item->modalidade == 2 || (int) $item->modalidade == 3){
                            foreach($numero as $n){
                                for($i = $de; $i <= $ate; $i++){
                                    if($this->compara_numeros($n,$premios[$i])){
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
                        }elseif((int) $item->modalidade == 15){// Verificação para a modalidade passe seco
                            $comparacao = $this->compara_numeros_dois_premios($numero,$premios[1], $premios[2], $item);
                            if($comparacao){
                                $aposta_sorteada = true;
                                array_push($sorteados, $comparacao);
                            }
                        }elseif((int) $item->modalidade == 13){// Verificação para a modalidade passe combinado
                            $demais_premios = $premios;
                            unset($demais_premios[1]);

                            $comparacao = $this->compara_numero_um_eresto($numero,$premios[1], $demais_premios, $item);

                            if(!empty($comparacao)){
                                $aposta_sorteada = true;
                                foreach($comparacao as $c){
                                    array_push($sorteados, $c);
                                }
                            }
                        }elseif((int) $item->modalidade == 18){// Verificação para a modalidade queima
                            foreach($numero as $n){
                                for($i = $de; $i <= $ate; $i++){
                                    if($this->compara_numero_queima($n,$premios[$i])){
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
                        }elseif((int) $item->modalidade == 4){// Verificação para a modalidade Grupo
                            foreach($numero as $n){
                                for($i = $de; $i <= $ate; $i++){
                                    if($this->compara_numero_grupo($n,$premios[$i])){
                                        array_push($sorteados, [
                                            'item_aposta_id' => $item->id,
                                            'numero_premio' => (int) $i,
                                            'numero_sorteado' => (int) $n,
                                            'valor' => $item->poss_ganho
                                        ]);
                                    }
                                }
                            }
                        }elseif((int) $item->modalidade == 11){
                            $comparacao = $this->compara_numero_duque_dezena($numero,$premios, $item);
                            if(!empty($comparacao)){
                                foreach($comparacao as $c){
                                    array_push($sorteados, $c);
                                }
                            }
                        }elseif((int) $item->modalidade == 17){
                            $comparacao = $this->compara_numero_grupo_combinado($numero,$premios, $item);
                            if($comparacao){
                                foreach($comparacao as $c){
                                    array_push($sorteados, $c);
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

    public function compara_numeros($numero, $premio){//verifica para as modalidade Milhar, centena e dezena
        $split_numero = str_split($numero);
        $split_premio = str_split($premio);
        $novo_premio = [];
        $novo_numero = [];

        krsort($split_numero);
        krsort($split_premio);
        $combinacao = '';

        foreach($split_premio as $sp){
            array_push($novo_premio,$sp);
        }

        foreach($split_numero as $sp){
            array_push($novo_numero,$sp);
        }

        foreach($novo_numero as $key => $num){
            if(isset($novo_premio[$key])){
                if($num === $novo_premio[$key]){
                    $combinacao = (string) $num.$combinacao;
                }else{
                    break;
                }
            }
        }
        return $combinacao == $numero;
    }

    public function compara_numero_duque_dezena($numero, $premios, $item){
        $item_grupos = [];
        foreach($this->grupos as $grupo){
            foreach($grupo as $g){
                array_push($item_grupos, $g);
            }
        }

        $encontrados = [];
        $sorteado = [];
        $numeros_sorteados = [];
        foreach($numero as $n){
            for($i = $item->premio_de; $i <= $item->premio_ate; $i++){
                if(isset($premios[$i])){
                    $sub = substr($premios[$i], -2);
                    if($n == $sub && array_search($n, $item_grupos)){
                        if(count($numeros_sorteados) > 0){
                            if(!in_array($n, $numeros_sorteados)){
                                array_push($numeros_sorteados,$n);
                                array_push($encontrados, ['premio' => $i, 'sorte' => $n]);
                            }
                        }else{
                            array_push($numeros_sorteados,$n);
                            array_push($encontrados, ['premio' => $i, 'sorte' => $n]);
                        }

                        if(count($encontrados) == 2){// Caso forem sorteados as duas dezenas encerra as verificações e retorna com os dados
                            array_push($sorteado,[
                                'item_aposta_id' => $item->id,
                                'numero_premio' => $encontrados[0]['premio'] . '/'. $encontrados[1]['premio'],
                                'numero_sorteado' => $encontrados[0]['sorte'] . ' '. $encontrados[1]['sorte'],
                                'valor' => $item->poss_ganho
                            ]);
                            break;
                        }
                    }
                }
            }
        }
        return $sorteado;
    }

    public function compara_numero_grupo_combinado($numero, $premios, $item){
        $encontrados = [];
        $sorteados = [];
        $premios_passados = [];

        foreach($numero as $n){
            for($i = $item->premio_de; $i <= $item->premio_ate; $i++){
                if(isset($premios[$i])){
                    if(isset($this->grupos[$n])){
                        $gr = $this->grupos[$n];
                        $premio1 = substr($premios[$i], 0,2);
                        $premio2 = substr($premios[$i], 2);

                        if(count($encontrados) == 2){
                            array_push($sorteados, [
                                'item_aposta_id' => $item->id,
                                'numero_premio' => $encontrados[0]['premio'] . '/'. $encontrados[1]['premio'],
                                'numero_sorteado' => $encontrados[0]['sorte'] . ' '. $encontrados[1]['sorte'],
                                'valor' => $item->poss_ganho
                            ]);
                            break;
                        }

                        if(!in_array($i, $premios_passados)){
                            if(in_array($premio1,$gr) || in_array($premio2,$gr)){
                                if(!in_array($n, $encontrados)){
                                    array_push($premios_passados, $i);
                                    array_push($encontrados, ['premio' => $i, 'sorte' => $n]);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $sorteados;
    }

    public function compara_numero_grupo($numero, $premio){
        if(isset($this->grupos[$numero])){
            $grupo = $this->grupos[$numero];
            $sub = substr($premio, -2);
            return array_search($sub,$grupo);
        }
    }

    public function compara_numero_queima($numero, $premio){//verifica para a modalidade Queima
        if(isset($this->grupos[$numero])){
            $grupo = $this->grupos[$numero];
            $sub = substr($premio, 0, 2);
            return array_search($sub,$grupo);
        }
        return false;
    }

    public function compara_numeros_dois_premios($numero, $premio1, $premio2, $item){// Verifica passe seco
        $sorteados = [];

        $item_aposta_id = '';
        $numero_premio = '1/2';
        $numero_sorteado = '';
        $valor = '';

        foreach($numero as $n){
            if($this->compara_numeros($n,$premio1)){
                $item_aposta_id = $item->id;
                $numero_sorteado = (int) $n;
                $valor = $item->poss_ganho;
                array_push($sorteados, $n);
            }

            if($this->compara_numeros($n,$premio2)){
                array_push($sorteados, $n);
            }
        }

        if(count($sorteados) == 2){
            return [
                'item_aposta_id' => $item_aposta_id,
                'numero_premio' => $numero_premio,
                'numero_sorteado' => $numero_sorteado,
                'valor' => $valor
            ];
        }else{
            return false;
        }
    }

    public function compara_numero_um_eresto($numero, $premio1, $premios, $item){
        $sorteados = [];
        $ate = $item->premio_ate;

        foreach($numero as $n){
            if($this->compara_numeros($n,$premio1)){
                $sorteados[] = [
                    'item_aposta_id' => $item->id,
                    'numero_premio' => 1,
                    'numero_sorteado' => $n,
                    'valor' => $item->poss_ganho
                ];
            }
        }

        if(!empty($sorteados)){
            foreach($numero as $n){
                for($i = 2; $i <= $ate; $i++){
                    if(isset($premios[$i])){
                        if($this->compara_numeros($n,$premios[$i])){
                            $sorteados[] = [
                                'item_aposta_id' => $item->id,
                                'numero_premio' => $i,
                                'numero_sorteado' => $n,
                                'valor' => $item->poss_ganho
                            ];
                        }
                    }
                }
            }
        }

        return $sorteados;
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
