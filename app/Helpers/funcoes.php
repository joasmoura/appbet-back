<?php

// function financaReceirasLacamentoAberto(){
//     $entrada = \App\Models\Financa::whereNull('fin_fechamento')->first();
//     if($entrada):
//         return url('painel/financas/receitas').'/'.$entrada->fin_mes.'/'.$entrada->fin_ano;
//     else:
//         return route('financas.index');
//     endif;
// }

// function financaDespesasLacamentoAberto(){
//     $saida = \App\Models\Financa::whereNull('fin_fechamento')->first();
//     if($saida):
//         return url('painel/financas/despesas').'/'.$saida->fin_mes.'/'.$saida->fin_ano;
//     else:
//             return route('financas.index');
//     endif;
// }

function dataParaBanco($data){
    return implode('-', array_reverse(explode('/', $data)));
}

function valorReal($valor){
    return number_format($valor,2,',','.');
}

function inteiroParaReal($valor){
    return number_format($valor,2,',','.');
}

function valorPorcentagem($valor){
    return number_format($valor,2);
}

function valorBanco($valor){
    $valor = str_replace([',','.'], ['.',''], $valor);
    return number_format($valor/100,2,'.','');
}

function mes_extenso($mes){
    $meses = array(
        'Jan' => 'Janeiro',
        'Feb' => 'Fevereiro',
        'Mar' => 'Março',
        'Apr' => 'Abril',
        'May' => 'Maio',
        'Jun' => 'Junho',
        'Jul' => 'Julho',
        'Aug' => 'Agosto',
        'Nov' => 'Novembro',
        'Sep' => 'Setembro',
        'Oct' => 'Outubro',
        'Dec' => 'Dezembro'
    );

    return $meses[$mes];
}

