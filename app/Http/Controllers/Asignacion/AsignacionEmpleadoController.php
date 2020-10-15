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
        //parent::__construct(); //validacion de autenticacion
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
            'turno_id'=>'required',
            'carnet_id'=>'required',
            'empleado_id'=>'required',
            'fecha'=>'required'
        ];

        $db = DB::connection('rrh');

        $db->beginTransaction();
            $this->validate($request,$rules);

            $data = $request->all();

            $asignacion = AsignacionEmpleado::create($data);

            $data_d = new DetalleAsignacionEmpleado;
            $data_d->turno_id = $request->turno_id;
            $data_d->asignacion_empleado_id = $asignacion->id;
            $data_d->carnet_id = $request->carnet_id;
            $data_d->empleado_id = $request->empleado_id;
            $data_d->fecha = $request->fecha;

            $data_d->save();

            $carnet = Carnet::find($request->carnet_id);
            $carnet->asignado = true;
            $carnet->save();
        $db->commit();

        return $this->showOne($asignacion,201,'insert');
    }

    /**
     */
    public function show(AsignacionEmpleado $asignacion_empleado)
    {
        return $this->showOne($asignacion_empleado,200,'select');
    }

    //obtener asignacion por turno e id en detalle asignacion empleado
    public function getDataTurn($id,$turno_id){
        $detalle = DetalleAsignacionEmpleado::where([['asignacion_empleado_id',$id],['turno_id',$turno_id]])->with('empleado','turno','carnet','asignacion.planificacion')->first();

        return $this->showOne($detalle);
    }

    /**
     */
    public function update(Request $request, AsignacionEmpleado $asignacion_empleado)
    {
        $rules = [
            'planificacion_id' => 'required|integer',
            'turno_id'=>'required',
            'carnet_id'=>'required',
            'empleado_id'=>'required',
            'fecha'=>'required'
        ];

        $db = DB::connection('rrh');

        $db->beginTransaction();
            $this->validate($request,$rules);

            $data_d = new DetalleAsignacionEmpleado;
            $data_d->turno_id = $request->turno_id;
            $data_d->asignacion_empleado_id = $asignacion_empleado->id;
            $data_d->carnet_id = $request->carnet_id;
            $data_d->empleado_id = $request->empleado_id;
            $data_d->fecha = $request->fecha;

            $data_d->save();

            $carnet = Carnet::find($request->carnet_id);
            $carnet->asignado = true;
            $carnet->save();

        $db->commit();

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
