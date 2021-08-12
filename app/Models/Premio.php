<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Premio extends Model
{
    use HasFactory;
    protected $table = 'premios_horarios';
    protected $fillable = ['horario_id','premio_1','premio_2','premio_3',
    'premio_4','premio_5','premio_6','premio_7'];

    public function sorteados(){
        return $this->hasMany(Sorteados::class,'resultado_id','id');
    }
}
