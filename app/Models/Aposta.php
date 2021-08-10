<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aposta extends Model
{
    use HasFactory;
    protected $table = 'apostas';
    protected $fillable = ['horario_id','codigo','user_id','total','status'];

    public function itens(){
        return $this->hasMany(ItensAposta::class,'aposta_id','id');
    }

    public function cambista(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function horario(){
        return $this->hasOne(Horarios_Extracao::class,'id','horario_id');
    }

}
