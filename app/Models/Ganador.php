<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ganador extends Model
{
    use HasFactory;

    protected $table = 'ganadores';

    protected $fillable = [
        'nombre',
        'edad',
        'whatsapp',
        'facebook_id',
        'fecha_dinamica',
        'fecha_entrega',
        'programa',
        'premio',
        'patrocinador',
        'caza_premios',
        'alerta',
    ];

    protected $casts = [
        'caza_premios'   => 'boolean',
        'alerta'         => 'boolean',
        'fecha_dinamica' => 'date',
        'fecha_entrega'  => 'date',
    ];

    /**
     * Mutuamente excluyente: Si se marca uno, se desmarca el otro.
     */
    protected static function booted()
    {
        static::saving(function ($ganador) {
            if ($ganador->alerta) {
                $ganador->caza_premios = false;
            } elseif ($ganador->caza_premios) {
                $ganador->alerta = false;
            }
        });
    }
}