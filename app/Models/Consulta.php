<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;
    
    protected $table = 'consultas';
    
    protected $fillable = [
        'proveedor',
        'credencial_id',
        'documento_consultado',
        'exitoso',
        'codigo_respuesta'
    ];
    
    // Relación con Reniec
    public function credencialReniec()
    {
        return $this->belongsTo(Reniec::class, 'credencial_id');
    }
    
    // Relación con Migraciones
    public function credencialMigraciones()
    {
        return $this->belongsTo(Migraciones::class, 'credencial_id');
    }
}