<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extracao extends Model
{
    use HasFactory;
    protected $table = 'extracoes';
    protected $fillable = ['data','status'];

    public function horas(){
        return $this->hasMany(Horarios_Extracao::class,'extracao_id','id');
    }

    public function getDataFormatAttribute(){
        return date('d/m/Y',strtotime($this->data));
    }
}
