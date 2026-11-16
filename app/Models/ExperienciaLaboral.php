<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienciaLaboral extends Model
{
    protected $table = 'experiencia_laboral';

    public $timestamps = false;

    protected $fillable = [
        'solicitante_id', 'empresa', 'direccion', 'telefono', 'jefe_inmediato',
        'fecha_ingreso', 'fecha_egreso', 'sueldo_inicial', 'sueldo_final',
        'cargo_desempenado', 'motivo_retiro', 'labores_realizadas',
    ];

    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class, 'solicitante_id');
    }
}