function mes_numero_nome($mes){
    $meses = array(
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Março',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Novembro',
        '10' => 'Setembro',
        '11' => 'Outubro',
        '12' => 'Dezembro'
    );

    return $meses[$mes];
}

 function verificarPalavrasChave($string,$discipulo = NULL,$adicionais = NULL) {

        $igreja = auth()->user()->empresa;
        $congregacao = App\Models\Congregacao::get();
        $pais = Illuminate\Support\Facades\DB::connection('mysql')->table('pais');
        $estado = Illuminate\Support\Facades\DB::connection('mysql')->table('estado')->get();
        $cidade = Illuminate\Support\Facades\DB::connection('mysql')->table('cidade')->get();
        //$discipulo = App\Models\Discipulo::find($discipulo);
//        dd($string);
        $replace = [
            '[igreja_nome]','[igreja_cidade]','[igreja_endereco]','[igreja_numero]','[igreja_bairro]','[igreja_uf]',
            '[igreja_codtelefone]','[igreja_telefone]','[igreja_codcelular]','[igreja_celular]','[igreja_cnpj]','[igreja_logo]',
            '[igreja_cep]','[igreja_complemento]','[igreja_pais]',

            '[data_atual_nome]','[data_atual_numero]',

            '[discipulo_codigo]','[discipulo_nome]','[discipulo_nascimento]','[pais_nascimento]','[estado_nascimento]',
            '[cidade_nascimento]','[nome_mae]','[nome_pai]','[cpf]','[rg]','[civil]','[nacionalidade]',
            '[congregacao]','[data_batismo]','[pais_batismo]','[estado_batismo]',
            '[cidade_batismo]','[local_batismo]','[situacao_congregacional]','[pais_endereco]',
            '[estado_endereco]','[cidade_endereco]','[endereco]','[cep]','[bairro]','[obs]','[foto]',

            '[complementar_nome]','[igreja_destino]','[cidade_destino]','[funcoes]',
        ];

        $sub = [
            $igreja->igr_nome,$igreja->igr_cidade,$igreja->igr_endereco,$igreja->igr_numero,$igreja->igr_bairro,$igreja->igr_uf,
            $igreja->igr_codtelefone,$igreja->igr_telefone,$igreja->igr_codcelular,$igreja->igr_celular,$igreja->igr_cnpj, Illuminate\Support\Facades\Storage::url('companies'.DIRECTORY_SEPARATOR.$igreja->uuid.DIRECTORY_SEPARATOR.$igreja->igr_logo),
            $igreja->igr_cep,$igreja->igr_complemento,$igreja->igr_pais,

            date('d').' de '.mes_extenso(date('M')).' de '.date('Y'),date('d/m/Y'),

            (isset($discipulo) && !empty($discipulo->id) ? $discipulo->id : ''),
            (isset($discipulo) && !empty($discipulo->disc_nome) ? $discipulo->disc_nome : ''),
            (isset($discipulo) && !empty($discipulo->disc_nascimento) ? date('d/m/Y',strtotime($discipulo->disc_nascimento)) : ''),
            (isset($discipulo) && !empty($discipulo->disc_paisNaturalidade) ? $pais->where('id', $discipulo->disc_paisNaturalidade)->first()->ps_nome_pt : ''),
            (isset($discipulo) && !empty($discipulo->disc_estadoNaturalidade) ? $estado->where('id',$discipulo->disc_estadoNaturalidade)->first()->uf_uf : ''),
            (isset($discipulo) && !empty($discipulo->disc_naturalidade) ? $cidade->where('id',$discipulo->disc_naturalidade)->first()->cd_nome : ''),
            (isset($discipulo) && !empty($discipulo->disc_mae) ? $discipulo->disc_mae : ''),
            (isset($discipulo) && !empty($discipulo->disc_pai) ? $discipulo->disc_pai : ''),
            (isset($discipulo) && !empty($discipulo->disc_cpf) ? $discipulo->disc_cpf : ''),
            (isset($discipulo) && !empty($discipulo->disc_rg) ? $discipulo->disc_rg : ''),
            (isset($discipulo) && !empty($discipulo->disc_civil) ?
                    ($discipulo->disc_civil == 'S' ? 'Solteiro(a)' :
                    ($discipulo->disc_civil == 'C' ? 'Casado(a)' :
                    ($discipulo->disc_civil == 'D' ? 'Divorciado(a)' :
                    ($discipulo->disc_civil == 'V' ? 'Viúvo(a)' : ''))) ) : ''),
            (isset($discipulo) && !empty($discipulo->disc_nacionalidade) ? $discipulo->disc_nacionalidade : ''),
            (isset($discipulo) && !empty($discipulo->disc_congregacao_id) ? $congregacao->where('id',$discipulo->disc_congregacao_id)->first()->con_nome : ''),
            (isset($discipulo) && !empty($discipulo->disc_batismo) ? date('d/m/Y',strtotime($discipulo->disc_batismo)) : ''),
            (isset($discipulo) && !empty($discipulo->disc_paisBatismo) ? $pais->where('id', $discipulo->disc_paisBatismo)->first()->ps_nome_pt : ''),
            (isset($discipulo) && !empty($discipulo->disc_estadoBatismo) ? $estado->where('id',$discipulo->disc_estadoBatismo)->first()->uf_uf : ''),
            (isset($discipulo) && !empty($discipulo->disc_cidadeBatismo) ? $cidade->where('id',$discipulo->disc_cidadeBatismo)->first()->cd_nome : ''),
            (isset($discipulo) && !empty($discipulo->disc_localBatismo) ? $discipulo->disc_localBatismo : ''),
            (isset($discipulo) && !empty($discipulo->disc_situacaoCongregacional) ?
                    ($discipulo->disc_situacaoCongregacional == 'NC' ? 'Novo Convertido' :
                    ($discipulo->disc_situacaoCongregacional == 'C' ? 'Congregado' :
                    ($discipulo->disc_situacaoCongregacional == 'M' ? 'Membro' : ''))) : ''),
            (isset($discipulo) && !empty($discipulo->disc_paisEndereco) ?
                    $pais->where('id',$discipulo->disc_paisEndereco)->first()->ps_nome_pt : ''),
            (isset($discipulo) && !empty($discipulo->disc_estadoEndereco) ? $estado->where('id',$discipulo->disc_estadoEndereco)->first()->uf_uf : ''),
            (isset($discipulo) && !empty($discipulo->disc_cidadeEsdereco) ? $cidade->where('id',$discipulo->disc_cidadeEndereco)->first()->cd_nome : ''),
            (isset($discipulo) && !empty($discipulo->disc_endereco) ? $discipulo->disc_endereco : ''),
            (isset($discipulo) && !empty($discipulo->disc_cep) ? $discipulo->disc_cep : ''),
            (isset($discipulo) && !empty($discipulo->disc_bairro) ? $discipulo->disc_bairro : ''),
            (isset($discipulo) && !empty($discipulo->disc_obs) ? $discipulo->disc_obs : ''),
            (isset($discipulo) && !empty($discipulo->disc_foto) ? $discipulo->disc_foto : ''),

            (isset($adicionais) && !empty($adicionais['complementar_nome']) ? $adicionais['complementar_nome'] : ''),
            (isset($adicionais) && !empty($adicionais['igreja_destino']) ? $adicionais['igreja_destino'] : ''),
            (isset($adicionais) && !empty($adicionais['cidade_destino']) ? $adicionais['cidade_destino'] : ''),
            (isset($adicionais) && !empty($adicionais['funcoes']) ? $adicionais['funcoes'] : ''),
        ];
//        dd(str_replace($replace,$sub,$string));

        return str_replace($replace,$sub,$string);
    }

