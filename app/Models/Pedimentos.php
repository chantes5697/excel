<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedimentos extends Model
{
    /** @use HasFactory<\Database\Factories\PedimentosFactory> */
    use HasFactory;

    protected $fillable = [
        'id_proveedor', 'id_planta', 'numero_pedimento', 'division', 
        'periodo', 'avance', 'estatus', 'responsable', 
        'procesos', 'inicio_proceso', 'tipo'
    ];
}
