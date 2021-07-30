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
    'terno_duque', 'duque_dezena', 'passe_combinado',
    'terno_duque_combinado', 'passe_seco'];
}