// function RelatorioGeral_modelo1($igreja,$financas,$sede,$congregacoes) {
//     $conteudo = '';
//     if($financas->first()):
//         $entradas = \App\Models\Fin_entradas::where('financa_id',$financas->first()->id)->get();
//         $saldoAnterior = $entradas->where('ent_tipo','saldo_anterior')->first();

//         $obreiro = [];
//         $obreiros = (\App\Models\Obreiro::where('status','em_atividade')->first() ? \App\Models\Obreiro::select('discipulo_id')->where('status','em_atividade')->get(): '');
//         if($obreiros):
//             foreach ($obreiros as $ob):
//                 array_push($obreiro, $ob->discipulo_id);
//             endforeach;
//         endif;

//         //Verifica existencia de obreiros


//         $valor_dizimoObreiros = ($entradas->where('ent_tipo','diz_disc')->first()
//                 ? \App\Models\Fin_dizimoDiscipulos::select('valor')->where('entrada_id',$entradas->where('ent_tipo','diz_disc')->first()->id)->whereIn('discipulo_id',$obreiro)->get()->sum('valor') : 0);

//         $valor_ofertasExtras = ($entradas->where('ent_tipo','oferta_disc')->first() ?
//                 \App\Models\Fin_ofertaDiscipulos::select('valor')->where('entrada_id',$entradas->where('ent_tipo','oferta_disc')->first()->id)->get()->sum('valor') : 0);

//         if($entradas->first()):
//             $totalEntradas = entradasDizimosSede($sede, $entradas, $igreja,$obreiro)+entradasOfertasSede($sede, $entradas, $igreja,$obreiro)+entradasCongregacoes($sede, $entradas, $igreja, $obreiro)+$valor_ofertasExtras+$valor_dizimoObreiros+entradasDonativos($entradas,$igreja);
//             $titulo = '<h4 style="text-align:center;">RELATÓRIO FINACEIRO DA SEDE E CONGREGAÇÕES</h4>';

//             $listaEntradas = '<div class="box-entradas">'
//                     . '<h4>ENTRADAS DA SEDE</h4>'
//                     . '<table class="table" border="1">'
//                         .'<tr>'
//                             .'<td class="texto_tabela">DIZIMOS DA SEDE</td>'
//                             .'<td align="right">R$ '.valorReal(entradasDizimosSede($sede, $entradas, $igreja,$obreiro)).'</td>'
//                         .'</tr>'

//                         .'<tr>'
//                             .'<td class="texto_tabela">OFERTAS DA SEDE</td>'
//                             .'<td align="right">R$ '.valorReal(entradasOfertasSede($sede, $entradas, $igreja,$obreiro)).'</td>'
//                         .'</tr>'

