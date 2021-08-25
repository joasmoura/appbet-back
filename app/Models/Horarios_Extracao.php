<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horarios_Extracao extends Model
{
    use HasFactory;
    protected $table = 'horarios_extracao';
    protected $fillable = ['nome','hora','extracao_id','regiao_id'];

    public function extracao(){
        return $this->hasOne(Extracao::class,'id','extracao_id');
    }

    public function premios(){
        return $this->hasOne(Premio::class,'id','horarios_id');
    }

    public function regiao(){
        return $this->hasOne(Regiao::class,'id','regiao_id');
    }

    public function apostas(){
        return $this->hasMany(Aposta::class,'horario_id','id');
    }
}
