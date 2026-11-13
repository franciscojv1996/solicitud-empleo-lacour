<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenciaPersonal extends Model
{
    protected $table = 'referencias_personales';
    public $timestamps = false;

    protected $fillable = [
        'solicitante_id', 'nombre', 'empresa', 'direccion',
        'telefono', 'ocupacion', 'tiempo_conocido',
    ];

    public function solicitante() { return $this->belongsTo(Solicitante::class, 'solicitante_id'); }
}