//                         .'<tr>'
//                             .'<td class="texto_tabela">OFERTAS EXTRAS</td>'
//                             .'<td align="right">R$ '.valorReal($valor_ofertasExtras).'</td>'
//                         .'</tr>'

//                         .'<tr>'
//                             .'<td class="texto_tabela">DÍZIMO DOS OBREIROS</td>'
//                             .'<td align="right">R$ '.valorReal($valor_dizimoObreiros).'</td>'
//                         .'</tr>'

//                         .'<tr>'
//                             .'<td class="texto_tabela">OFERTAS E DIZÍMO DAS CONGREGAÇÕES</td>'
//                             .'<td align="right">R$ '.valorReal(entradasCongregacoes($sede, $entradas, $igreja, $obreiro)).'</td>'
//                         .'</tr>'

//                         .'<tr>'
//                             .'<td class="texto_tabela">DONATIVOS</td>'
//                             .'<td align="right">R$ '.valorReal(entradasDonativos($entradas,$igreja)).'</td>'
//                         .'</tr>'
//                     . '</table>'


//              . '<h4>ENTRADAS DAS CONGREGAÇÕES</h4>'
//                     . '<table class="table" border="1" >';

//                         $listaCongregacoes = '';
//                         if($congregacoes->first()):
//                             foreach($congregacoes as $con):
//                                 $listaCongregacoes .= '<tr >'
//                                     . '<td class="texto_tabela">'.$con->con_nome.'</td>'
//                                     . '<td align="right">R$ ';

//                                         $discCongregacao = [];
//                                         $discipulosCongregacao = App\Models\Discipulo::select('id')->where('disc_congregacao_id',$con->id)->get();
//                                         if($discipulosCongregacao):
//                                             foreach ($discipulosCongregacao as $disc):
//                                                 array_push($discCongregacao,$disc->id);
//                                             endforeach;
//                                         endif;

//                                         $listaCongregacoes .= valorReal(($entradas->where('ent_tipo','oferta_diz')->first()
//                                             ? App\Models\Fin_ofertaDizimoCongregacao::select('valor')->where('entrada_id',$entradas->where('ent_tipo','oferta_diz')->first()->id)->where('congregacao_id',$con->id)->get()->sum('valor') : 0)

//                                         + ($entradas->where('ent_tipo','oferta_con')->first()
//                                             ? \App\Models\Fin_ofertaCongregacoes::select('valor')->where('entrada_id',$entradas->where('ent_tipo','oferta_con')->first()->id)->where('congregacao_id',$con->id)->get()->sum('valor') : 0)

//                                         +($entradas->where('ent_tipo','diz_con')->first()
//                                             ? \App\Models\Fin_dizimoCongregacoes::select('valor')->where('entrada_id',$entradas->where('ent_tipo','diz_con')->first()->id)->where('congregacao_id',$con->id)->get()->sum('valor') : 0)


//                                         +($entradas->where('ent_tipo','diz_disc')->first() ?
//                                                 \App\Models\Fin_dizimoDiscipulos::select('valor')->where('entrada_id',$entradas->where('ent_tipo','diz_disc')->first()->id)->whereIn('discipulo_id',$discCongregacao)->whereNotIn('discipulo_id',$obreiro)->get()->sum('valor') : 0)

//                                         +($entradas->where('ent_tipo','oferta_mis')->first() ?
//                                                 App\Models\Fin_ofertaMissoes::select('valor')->where('entrada_id',$entradas->where('ent_tipo','oferta_mis')->first()->id)->where('congregacao_id',$con->id)->get()->sum('valor') : 0)

//                                         );
//                                      $listaCongregacoes .= '</td>'
//                                     . '</tr>';
//                             endforeach;
//                         endif;

//                         $listaEntradas .=$listaCongregacoes;
//                     $listaEntradas .= '</table><br>'

//             . '</div>';

