<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SolicitudEmpleoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Datos del cargo
            'cargo_aspira'      => 'required|string|max:100',
            'fecha_solicitud'   => 'required|date',
            'empleado_actual'   => 'nullable|in:Si,No',
            'sueldo_aspira'     => 'nullable|string|max:50',

            // Datos personales
            'nombres'           => 'required|string|max:150',
            'cedula_tipo'       => 'required|in:V,E',
            'cedula_numero'     => ['required', 'string', 'max:20',
                                    Rule::unique('solicitantes', 'cedula_numero')
                                        ->where('cedula_tipo', $this->input('cedula_tipo'))],
            'direccion'         => 'nullable|string',
            'telefono'          => 'required|string|max:20|unique:solicitantes,telefono',
            'otra_direccion'    => 'nullable|string',
            'correo'            => 'required|email|max:100|unique:solicitantes,correo',
            'sexo'              => 'nullable|in:Masculino,Femenino',
            'estado_civil'      => 'nullable|string|max:50',
            'lugar_nacimiento'  => 'nullable|string|max:100',
            'fecha_nacimiento'  => 'nullable|date',
            'edad'              => 'nullable|integer|min:14|max:100',
            'nacionalidad'      => 'nullable|string|max:50',
            'gaceta_num'        => 'nullable|string|max:50',
            'gaceta_fecha'      => 'nullable|date',
            'vehiculo'          => 'nullable|in:Si,No',
            'licencia'          => 'nullable|in:Si,No',
            'licencia_detalles' => 'nullable|string|max:100',
            'certificado_medico'=> 'nullable|string|max:100',
            'fecha_vcto_medico' => 'nullable|date',
            'talla_camisa'      => 'nullable|string|max:10',
            'talla_pantalon'    => 'nullable|string|max:10',
            'talla_botas'       => 'nullable|string|max:10',
            'estatura'          => 'nullable|string|max:10',
            'peso'              => 'nullable|string|max:10',

            // Declaración
            'acceptTerms'       => 'accepted',

            // Anexos (archivos)
            'file_cv'           => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'file_cedula'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_rif'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Arrays dinámicos
            'familiar_nombre.*'      => 'nullable|string|max:150',
            'edu_instituto.*'        => 'nullable|string|max:150',
            'exp_empresa.*'          => 'nullable|string|max:150',
            'ref_nombre.*'           => 'nullable|string|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'cedula_numero.unique' => 'Ya existe una solicitud con esa cédula.',
            'correo.unique'        => 'Ya existe una solicitud con ese correo.',
            'telefono.unique'      => 'Ya existe una solicitud con ese teléfono.',
            'acceptTerms.accepted' => 'Debes aceptar los términos y condiciones.',
        ];
    }
}