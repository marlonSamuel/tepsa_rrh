<?php

namespace App\Http\Controllers\Asistencia;

use App\AsistenciaAlmuerzo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class AsistenciaAlmuerzoController extends ApiController
{
     public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $asistenciaAlmuerzos = AsistenciaAlmuerzo::all();
        return $this->showAll($asistenciaAlmuerzos);
    }


    /**
     */
    public function store(Request $request)
    {
        $rules = [
            'detalle_asignacion_empleado_id'=>'required|integer'
        ];

        $this->validate($request,$rules);
        $data = $request->all();

        $asistenciaAlmuerzo = AsistenciaAlmuerzo::create($data);

        return $this->showOne($asistenciaAlmuerzo,201,'insert');
    }

    /**
     */
    public function show(AsistenciaAlmuerzo $asistencia_almuerzo)
    {
        return $this->showOne($asistencia_almuerzo,200,'select');
    }

}