//             $listaEntradas .= '<div class="box-saidas">'
//                     .'<h4>DESPESAS</h4><table class="table" border="1">';
//                         $despesas = App\Models\CadastroDespesas::get();
//                         $grupos = App\Models\Fin_gruposDespesas::get();
//                         $subgrupos = \App\Models\Fin_subGruposDespesas::get();
//                         $saidas = App\Models\Fin_saidas::where('financa_id',$financas->first()->id)->get();
//                         $totalSaidas = 0;
//                         $totalGrupo = 0;

//                         if($grupos):
//                             foreach ($grupos as $g):
//                                 $listaEntradas .= '<tr>'
//                                     .'<td  class="texto_tabela text_center" colspan="2" style="font-weight:bold; text-transform:uppercase; background:#f2f2f2;">'.$g->nome.'</td>'
//                                 .'</tr>';

//                                 if($saidas):

//                                     //Chama função de saidas de despesas com subgrupo e retorna nomes dos subgrupo e valores
//                                     $despesas_com_subgrupo = despesas_com_subgrupo($g,$saidas, $despesas, $subgrupos);
//                                     foreach($despesas_com_subgrupo as $desps):
//                                         $totalGrupo = $totalGrupo+$desps['valor'];
//                                         $totalSaidas = $totalSaidas +$desps['valor'];
//                                         $listaEntradas .=
//                                             '<tr>'
//                                                 .'<td class="texto_tabela">'.$desps['nome'].'</td>'
//                                                 .'<td class="texto_tabela">R$ '.inteiroParaReal($desps['valor']).'</td>'
//                                             .'</tr>';
//                                     endforeach;

//                                     foreach($saidas as $sai)://ENTRA NA CONDIÇÃO ONDE AS SAÍDAS ESTÃO ATRELADAS A DESPESAS QUE NÃO TEM SUBGRUPO
//                                         $despesas_sem_subgrupo = $despesas->where('id',$sai->despesa_id)->first();
//                                         if($despesas_sem_subgrupo && $despesas_sem_subgrupo->grupo_id == $g->id):
//                                             if(empty($despesas_sem_subgrupo->subgrupo_id)):

//                                                 if($despesas_sem_subgrupo->modo == '%'):
//                                                     $total = $totalEntradas+$saldoAnterior->ent_saldo_anterior;
//                                                     $valor_percent = $despesas_sem_subgrupo->valor*$total/100;
//                                                     $totalGrupo = $totalGrupo+ $valor_percent;
//                                                     $totalSaidas = $totalSaidas + $valor_percent;

//                                                     $listaEntradas .=
//                                                             '<tr><td class="texto_tabela">'.$despesas_sem_subgrupo->nome.'</td>'
//                                                                 .'<td class="texto_tabela">R$ '.inteiroParaReal($valor_percent).'</td>'
//                                                         .'</tr>';

//                                                 elseif($despesas_sem_subgrupo->modo == '$'):
//                                                     if(!empty($despesas_sem_subgrupo->valor)):
//                                                         $valor_semSubGrupo = $despesas_sem_subgrupo->valor;
//                                                         $totalGrupo = $totalGrupo+$valor_semSubGrupo;
//                                                         $totalSaidas = $totalSaidas+$valor_semSubGrupo;
//                                                         $listaEntradas .=
//                                                                     '<tr><td class="texto_tabela">'.$despesas_sem_subgrupo->nome.'</td>'
//                                                                         .'<td class="texto_tabela">R$ '.inteiroParaReal($valor_semSubGrupo).'</td>'
//                                                                 .'</tr>';

//                                                     else:
//                                                         $valor_semSubGrupo = $sai->sai_valor;
//                                                         $totalGrupo = $totalGrupo+$valor_semSubGrupo;
//                                                         $totalSaidas = $totalSaidas+$valor_semSubGrupo;
//                                                         $listaEntradas .=
//                                                             '<tr><td class="texto_tabela">'.$despesas_sem_subgrupo->nome.'</td>'
//                                                                 .'<td class="texto_tabela">R$ '.inteiroParaReal($valor_semSubGrupo).'</td>'
//                                                         .'</tr>';
//                                                     endif;
//                                                 endif;
//                                             endif;
//                                         endif;
//                                     endforeach;

