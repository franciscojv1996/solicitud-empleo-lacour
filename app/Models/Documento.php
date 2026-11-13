<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';
    public $timestamps = false;

    protected $fillable = ['solicitante_id', 'cv_ruta', 'cedula_ruta', 'rif_ruta'];

    public function solicitante() { return $this->belongsTo(Solicitante::class, 'solicitante_id'); }
}