<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reniec extends Model
{
    use HasFactory;

    protected $table = 'reniec';
    protected $fillable = ['nuDniUsuario','nuRucUsuario', 'password', 'estado', 'n_consult', 'created_at', 'updated_at'];

    
}
