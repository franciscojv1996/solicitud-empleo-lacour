<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitante extends Model
{
    protected $table = 'solicitantes';
    public $timestamps = false;

    protected $fillable = [
        'cargo_aspira', 'fecha_solicitud', 'empleado_actual', 'sueldo_aspira',
        'nombres', 'cedula_tipo', 'cedula_numero', 'direccion', 'telefono',
        'otra_direccion', 'correo', 'sexo', 'estado_civil', 'lugar_nacimiento',
        'fecha_nacimiento', 'edad', 'nacionalidad', 'gaceta_num', 'gaceta_fecha',
        'vehiculo', 'licencia', 'licencia_detalles', 'certificado_medico',
        'fecha_vcto_medico', 'talla_camisa', 'talla_pantalon', 'talla_botas',
        'estatura', 'peso', 'acepto_terminos',
    ];

    protected $casts = [
        'fecha_solicitud'   => 'date',
        'fecha_nacimiento'  => 'date',
        'gaceta_fecha'      => 'date',
        'fecha_vcto_medico' => 'date',
        'acepto_terminos'   => 'boolean',
    ];

    public function familiares()            { return $this->hasMany(Familiar::class, 'solicitante_id'); }
    public function educacion()             { return $this->hasMany(Educacion::class, 'solicitante_id'); }
    public function experienciaLaboral()    { return $this->hasMany(ExperienciaLaboral::class, 'solicitante_id'); }
    public function referenciasPersonales() { return $this->hasMany(ReferenciaPersonal::class, 'solicitante_id'); }
    public function documentos()            { return $this->hasOne(Documento::class, 'solicitante_id'); }
}