//                                     $listaEntradas .=
//                                         '<tr style="background:#f2f2f2;">'
//                                             .'<td style=" font-weight:bold; font-size:10px;">VALOR TOTAL DO GRUPO</td>'
//                                             .'<td style="  font-weight:bold; font-size:10px;">R$ '.inteiroParaReal($totalGrupo).'</td>'
//                                         .'</tr>'

//                                         .'<tr>'
//                                             .'<td colspan="2"></td>'
//                                         .'</tr>';

//                                     $totalGrupo = 0;
//                                 endif;
//                             endforeach;
//                         endif;

//                     $listaEntradas .= '</table></div>';

//                     //FECHAMENTO DO RELATÓRIO

//                     $listaEntradas .= '<table class="table" border="1" style="text-transform:uppercase;">'
//                         .'<tr class="totalParcial">'
//                             .'<td class="texto-totalParcial">TOTAL DAS ENTRADAS</td>'
//                             .'<td align="right" class="texto-totalParcial">R$ '.valorReal($totalEntradas).'</td>'
//                         .'</tr>'
//                         .'<tr  class="totalParcial">'
//                             .'<td class="texto-totalParcial">SALDO DO MÊS ANTERIOR</td>'
//                             .'<td align="right" class="texto-totalParcial"> R$'.valorReal($saldoAnterior->ent_saldo_anterior).'</td>'
//                         .'</tr>'

//                         .'<tr class="totalEntradas">'
//                             .'<td class="texto-totalEntradas">TOTAL GERAL DAS RECEITAS</td>'
//                             .'<td align="right" class="texto-totalEntradas">R$ '.valorReal($totalEntradas+$saldoAnterior->ent_saldo_anterior).'</td>'
//                         .'</tr>'
//                     .'</table>'  ;

//                      $listaEntradas .= '<div class="" style="margin-top:10px;"><table class="table" border="1">'.
//                              '<tr class="texto-totalEntradas">'.
//                                 '<td width="50%" style="text-align:center; font-weight:bold;">TOTAL GERAL DAS ENTRADAS</td>'.
//                                 '<td style="text-align:center; font-weight:bold;">R$ '.valorReal($totalEntradas+$saldoAnterior->ent_saldo_anterior).'</td>'.
//                              '</tr>'.

//                              '<tr class="texto-totalEntradas">'.
//                                 '<td width="50%" style="text-align:center; font-weight:bold;">TOTAL GERAL DAS SAÍDAS</td>'.
//                                 '<td style="text-align:center; font-weight:bold;">R$ '.valorReal($totalSaidas).'</td>'.
//                              '</tr>'.

//                              '<tr style="background:#999;">'.
//                                 '<td width="50%" style="text-align:center; font-weight:bold;" class="texto-totalEntradas">SALDO</td>'.
//                                 '<td style="text-align:center; font-weight:bold;" class="texto-totalEntradas">R$ '.valorReal(($totalEntradas+$saldoAnterior->ent_saldo_anterior)-$totalSaidas).'</td>'.
//                              '</tr>'.

//                      '</table></div>';


//             $conteudo .= verificarPalavrasChave(cabecalhoPDF(),null).$titulo.$listaEntradas;

//         endif;
//     endif;

//     return $conteudo;
// }

