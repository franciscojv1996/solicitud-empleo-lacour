<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitudEmpleoRequest;
use App\Models\Documento;
use App\Models\Educacion;
use App\Models\ExperienciaLaboral;
use App\Models\Familiar;
use App\Models\ReferenciaPersonal;
use App\Models\Solicitante;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SolicitudEmpleoController extends Controller
{
    public function index()
    {
        return view('solicitud-empleo.index');
    }

    public function store(SolicitudEmpleoRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // 1) Crear solicitante
            $solicitante = Solicitante::create([
                'cargo_aspira'       => $request->cargo_aspira,
                'fecha_solicitud'    => $request->fecha_solicitud,
                'empleado_actual'    => $request->empleado_actual ?? 'No',
                'sueldo_aspira'      => $request->sueldo_aspira,
                'nombres'            => $request->nombres,
                'cedula_tipo'        => $request->cedula_tipo,
                'cedula_numero'      => $request->cedula_numero,
                'direccion'          => $request->direccion,
                'telefono'           => $request->telefono,
                'otra_direccion'     => $request->otra_direccion,
                'correo'             => $request->correo,
                'sexo'               => $request->sexo,
                'estado_civil'       => $request->estado_civil,
                'lugar_nacimiento'   => $request->lugar_nacimiento,
                'fecha_nacimiento'   => $request->fecha_nacimiento,
                'edad'               => $request->edad,
                'nacionalidad'       => $request->nacionalidad,
                'gaceta_num'         => $request->gaceta_num,
                'gaceta_fecha'       => $request->gaceta_fecha,
                'vehiculo'           => $request->vehiculo ?? 'No',
                'licencia'           => $request->licencia ?? 'No',
                'licencia_detalles'  => $request->licencia_detalles,
                'certificado_medico' => $request->certificado_medico,
                'fecha_vcto_medico'  => $request->fecha_vcto_medico,
                'talla_camisa'       => $request->talla_camisa,
                'talla_pantalon'     => $request->talla_pantalon,
                'talla_botas'        => $request->talla_botas,
                'estatura'           => $request->estatura,
                'peso'               => $request->peso,
                'acepto_terminos'    => 1,
            ]);

            // 2) Familiares
            foreach ((array) $request->input('familiar_nombre', []) as $i => $nombre) {
                if (empty($nombre) && empty($request->input("familiar_parentesco.$i"))) continue;
                Familiar::create([
                    'solicitante_id'   => $solicitante->id,
                    'parentesco'       => $request->input("familiar_parentesco.$i"),
                    'nombre'           => $nombre,
                    'telefono'         => $request->input("familiar_telefono.$i"),
                    'cedula'           => $request->input("familiar_cedula.$i"),
                    'fecha_nacimiento' => $request->input("familiar_fecha_nac.$i"),
                    'edad'             => $request->input("familiar_edad.$i"),
                ]);
            }

            // 3) Educación
            foreach ((array) $request->input('edu_instituto', []) as $i => $instituto) {
                if (empty($instituto) && empty($request->input("edu_nivel.$i"))) continue;
                Educacion::create([
                    'solicitante_id' => $solicitante->id,
                    'nivel'          => $request->input("edu_nivel.$i"),
                    'instituto'      => $instituto,
                    'anos_cursados'  => $request->input("edu_anos.$i"),
                    'fecha_desde'    => $request->input("edu_desde.$i") ?: null,
                    'fecha_hasta'    => $request->input("edu_hasta.$i") ?: null,
                    'obtuvo_titulo'  => $request->input("edu_titulo.$i"),
                    'especialidad'   => $request->input("edu_especialidad.$i"),
                ]);
            }

            // 4) Experiencia laboral
            foreach ((array) $request->input('exp_empresa', []) as $i => $empresa) {
                if (empty($empresa) && empty($request->input("exp_cargo.$i"))) continue;
                ExperienciaLaboral::create([
                    'solicitante_id'    => $solicitante->id,
                    'empresa'           => $empresa,
                    'direccion'         => $request->input("exp_direccion.$i"),
                    'telefono'          => $request->input("exp_telefono.$i"),
                    'jefe_inmediato'    => $request->input("exp_jefe.$i"),
                    'fecha_ingreso'     => $request->input("exp_fecha_ingreso.$i") ?: null,
                    'fecha_egreso'      => $request->input("exp_fecha_egreso.$i") ?: null,
                    'sueldo_inicial'    => $request->input("exp_sueldo_inicial.$i"),
                    'sueldo_final'      => $request->input("exp_sueldo_final.$i"),
                    'cargo_desempenado' => $request->input("exp_cargo.$i"),
                    'motivo_retiro'     => $request->input("exp_motivo.$i"),
                    'labores_realizadas'=> $request->input("exp_labores.$i"),
                ]);
            }

            // 5) Referencias personales
            foreach ((array) $request->input('ref_nombre', []) as $i => $nombre) {
                if (empty($nombre)) continue;
                ReferenciaPersonal::create([
                    'solicitante_id'  => $solicitante->id,
                    'nombre'          => $nombre,
                    'empresa'         => $request->input("ref_empresa.$i"),
                    'direccion'       => $request->input("ref_direccion.$i"),
                    'telefono'        => $request->input("ref_telefono.$i"),
                    'ocupacion'       => $request->input("ref_ocupacion.$i"),
                    'tiempo_conocido' => $request->input("ref_tiempo.$i"),
                ]);
            }

            // 6) Documentos (archivos)
            $docData = ['solicitante_id' => $solicitante->id];
            if ($request->hasFile('file_cv')) {
                $docData['cv_ruta'] = $request->file('file_cv')->store('uploads', 'public');
            }
            if ($request->hasFile('file_cedula')) {
                $docData['cedula_ruta'] = $request->file('file_cedula')->store('uploads', 'public');
            }
            if ($request->hasFile('file_rif')) {
                $docData['rif_ruta'] = $request->file('file_rif')->store('uploads', 'public');
            }
            Documento::create($docData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '¡Gracias! Tu solicitud de empleo ha sido enviada correctamente.',
                'id'      => $solicitante->id,
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $msg = ($e->getCode() === '23000')
                ? 'Ya existe una solicitud con esos datos (cédula, correo o teléfono).'
                : 'Error al guardar: ' . $e->getMessage();
            return response()->json(['success' => false, 'message' => $msg], 422);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }
}