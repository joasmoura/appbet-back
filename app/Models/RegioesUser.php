<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegioesUser extends Model
{
    use HasFactory;
    protected $table = 'regioes_users';
    protected $fillable = ['user_id','regiao_id'];
}
