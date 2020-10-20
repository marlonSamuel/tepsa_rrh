<?php

namespace App\Http\Controllers\Asignacion;

use Illuminate\Http\Request;
use App\DetalleAsignacionEmpleado;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class DetalleAsignacionEmpleadoController extends ApiController
{
    public function __construct()
    {
        #parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $detalle = DetalleAsignacionEmpleado::with('asignacion','turno','carnet','empleado')->get();
        return $this->showAll($detalle);
    }

    /**
     */
    public function destroy(DetalleAsignacionEmpleado $detalle_asignacion_empleado)
    {

        $detalle_asignacion_empleado->delete();
        return $this->showOne($detalle_asignacion_empleado,201,'delete');
    }
}
