<?php

namespace App\Http\Controllers\Asignacion;

use App\Carnet;
use App\AsignacionEmpleado;
use Illuminate\Http\Request;
use App\DetalleAsignacionEmpleado;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class AsignacionEmpleadoController extends ApiController
{
    public function __construct()
    {
        #parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $asignaciones = AsignacionEmpleado::with('planificacion.buque')->get();
        return $this->showAll($asignaciones);
    }


    /**
     */
    public function store(Request $request)
    {
        $rules = [
            'planificacion_id' => 'required|integer',
            'detalle_asignacion'=>'required'
        ];

        $db = DB::connection('rrh');

        $db->beginTransaction();
            $this->validate($request,$rules);
            $data = $request->all();

            $asignacion = AsignacionEmpleado::create($data);

            foreach ($request->detalle_asignacion as $d) {
                $data_d = new DetalleAsignacionEmpleado;
                $data_d->turno_id = $d['turno_id'];
                $data_d->asignacion_empleado_id = $asignacion->id;
                $data_d->carnet_id = $d['carnet_id'];
                $data_d->empleado_id = $d['empleado_id'];
                $data_d->fecha = $d['fecha'];

                $data_d->save();

                $carnet = Carnet::find($d['carnet_id']);
                $carnet->asignado = true;
                $carnet->save();
            }

        $db->commit();

        return $this->showOne($asignacion,201,'insert');
    }

    /**
     */
    public function show(AsignacionEmpleado $asignacion_empleado)
    {
        return $this->showOne($asignacion_empleado,200,'select');
    }

    /**
     */
    public function update(Request $request, AsignacionEmpleado $asignacion_empleado)
    {
        $rules = [
            'planificacion_id' => 'required|integer',
        ];

        $this->validate($request,$rules);

        $asignacion_empleado->planificacion_id = $request->planificacion_id;

        if(!$asignacion_empleado->isDirty())
        {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar',422);
        }

        $asignacion_empleado->save();

        return $this->showOne($asignacion_empleado,201,'update');

    }

    /**
     */
    public function destroy(AsignacionEmpleado $asignacion_empleado)
    {
        $asignacion_empleado->delete();

        return $this->showOne($asignacion_empleado,201,'delete');
    }
}
