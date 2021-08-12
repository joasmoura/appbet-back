<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sorteados extends Model
{
    use HasFactory;
    protected $table = 'sorteados';
    protected $fillable = ['resultado_id','item_aposta_id','numero_premio','numero_sorteado','valor'];

    public function resultado(){
        return $this->hasOne(Premio::class,'resultado_id','id');
    }
}
