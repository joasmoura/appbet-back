<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regiao extends Model
{
    use HasFactory;
    protected $table = 'regioes';
    protected $fillable = ['nome'];

    public function horarios(){
        return $this->hasMany(Horarios_Extracao::class,'regiao_id','id');
    }

    public function mercado(){
        return $this->hasOne(Mercado::class);
    }
}
