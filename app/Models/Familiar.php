<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Familiar extends Model
{
    protected $table = 'familiares';
    public $timestamps = false;

    protected $fillable = [
        'solicitante_id', 'parentesco', 'nombre', 'telefono',
        'cedula', 'fecha_nacimiento', 'edad',
    ];

    public function solicitante() { return $this->belongsTo(Solicitante::class, 'solicitante_id'); }
}