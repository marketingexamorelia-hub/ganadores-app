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
        'premio_especial', // <-- Agregado aquí
    ];

    protected $casts = [
        'caza_premios'    => 'boolean',
        'alerta'          => 'boolean',
        'premio_especial' => 'boolean', // <-- Agregado aquí
        'fecha_dinamica'  => 'date',
        'fecha_entrega'   => 'date',
    ];

    /**
     * Mutuamente excluyente: Si se marca uno, se desmarca el otro.
     * (Ajusta esta lógica si el premio especial debe ser exclusivo con los otros o independiente).
     */
    protected static function booted()
    {
        static::saving(function ($ganador) {
            if ($ganador->alerta) {
                $ganador->caza_premios = false;
                $ganador->premio_especial = false; // Opcional si deseas que sea excluyente
            } elseif ($ganador->caza_premios) {
                $ganador->alerta = false;
                $ganador->premio_especial = false; // Opcional si deseas que sea excluyente
            } elseif ($ganador->premio_especial) {
                $ganador->alerta = false;
                $ganador->caza_premios = false; // Opcional si deseas que sea excluyente
            }
        });
    }
}