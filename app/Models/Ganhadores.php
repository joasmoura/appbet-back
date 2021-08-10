<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ganhadores extends Model
{
    use HasFactory;
    protected $table = 'ganhadores';
    protected $fillable = ['user_id','premio_id','valor'];
}
