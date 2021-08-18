<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComissoesGerente extends Model
{
    use HasFactory;
    protected $table = 'comissoes_gerentes';
    protected $fillable = ['aposta_id','valor'];

    public function aposta(){
        return $this->hasOne(Aposta::class,'id','aposta_id');
    }
}
