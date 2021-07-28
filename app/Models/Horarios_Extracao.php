<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horarios_Extracao extends Model
{
    use HasFactory;
    protected $table = 'horarios_extracao';
    protected $fillable = ['nome','hora','extracao_id','regiao_id'];
}
