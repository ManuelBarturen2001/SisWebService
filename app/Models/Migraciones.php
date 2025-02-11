<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Migraciones extends Model
{
    use HasFactory;

    protected $table = 'migraciones';
    protected $fillable = ['username','password','ip','nivelacceso','estado', 'created_at', 'updated_at'];
}