function despesas_com_subgrupo($g,$saidas,$despesas,$subgrupos){
    $data =[];
    $des = [];
    foreach($saidas as $sa):
        array_push($des,$sa->despesa_id);
    endforeach;

    //FILTRANDO DESPESAS PELO GRUPO E PELAS SAÍDAS CADASTRADAS
    $despesas_com_subgrupo = $despesas->where('grupo_id',$g->id)->whereIn('id',$des);

    if($despesas_com_subgrupo):
        $sub_id = [];
        foreach($despesas_com_subgrupo as $ent_sub):
            array_push($sub_id, $ent_sub->subgrupo_id);//ADICIONANDO IPS DOS SUBGRUPOS AO ARRAY 'SUB_ID'
        endforeach;

        $sub = $subgrupos->whereIn('id',$sub_id);//FILTRANDO SUBGRUPOS VINDOS DAS DESPESAS E SAÍDAS CADASTRADAS

        foreach ($sub as $s):
                $despFinal = $despesas->where('subgrupo_id',$s->id);//FILTRANDO DESPESAS POR CADA SUBGRUPO
                $ent = [];
                foreach($despFinal as $dp):
                    array_push($ent, $dp->id);//ADICIONANDO IDS DAS DESPESAS AO ARRAY 'ENT'
                endforeach;

                $valor_subs = $saidas->whereIn('despesa_id',$ent)->sum('sai_valor');

                unset($ent);//LIMPA ARRAY DAS ENTRADAS PARA SER PREENCHIDO NO PRÓXIMO LOOP
                $dados = ['nome' => $s->nome,'valor' => $valor_subs];
                array_push($data,$dados);

        endforeach;
        return $data;
    endif;
}

// function entradasDizimosSede($sede,$entradas,$igreja,$obreiro){
//     $Fin_OfertaDizimoCongregacao = $entradas->where('ent_tipo','oferta_diz');
//     $Fin_dizimoCongregacoes = $entradas->where('ent_tipo','diz_con');
//     $Fin_dizimoDiscipulos = $entradas->where('ent_tipo','diz_disc');

//     $valor_ofertaDizimoSede = ($Fin_OfertaDizimoCongregacao->first()
//                 ? App\Models\Fin_ofertaDizimoCongregacao::select('valor')->where('entrada_id',$Fin_OfertaDizimoCongregacao->first()->id)->where('congregacao_id',$sede->id)->get()->sum('valor') : 0);

//     $valor_dizimoSede = ($Fin_dizimoCongregacoes->first()
//             ? \App\Models\Fin_dizimoCongregacoes::select('valor')->where('entrada_id',$Fin_dizimoCongregacoes->first()->id)->where('congregacao_id',$sede->id)->get()->sum('valor') : 0);

//     $discipulos = [];
//         $discipulosSede = App\Models\Discipulo::select('id')->where('disc_congregacao_id',$sede->id)->get();
//         if($discipulosSede):
//             foreach ($discipulosSede as $disc):
//                 array_push($discipulos,$disc->id);
//             endforeach;
//         endif;

//         $valor_dizimoDiscipulosSede = ($Fin_dizimoDiscipulos->first() ?
//                 \App\Models\Fin_dizimoDiscipulos::select('valor')->where('entrada_id',$Fin_dizimoDiscipulos->first()->id)->whereIn('discipulo_id',$discipulos)->whereNotIn('discipulo_id',$obreiro)->get()->sum('valor') : 0);


//         $total_EntradasSede = $valor_ofertaDizimoSede+$valor_dizimoSede+$valor_dizimoDiscipulosSede;

//         return $total_EntradasSede;
// }

// function entradasOfertasSede($sede,$entradas,$igreja,$obreiro){
//     $Fin_ofertaCongregacoes = $entradas->where('ent_tipo','oferta_con');
//     $Fin_ofertaMissoes = $entradas->where('ent_tipo','oferta_mis');


//     $valor_ofertaSede = ($Fin_ofertaCongregacoes->first()
//                 ? \App\Models\Fin_ofertaCongregacoes::select('valor')->where('entrada_id',$Fin_ofertaCongregacoes->first()->id)->where('congregacao_id',$sede->id)->get()->sum('valor') : 0);

//     $discipulos = [];
//         $discipulosSede = App\Models\Discipulo::select('id')->where('disc_congregacao_id',$sede->id)->get();
//         if($discipulosSede):
//             foreach ($discipulosSede as $disc):
//                 array_push($discipulos,$disc->id);
//             endforeach;
//         endif;

