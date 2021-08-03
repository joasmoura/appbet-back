<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItensAposta extends Model
{
    use HasFactory;
    protected $table = 'itens_apostas';
    protected $fillable = ['aposta_id','modalidade','valor','subtotal','numero','poss_ganho','premio_de','premio_ate'];
}
