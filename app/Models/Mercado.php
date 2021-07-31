<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mercado extends Model
{
    use HasFactory;
    protected $table = 'mercados';
    protected $fillable = ['regiao_id', 'regiao_id',
    'grupo', 'dezena', 'centena', 'milhar', 'duque_grupo',
    'terno_grupo', 'terno_dezena', 'milhar_centena',
    'milhar_invertida', 'mc_invertida', 'centena_invertida',
    'duque_dezena', 'passe_combinado',
    'terno_grupo_combinado', 'passe_seco', 'terno_dezena_cercado',
    'grupo_combinado','passe_vai_vem','passe_vai'];
}