//         $valor_MissoesSede = ($Fin_ofertaMissoes->first() ?
//                 App\Models\Fin_ofertaMissoes::select('valor')->where('entrada_id',$Fin_ofertaMissoes->first()->id)->where('congregacao_id',$sede->id)->get()->sum('valor') : 0);


//         $total_EntradasSede = $valor_MissoesSede+$valor_ofertaSede;

//         return $total_EntradasSede;
// }

// function entradasDonativos($entradas,$igreja){
//     $entradas_donativo = $entradas->where('ent_tipo','donativo')->first();
//     if($entradas_donativo):
//         $donativos = \App\Models\Fin_donativos::select('valor')->where('entrada_id', $entradas_donativo->id)->get();
//         $ValorDonativo = $donativos->sum('valor');
//     else:
//         $ValorDonativo = 0;
//     endif;

//     return $ValorDonativo;
// }

// function entradasCongregacoes($sede,$entradas,$igreja,$obreiro){
//     $Fin_OfertaDizimoCongregacao = $entradas->where('ent_tipo','oferta_diz');
//     $Fin_ofertaCongregacoes = $entradas->where('ent_tipo','oferta_con');
//     $Fin_dizimoCongregacoes = $entradas->where('ent_tipo','diz_con');
//     $Fin_dizimoDiscipulos = $entradas->where('ent_tipo','diz_disc');
//     $Fin_ofertaMissoes = $entradas->where('ent_tipo','oferta_mis');
//     //Entrads das congregações exceto da sede
//         $valor_ofertaDizimoCongregacoes = ($Fin_OfertaDizimoCongregacao->first()
//                 ? App\Models\Fin_ofertaDizimoCongregacao::select('valor')->where('entrada_id',$Fin_OfertaDizimoCongregacao->first()->id)->where('congregacao_id', '!=',$sede->id)->get()->sum('valor') : 0);

//         $valor_ofertaCongregacoes = ($Fin_ofertaCongregacoes->first()
//                 ? \App\Models\Fin_ofertaCongregacoes::select('valor')->where('entrada_id',$Fin_ofertaCongregacoes->first()->id)->where('congregacao_id','!=',$sede->id)->get()->sum('valor') : 0);

//         $valor_dizimoCongregacoes = ($Fin_dizimoCongregacoes->first()
//                 ? \App\Models\Fin_dizimoCongregacoes::select('valor')->where('entrada_id',$Fin_dizimoCongregacoes->first()->id)->where('congregacao_id','!=',$sede->id)->get()->sum('valor') : 0);

//         $discCongregacoes = [];
//         $discipulosCongregacoes = App\Models\Discipulo::select('id')->where('disc_congregacao_id','!=',$sede->id)->get();
//         if($discipulosCongregacoes):
//             foreach ($discipulosCongregacoes as $disc):
//                 array_push($discCongregacoes,$disc->id);
//             endforeach;
//         endif;
//         $valor_dizimoDiscipulosCongregacoes = ($Fin_dizimoDiscipulos->first() ?
//                 \App\Models\Fin_dizimoDiscipulos::select('valor')->where('entrada_id',$Fin_dizimoDiscipulos->first()->id)->whereIn('discipulo_id',$discCongregacoes)->whereNotIn('discipulo_id',$obreiro)->get()->sum('valor') : 0);

//         $valor_MissoesCongregacoes = ($Fin_ofertaMissoes->first() ?
//                 App\Models\Fin_ofertaMissoes::select('valor')->where('entrada_id',$Fin_ofertaMissoes->first()->id)->where('congregacao_id','!=',$sede->id)->get()->sum('valor') : 0);

//         $total_EntradasCongregacoes = $valor_ofertaDizimoCongregacoes+$valor_ofertaCongregacoes+$valor_dizimoCongregacoes+$valor_dizimoDiscipulosCongregacoes+$valor_MissoesCongregacoes;

//         return $total_EntradasCongregacoes;
// }

// function cabecalhoPDF() {
//      $opcao = \App\Models\Opcao::where('op_nome','cabecalho_relatorios')->get();

//      if($opcao->first()):
//          return $opcao->first()->op_conteudo;
//      endif;
// }
