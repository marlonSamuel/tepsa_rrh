<?php

namespace App\Http\Controllers\Asignacion;

use App\Carnet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\DetalleAsignacionEmpleado;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class DetalleAsignacionEmpleadoController extends ApiController
{
    public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $detalle = DetalleAsignacionEmpleado::with('asignacion','turno','carnet','empleado')->get();
        return $this->showAll($detalle);
    }

    //retornar asignacion de empleado, codigo turno y fecha
    public function showAsign($codigo,$fecha,$turno_id){

        $carnet = Carnet::where('codigo',$codigo)->firstOrFail();

        $asignacion = DetalleAsignacionEmpleado::where([['carnet_id',$carnet->id],['fecha',$fecha],['turno_id',$turno_id]])->with('empleado','asistencia_turno.cargo_turno.cargo','asignacion.planificacion.buque','turno')->first();
        
        if(is_null($asignacion)){
            return $this->errorResponse('no se encontró asignación de este empleado con el codigo '.$codigo.' fecha y turno correspondiente', 421);
        }

        return $this->showOne($asignacion);
    }

    /**
     */
    public function destroy(DetalleAsignacionEmpleado $detalle_asignacion_empleado)
    {
        $detalle_asignacion_empleado->delete();
        return $this->showOne($detalle_asignacion_empleado,201,'delete');
    }
}
