<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Educacion extends Model
{
    protected $table = 'educacion';
    public $timestamps = false;

    protected $fillable = [
        'solicitante_id', 'nivel', 'instituto', 'anos_cursados',
        'fecha_desde', 'fecha_hasta', 'obtuvo_titulo', 'especialidad',
    ];

    public function solicitante() { return $this->belongsTo(Solicitante::class, 'solicitante_id'); }
}